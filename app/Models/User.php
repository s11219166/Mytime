<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'first_name',
        'last_name',
        'phone',
        'department',
        'position',
        'bio',
        'timezone',
        'date_format',
        'time_format',
        'working_hours',
        'email_notifications',
        'project_updates',
        'time_reminders',
        'weekly_reports',
        'profile_photo_path',
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
            'email_notifications' => 'boolean',
            'project_updates' => 'boolean',
            'time_reminders' => 'boolean',
            'weekly_reports' => 'boolean',
            'working_hours' => 'integer',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get projects created by this user
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Get projects this user is assigned to
     */
    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get all projects (created + assigned) - Helper method
     * Note: This returns a query builder, not a relationship
     */
    public function getAllProjects()
    {
        return Project::where('created_by', $this->id)
            ->orWhereHas('teamMembers', function($query) {
                $query->where('user_id', $this->id);
            });
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get courses created by this user
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get user's time entries
     */
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}
