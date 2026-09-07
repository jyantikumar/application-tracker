<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'salary',
        'salary_min',
        'salary_max',
        'currency',
        'pay_rate_type',
        'hours_per_week',
        'job_type',
        'description',
        'pros',
        'cons',
        'city',
        'ApplicationURL',
        'source',
        'status',
    ];

    protected $casts = [
        'salary'         => 'float',
        'salary_min'     => 'float',
        'salary_max'     => 'float',
        'hours_per_week' => 'float',
    ];

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Formatted salary string display helper
    public function getFormattedSalaryAttribute(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return $this->currency . ' ' . number_format($this->salary_min, 2) . ' - ' . number_format($this->salary_max, 2);
        }

        return $this->currency . ' ' . number_format($this->salary ?: 0, 2);
    }

    // Accessor to compute earnings across different standard timeframes
    public function getCalculatedEarningsAttribute(): array
    {
        $BaseSalary   = $this->salary ?: (($this->salary_min + $this->salary_max) / 2);
        $HoursPerWeek = $this->hours_per_week ?: 40;
        $HoursPerDay  = $HoursPerWeek / 5;

        if ($this->pay_rate_type === 'Hourly') {
            $Hourly  = $BaseSalary;
            $Daily   = $Hourly * $HoursPerDay;
            $Weekly  = $Hourly * $HoursPerWeek;
            $Monthly = ($Weekly * 52) / 12;
            $Annual  = $Weekly * 52;
        } else {
            // Monthly base calculation
            $Monthly = $BaseSalary;
            $Annual  = $Monthly * 12;
            $Weekly  = $Annual / 52;
            $Daily   = $Weekly / 5;
            $Hourly  = $HoursPerWeek > 0 ? $Weekly / $HoursPerWeek : 0;
        }

        return [
            'Hourly'  => round($Hourly, 2),
            'Daily'   => round($Daily, 2),
            'Weekly'  => round($Weekly, 2),
            'Monthly' => round($Monthly, 2),
            'Annual'  => round($Annual, 2),
        ];
    }

    public function Interviews(): HasMany
    {
        return $this->hasMany(Interview::class)->orderBy('scheduled_at', 'ASC');
    }

    // Helper property to check if at least one interview was completed
    public function getHasBeenInterviewedAttribute(): bool
    {
        return $this->Interviews()->where('status', 'Completed')->exists();
    }
}