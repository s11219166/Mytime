<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ProjectDueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $user;
    public $daysRemaining;
    public $urgencyLevel;
    public $notificationTime;

    /**
     * Create a new message instance.
     */
    public function __construct(Project $project, User $user, int $daysRemaining, string $urgencyLevel = 'normal', ?string $notificationTime = null)
    {
        $this->project = $project;
        $this->user = $user;
        $this->daysRemaining = $daysRemaining;
        $this->urgencyLevel = $urgencyLevel;
        $this->notificationTime = $notificationTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Create urgency-based subject line
        $subjectPrefix = '';
        if ($this->urgencyLevel === 'critical') {
            $subjectPrefix = '🔴 CRITICAL: ';
        } elseif ($this->urgencyLevel === 'high') {
            $subjectPrefix = '🚨 URGENT: ';
        } elseif ($this->urgencyLevel === 'overdue') {
            $subjectPrefix = '❌ OVERDUE: ';
        } elseif ($this->daysRemaining == 2) {
            $subjectPrefix = '⚠️ Alert: ';
        } elseif ($this->daysRemaining == 3 && $this->notificationTime === 'evening') {
            $subjectPrefix = '🌙 Evening Reminder: ';
        }

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@mytime.com'),
                config('mail.from.name', 'MyTime')
            ),
            subject: $subjectPrefix . 'Project Due Reminder: ' . $this->project->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-due-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
