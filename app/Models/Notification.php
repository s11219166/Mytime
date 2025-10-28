<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project associated with the notification
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get icon class based on notification type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'project_due' => 'fa-calendar-check',
            'project_overdue' => 'fa-exclamation-triangle',
            'project_reminder' => 'fa-bell',
            'time_reminder' => 'fa-clock',
            'project_completed' => 'fa-check-circle',
            'project_assigned' => 'fa-user-plus',
            default => 'fa-info-circle'
        };
    }

    /**
     * Get color class based on notification type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'project_due' => 'warning',
            'project_overdue' => 'danger',
            'project_reminder' => 'info',
            'time_reminder' => 'primary',
            'project_completed' => 'success',
            'project_assigned' => 'success',
            default => 'secondary'
        };
    }
}
