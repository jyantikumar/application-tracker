<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all job applications for the user.
     *
     * @return HasMany<JobApplication>
     */
    public function JobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
    public function sources(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(ApplicationSource::class);
}

public function statuses(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(ApplicationStatus::class);
}
protected static function booted(): void
{
    static::created(function (User $User) {
        $DefaultSources = [
            'LinkedIn', 'Indeed', 'JobStreet', 'OnlineJobs.ph (OLJ)',
            'Referral', 'Company Website', 'Direct',
        ];
        foreach ($DefaultSources as $Name) {
            $User->sources()->create(['name' => $Name]);
        }

        $DefaultStatuses = [
            'Applied', 'Under Review', 'Currently Interviewing',
            'Waiting response', 'Offered', 'Hired', 'Rejected', 'No Response',
        ];
        foreach ($DefaultStatuses as $Name) {
            $User->statuses()->create(['name' => $Name]);
        }
    });
}
}