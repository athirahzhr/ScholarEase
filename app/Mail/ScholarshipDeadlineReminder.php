<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ScholarshipDeadlineReminder extends Mailable
{
    public $user;
    public $scholarship;
    public $daysLeft;

    public function __construct($user, $scholarship, $daysLeft)
    {
        $this->user = $user;
        $this->scholarship = $scholarship;
        $this->daysLeft = $daysLeft;
    }

    public function build()
    {
        return $this->subject('⏰ Scholarship Deadline Reminder')
                    ->view('emails.scholarship-reminder');
    }
}
