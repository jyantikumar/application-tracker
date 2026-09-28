<?php

namespace App\Http\Controllers;

use App\Http\Requests\SnoozeReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReminderController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $filter = $request->string('filter', 'all')->toString();

        $query = Reminder::query()
            ->with([
                'application:id,company_name,position,status,city,source,user_id',
                'interview:id,job_application_id,interview_type,scheduled_at,status,notes',
            ])
            ->whereHas('application', fn ($q) => $q->where('user_id', $userId));

        match ($filter) {
            'active' => $query->active(),
            'pending' => $query->pending(),
            'scheduled' => $query
                ->where('is_dismissed', false)
                ->whereNull('sent_at')
                ->where('scheduled_for', '>', now()),
            'dismissed' => $query->where('is_dismissed', true),
            default => null,
        };

        $reminders = $query
            ->orderByRaw("CASE WHEN is_dismissed = 0 AND sent_at IS NOT NULL THEN 0 WHEN is_dismissed = 0 AND sent_at IS NULL THEN 1 ELSE 2 END")
            ->orderByDesc('scheduled_for')
            ->get();

        $counts = [
            'all' => Reminder::query()
                ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
                ->count(),
            'active' => Reminder::query()
                ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
                ->active()
                ->count(),
            'pending' => Reminder::query()
                ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
                ->pending()
                ->count(),
            'scheduled' => Reminder::query()
                ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
                ->where('is_dismissed', false)
                ->whereNull('sent_at')
                ->where('scheduled_for', '>', now())
                ->count(),
            'dismissed' => Reminder::query()
                ->whereHas('application', fn ($q) => $q->where('user_id', $userId))
                ->where('is_dismissed', true)
                ->count(),
        ];

        return Inertia::render('Notifications/Index', [
            'Reminders' => $reminders,
            'Filter' => $filter,
            'Counts' => $counts,
        ]);
    }

    public function dismiss(Reminder $reminder): RedirectResponse
    {
        $this->authorizeReminder($reminder);

        $reminder->update(['is_dismissed' => true]);

        return redirect()->back()->with('success', 'Reminder dismissed.');
    }

    public function snooze(SnoozeReminderRequest $request, Reminder $reminder): RedirectResponse
    {
        $hours = (int) ($request->validated('hours') ?? 24);

        $reminder->update([
            'is_dismissed' => false,
            'sent_at' => null,
            'scheduled_for' => now()->addHours($hours),
        ]);

        return redirect()->back()->with(
            'success',
            "Reminder snoozed for {$hours} hour".($hours === 1 ? '' : 's').'.'
        );
    }

    public function dismissAll(Request $request): RedirectResponse
    {
        Reminder::query()
            ->whereHas('application', fn ($q) => $q->where('user_id', $request->user()->id))
            ->active()
            ->update(['is_dismissed' => true]);

        return redirect()->back()->with('success', 'All reminders dismissed.');
    }

    protected function authorizeReminder(Reminder $reminder): void
    {
        $reminder->loadMissing('application');

        abort_if(
            ! $reminder->application || $reminder->application->user_id !== auth()->id(),
            403
        );
    }
}
