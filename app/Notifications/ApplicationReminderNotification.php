<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Reminder $reminder) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reminder = $this->reminder->loadMissing('application');

        return (new MailMessage)
            ->subject($reminder->title.' — Job Tracker')
            ->greeting('Hi '.$notifiable->name.',')
            ->line($reminder->body)
            ->action('View applications', route('job-applications.applications'))
            ->line('You can dismiss or snooze this reminder from the app.');
    }

    public function toArray(object $notifiable): array
    {
        $reminder = $this->reminder->loadMissing('application');

        return [
            'reminder_id' => $reminder->id,
            'type' => $reminder->type,
            'title' => $reminder->title,
            'body' => $reminder->body,
            'application_id' => $reminder->application_id,
            'company_name' => $reminder->application?->company_name,
            'position' => $reminder->application?->position,
            'scheduled_for' => $reminder->scheduled_for?->toIso8601String(),
        ];
    }
}
