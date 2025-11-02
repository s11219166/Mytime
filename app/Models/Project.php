<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\App;
use App\Mail\ProjectDueReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'budget',
        'start_date',
        'end_date',
        'progress',
        'tags',
        'created_by',
        'course_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'tags' => 'array',
        'budget' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($project) {
            // Dispatch notification job instead of sending immediately
            // This will make project creation faster
            \Illuminate\Support\Facades\Artisan::queue('project:send-notification', [
                'project_id' => $project->id
            ]);
        });
    }

    /**
     * Send notification when project is created
     */
    public function sendNewProjectNotification()
    {
        // Get the notification service
        $notificationService = App::make('App\Services\NotificationService');
        $pushNotificationService = App::make('App\Services\PushNotificationService');

        // Notify project creator
        if ($this->creator) {
            $notificationService->notifyProjectAssignment($this, $this->creator);

            // Send push notification
            if (isset($this->creator->push_notifications) && $this->creator->push_notifications) {
                try {
                    $pushNotificationService->sendNewProjectNotification($this->creator, $this);
                    Log::info('Push notification sent for new project to creator: ' . $this->creator->id);
                } catch (\Exception $e) {
                    Log::error('Failed to send new project push notification: ' . $e->getMessage());
                }
            }

            // Send email notification
            if (isset($this->creator->email_notifications) && $this->creator->email_notifications) {
                try {
                    Mail::to($this->creator->email)
                        ->send(new ProjectDueReminderMail(
                            $this,
                            $this->creator,
                            0, // days remaining (0 for new project)
                            'new_project', // urgency level
                            'creation' // notification time
                        ));
                } catch (\Exception $e) {
                    Log::error('Failed to send new project email: ' . $e->getMessage());
                }
            }
        }

        // Notify all team members
        foreach ($this->teamMembers as $member) {
            if (!isset($member->project_updates) || $member->project_updates) {
                $notificationService->notifyProjectAssignment($this, $member);

                // Send push notification
                if (isset($member->push_notifications) && $member->push_notifications) {
                    try {
                        $pushNotificationService->sendNewProjectNotification($member, $this);
                        Log::info('Push notification sent for new project to team member: ' . $member->id);
                    } catch (\Exception $e) {
                        Log::error('Failed to send new project push notification to team member: ' . $e->getMessage());
                    }
                }

                // Send email notification
                if (isset($member->email_notifications) && $member->email_notifications) {
                    try {
                        Mail::to($member->email)
                            ->send(new ProjectDueReminderMail(
                                $this,
                                $member,
                                0, // days remaining (0 for new project)
                                'new_project', // urgency level
                                'creation' // notification time
                            ));
                    } catch (\Exception $e) {
                        Log::error('Failed to send new project email to team member: ' . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Get the user who created the project
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the team members assigned to this project
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'inprogress' => 'In Progress',
            'review_pending' => 'Review Pending',
            'revision_needed' => 'Revision Needed',
            'awaiting_input' => 'Awaiting Input',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-success',
            'inprogress' => 'bg-primary',
            'review_pending' => 'bg-warning',
            'revision_needed' => 'bg-warning',
            'awaiting_input' => 'bg-info',
            'paused' => 'bg-secondary',
            'overdue' => 'bg-danger',
            'completed' => 'bg-info',
            'cancelled' => 'bg-dark',
            'inactive' => 'bg-light text-dark',
            default => 'bg-secondary'
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-success',
            'medium' => 'bg-warning',
            'high' => 'bg-danger',
            'urgent' => 'bg-dark',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Get the computed status based on dates and progress
     */
    public function getCurrentStatusAttribute(): string
    {
        // If project is already marked as completed or cancelled, keep that status
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return $this->status;
        }

        // Check if project is overdue
        if ($this->is_overdue) {
            return 'overdue';
        }

        // If end date is today
        if ($this->end_date && $this->end_date->isToday()) {
            return 'due';
        }

        // If project has started but not completed
        if ($this->progress > 0 && $this->progress < 100) {
            return 'inprogress';
        }

        // Default to current status if none of the above conditions are met
        return $this->status;
    }

    /**
     * Get relative time status for display
     */
    public function getTimeStatusAttribute(): string
    {
        if (!$this->end_date) {
            return 'No deadline set';
        }

        if ($this->end_date->isPast()) {
            return 'Overdue by ' . $this->end_date->diffForHumans(now(), ['parts' => 1]);
        }

        if ($this->end_date->isToday()) {
            return 'Due today';
        }

        if ($this->end_date->isTomorrow()) {
            return 'Due tomorrow';
        }

        if ($this->end_date->diffInDays() <= 7) {
            return 'Due ' . $this->end_date->diffForHumans(now(), ['parts' => 1]);
        }

        return 'Due on ' . $this->end_date->format('M d, Y');
    }
}
