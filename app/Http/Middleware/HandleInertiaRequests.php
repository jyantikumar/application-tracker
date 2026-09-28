<?php

namespace App\Http\Middleware;

use App\Models\ApplicationSource;
use App\Models\ApplicationStatus;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user
                    ? $user->only(['id', 'name', 'email', 'email_verified_at'])
                    : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'applicationSources' => fn () => $user
                ? ApplicationSource::where('user_id', $user->id)->orderBy('name')->pluck('name')
                : [],
            'applicationStatuses' => fn () => $user
                ? ApplicationStatus::where('user_id', $user->id)->orderBy('name')->pluck('name')
                : [],
            'activeReminders' => fn () => $user
                ? Reminder::query()
                    ->with(['application:id,company_name,position,user_id'])
                    ->whereHas('application', fn ($q) => $q->where('user_id', $user->id))
                    ->active()
                    ->latest('sent_at')
                    ->limit(20)
                    ->get()
                : [],
        ];
    }
}
