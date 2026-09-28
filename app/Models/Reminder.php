<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    public const TYPE_FOLLOW_UP = 'follow_up';
    public const TYPE_INTERVIEW = 'interview';
    public const TYPE_STALE_APP = 'stale_app';

    protected $fillable = [
        'application_id',
        'interview_id',
        'type',
        'lead_hours',
        'scheduled_for',
        'sent_at',
        'is_dismissed',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'is_dismissed' => 'boolean',
        'lead_hours' => 'integer',
    ];

    protected $appends = [
        'title',
        'body',
        'status_label',
        'type_label',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_dismissed', false)
            ->whereNotNull('sent_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query
            ->where('is_dismissed', false)
            ->whereNull('sent_at')
            ->where('scheduled_for', '<=', now());
    }

    public function getTitleAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_FOLLOW_UP => 'Follow-up nudge',
            self::TYPE_INTERVIEW => $this->lead_hours === 1
                ? 'Interview in 1 hour'
                : 'Interview tomorrow',
            self::TYPE_STALE_APP => 'Stale application',
            default => 'Reminder',
        };
    }

    public function getBodyAttribute(): string
    {
        $app = $this->relationLoaded('application') ? $this->application : null;
        $company = $app?->company_name ?? 'this role';
        $position = $app?->position ?? 'application';

        return match ($this->type) {
            self::TYPE_FOLLOW_UP => "It's been a week since you applied to {$company} ({$position}). Consider sending a follow-up.",
            self::TYPE_INTERVIEW => $this->lead_hours === 1
                ? "Your interview for {$position} at {$company} starts in about an hour."
                : "You have an interview for {$position} at {$company} in about 24 hours.",
            self::TYPE_STALE_APP => "No updates on {$company} ({$position}) in 14+ days. Review or archive it.",
            default => 'You have an application reminder.',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_dismissed) {
            return 'dismissed';
        }

        if ($this->sent_at) {
            return 'active';
        }

        if ($this->scheduled_for && $this->scheduled_for->isFuture()) {
            return 'scheduled';
        }

        return 'pending';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_FOLLOW_UP => 'Follow-up',
            self::TYPE_INTERVIEW => 'Interview alert',
            self::TYPE_STALE_APP => 'Stale application',
            default => ucfirst(str_replace('_', ' ', (string) $this->type)),
        };
    }
}
