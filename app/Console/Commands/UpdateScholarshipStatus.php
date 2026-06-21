<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scholarship;
use Carbon\Carbon;

class UpdateScholarshipStatus extends Command
{
    protected $signature = 'scholarships:update-status';

    protected $description =
        'Update scholarship active/expired status based on deadline';

    public function handle()
    {
        $updated = 0;

        Scholarship::whereNotNull('deadline')
            ->get()
            ->each(function ($scholarship) use (&$updated) {

                $shouldBeActive =
                    Carbon::parse($scholarship->deadline)
                        ->isFuture();

                if (
                    $scholarship->is_active !==
                    $shouldBeActive
                ) {

                    $scholarship->update([
                        'is_active' => $shouldBeActive
                    ]);

                    $updated++;
                }
            });

        $this->info(
            "Updated {$updated} scholarship statuses."
        );

        return Command::SUCCESS;
    }
}