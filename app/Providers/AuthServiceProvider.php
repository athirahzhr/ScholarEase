<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {

            return (new MailMessage)
                ->subject('🎓 Verify Your ScholarEase Account')
                ->greeting('Welcome to ScholarEase!')
                ->line('Thank you for joining ScholarEase, your intelligent scholarship recommendation platform.')
                ->line('To activate your account and start discovering scholarships tailored to your academic profile, please verify your email address.')
                ->action('Verify My Email', $url)
                ->line('Once verified, you will be able to:')
                ->line('• Receive personalized scholarship recommendations')
                ->line('• Bookmark scholarships and track deadlines')
                ->line('• Receive scholarship deadline reminder notifications')
                ->line('• Access all ScholarEase features securely')
                ->line('If you did not create this account, no further action is required.')
                ->salutation("Best Regards,\nScholarEase Team");
        });
    }
}