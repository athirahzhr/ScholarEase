<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bookmark;
use Carbon\Carbon;
use App\Notifications\ScholarshipDeadlineNear;

class SendDeadlineNotifications extends Command
{
    protected $signature = 'notify:scholarship-deadline';
    protected $description = 'Send email notification for bookmarked scholarships nearing deadline';

    public function handle()
    {
        $today = Carbon::today();
        $threshold = $today->copy()->addDays(3); // 🔥 3 hari sebelum deadline

        $bookmarks = Bookmark::with(['user', 'scholarship'])
            ->whereNull('notified_at')
            ->whereHas('scholarship', function ($q) use ($today, $threshold) {
                $q->whereDate('deadline', '>=', $today)
                  ->whereDate('deadline', '<=', $threshold);
            })
            ->get();

        foreach ($bookmarks as $bookmark) {
            $scholarship = $bookmark->scholarship;

            if (!$scholarship || !$bookmark->user) {
                continue;
            }

            $daysLeft = $today->diffInDays(
                Carbon::parse($scholarship->deadline)
            );

            $bookmark->user->notify(
                new ScholarshipDeadlineNear($scholarship, $daysLeft)
            );

            // 🔒 MARK AS SENT
            $bookmark->update([
                'notified_at' => now()
            ]);

            $this->info(
                "Email sent to {$bookmark->user->email} for {$scholarship->title}"
            );
        }

        return Command::SUCCESS;
    }
}
