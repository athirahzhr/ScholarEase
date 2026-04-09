<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Scholarship;

class ScholarshipDeadlineNear extends Notification
{
    use Queueable;

    public function __construct(
        protected Scholarship $scholarship,
        protected int $daysLeft
    ) {}

    public function via($notifiable)
    {
        return ['mail']; // 📧 EMAIL
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⏰ Scholarship Deadline Approaching')
            ->greeting('Hi ' . $notifiable->name)
            ->line('A scholarship you bookmarked is closing soon!')
            ->line('🎓 Scholarship: ' . $this->scholarship->title)
            ->line('⏳ Days left: ' . $this->daysLeft . ' days')
            ->action(
                'View Scholarship',
                route('scholarships.show', $this->scholarship->id)
            )
            ->line('Good luck with your application!');
    }
}
