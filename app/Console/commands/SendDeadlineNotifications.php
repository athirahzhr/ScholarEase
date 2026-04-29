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
        $threshold = $today->copy()->addDays(3);

        $bookmarks = Bookmark::with(['user', 'scholarship'])
            ->whereNull('notified_at')
            ->whereHas('scholarship', function ($q) use ($today, $threshold) {
                $q->whereDate('deadline', '>=', $today)
                  ->whereDate('deadline', '<=', $threshold);
            })
            ->get();

        $count = 0; 

        foreach ($bookmarks as $bookmark) {

            $scholarship = $bookmark->scholarship;
            $user = $bookmark->user;

            if (!$scholarship || !$user) {
                continue;
            }

            $daysLeft = now()->diffInDays($scholarship->deadline, false);

            // Prevent duplicate (extra safety)
            $updated = Bookmark::where('id', $bookmark->id)
                ->whereNull('notified_at')
                ->update([
                    'notified_at' => now()
                ]);

            if ($updated) {
                $user->notify(
                    new ScholarshipDeadlineNear($scholarship, $daysLeft)
                );

                $count++;

                $this->info(
                    "[SUCCESS] {$user->email} → {$scholarship->title} ({$daysLeft} days left)"
                );
            }
        }

       
        $this->info("Total notifications sent: {$count}");

        return Command::SUCCESS;
    }
}