<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewScoreNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
   public $grade;

    public function __construct($grade)
    {
        $this->grade = $grade;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

   


    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
{
     return (new MailMessage)
            ->line('A new score has been inserted for an activity.')
            ->line('Subject: ' . $this->grade->enrolledStudent->subject->description)
            ->line('Activity: ' . $this->grade->assessment->description)
            ->line('Points: ' . $this->grade->points);
    }
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
