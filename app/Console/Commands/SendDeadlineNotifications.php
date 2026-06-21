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
                  ->whereDate('deadline', $today->copy()->addDays(3));
            })
            ->get();

        $count = 0;

        foreach ($bookmarks as $bookmark) {

            $scholarship = $bookmark->scholarship;
            $user = $bookmark->user;

            if (!$scholarship || !$user) {
                continue;
            }
            $daysLeft = today()->diffInDays($scholarship->deadline, false);

            try {

                // Send notification email
                $user->notify(
                    new ScholarshipDeadlineNear(
                        $scholarship,
                        $daysLeft
                    )
                );

                // Mark as sent
                $bookmark->update([
                    'notified_at' => now(),
                    'notification_status' => 'success',
                    'notification_error' => null,
                ]);

                $count++;

                $this->info(
                    "[SUCCESS] {$user->email} → {$scholarship->title} ({$daysLeft} days left)"
                );

            } catch (\Exception $e) {

                $bookmark->update([
                    'notification_status' => 'failed',
                    'notification_error' => $e->getMessage(),
                ]);

                $this->error(
                    "[FAILED] {$user->email} → {$e->getMessage()}"
                );
            }
        }

        $this->info("Total notifications sent: {$count}");

        return Command::SUCCESS;
    }
}