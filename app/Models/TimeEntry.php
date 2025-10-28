<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_description',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the user who owns the time entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project associated with the time entry
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) {
            if ($this->end_time) {
                $duration = $this->start_time->diffInMinutes($this->end_time);
            } else {
                $duration = $this->start_time->diffInMinutes(now());
            }
        } else {
            $duration = $this->duration_minutes;
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return sprintf("%dh %dm", $hours, $minutes);
        }

        return sprintf("%dm", $minutes);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'running' => 'bg-primary',
            'paused' => 'bg-warning',
            'completed' => 'bg-success',
            default => 'bg-secondary'
        };
    }
}
