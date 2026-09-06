<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'position',
        'salary',
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
        'hours_per_week' => 'float',
    ];

    // Accessor to compute earnings across different standard timeframes
    public function getCalculatedEarningsAttribute(): array
    {
        $BaseSalary   = $this->salary;
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
}