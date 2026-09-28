<?php

namespace App\Console\Commands;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\Reminder;
use App\Notifications\ApplicationReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessApplicationReminders extends Command
{
    protected $signature = 'reminders:process';

    protected $description = 'Generate and dispatch follow-up, interview, and stale application reminders';

    public function handle(): int
    {
        $created = 0;
        $sent = 0;

        $created += $this->createFollowUpReminders();
        $created += $this->createInterviewReminders();
        $created += $this->createStaleReminders();
        $sent += $this->dispatchDueReminders();

        $this->info("Created {$created} reminder(s); sent {$sent} notification(s).");

        return self::SUCCESS;
    }

    protected function createFollowUpReminders(): int
    {
        $cutoff = now()->subDays(7);
        $created = 0;

        $applications = JobApplication::query()
            ->where('created_at', '<=', $cutoff)
            ->whereRaw('LOWER(status) = ?', ['applied'])
            ->whereDoesntHave('reminders', function ($query) {
                $query->where('type', Reminder::TYPE_FOLLOW_UP)
                    ->where('is_dismissed', false)
                    ->where(function ($inner) {
                        $inner->whereNull('sent_at')
                            ->orWhere('sent_at', '>=', now()->subDays(7));
                    });
            })
            ->get();

        foreach ($applications as $application) {
            Reminder::create([
                'application_id' => $application->id,
                'type' => Reminder::TYPE_FOLLOW_UP,
                'scheduled_for' => now(),
            ]);
            $created++;
        }

        return $created;
    }

    protected function createInterviewReminders(): int
    {
        $created = 0;

        $interviews = Interview::query()
            ->where('status', 'Scheduled')
            ->where('scheduled_at', '>', now())
            ->get();

        foreach ($interviews as $interview) {
            foreach ([24, 1] as $hours) {
                $scheduledFor = Carbon::parse($interview->scheduled_at)->subHours($hours);

                // Skip windows that already passed by more than 2 hours.
                if ($scheduledFor->lt(now()->subHours(2))) {
                    continue;
                }

                $exists = Reminder::query()
                    ->where('interview_id', $interview->id)
                    ->where('type', Reminder::TYPE_INTERVIEW)
                    ->where('lead_hours', $hours)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Reminder::create([
                    'application_id' => $interview->job_application_id,
                    'interview_id' => $interview->id,
                    'type' => Reminder::TYPE_INTERVIEW,
                    'lead_hours' => $hours,
                    'scheduled_for' => $scheduledFor->isPast() ? now() : $scheduledFor,
                ]);
                $created++;
            }
        }

        return $created;
    }

    protected function createStaleReminders(): int
    {
        $cutoff = now()->subDays(14);
        $created = 0;

        $applications = JobApplication::query()
            ->where('updated_at', '<=', $cutoff)
            ->whereNotIn('status', ['Hired', 'Rejected', 'Offered', 'No Response'])
            ->whereDoesntHave('reminders', function ($query) {
                $query->where('type', Reminder::TYPE_STALE_APP)
                    ->where('is_dismissed', false)
                    ->where(function ($inner) {
                        $inner->whereNull('sent_at')
                            ->orWhere('sent_at', '>=', now()->subDays(14));
                    });
            })
            ->whereDoesntHave('Interviews', function ($query) use ($cutoff) {
                $query->where('updated_at', '>', $cutoff);
            })
            ->get();

        foreach ($applications as $application) {
            Reminder::create([
                'application_id' => $application->id,
                'type' => Reminder::TYPE_STALE_APP,
                'scheduled_for' => now(),
            ]);
            $created++;
        }

        return $created;
    }

    protected function dispatchDueReminders(): int
    {
        $sent = 0;

        $reminders = Reminder::query()
            ->with(['application.User'])
            ->pending()
            ->get();

        foreach ($reminders as $reminder) {
            $user = $reminder->application?->User;

            if (! $user) {
                continue;
            }

            $user->notify(new ApplicationReminderNotification($reminder));
            $reminder->update(['sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }
}
