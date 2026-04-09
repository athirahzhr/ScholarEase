<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Scholarship;
use App\Observers\ScholarshipObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scholarship::observe(ScholarshipObserver::class);
    }
}
