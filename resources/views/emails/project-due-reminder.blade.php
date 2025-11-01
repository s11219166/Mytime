<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header.critical {
            background: linear-gradient(135deg, #dc3545 0%, #b71c1c 100%);
            animation: pulse 2s infinite;
        }
        .header.high {
            background: linear-gradient(135deg, #ff6b6b 0%, #dc3545 100%);
        }
        .header.overdue {
            background: linear-gradient(135deg, #721c24 0%, #dc3545 100%);
            animation: pulse 2s infinite;
        }
        .header.moderate {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }
        .header.new_project {
            background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.85; }
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .project-info {
            background: #f8f9fa;
            border-left: 4px solid #32CD32;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .project-info.critical {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .project-info.high {
            border-left-color: #ff6b6b;
            background: #fff8f8;
        }
        .project-info.overdue {
            border-left-color: #721c24;
            background: #ffe6e6;
        }
        .project-info.moderate {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        .project-info.new_project {
            border-left-color: #17a2b8;
            background: #f0f8ff;
        }
        .project-info h3 {
            margin-top: 0;
            color: #32CD32;
        }
        .project-info.critical h3 {
            color: #dc3545;
        }
        .project-info.high h3 {
            color: #ff6b6b;
        }
        .project-info.overdue h3 {
            color: #721c24;
        }
        .project-info.moderate h3 {
            color: #ffc107;
        }
        .project-info.new_project h3 {
            color: #17a2b8;
        }
        .priority-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-badge.urgent {
            background: #dc3545;
            color: white;
        }
        .priority-badge.high {
            background: #fd7e14;
            color: white;
        }
        .priority-badge.medium {
            background: #ffc107;
            color: #333;
        }
        .priority-badge.low {
            background: #28a745;
            color: white;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert.danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .alert.info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .progress-bar {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        @php
            $headerClass = '';
            if ($urgencyLevel === 'critical') {
                $headerClass = 'critical';
            } elseif ($urgencyLevel === 'high') {
                $headerClass = 'high';
            } elseif ($urgencyLevel === 'overdue') {
                $headerClass = 'overdue';
            } elseif ($daysRemaining == 2) {
                $headerClass = 'moderate';
            } elseif ($urgencyLevel === 'new_project') {
                $headerClass = 'new_project';
            }
        @endphp

        <div class="header {{ $headerClass }}">
            @if($urgencyLevel === 'new_project')
                <h1>🎉 New Project Assignment</h1>
                <p style="margin: 10px 0 0 0; font-size: 16px; font-weight: bold;">You've been assigned to a new project!</p>
            @else
                <h1>🔔 Project Due Date Reminder</h1>
                @if($urgencyLevel === 'critical')
                    <p style="margin: 10px 0 0 0; font-size: 16px; font-weight: bold;">🔴 CRITICAL ALERT - DUE TODAY! 🔴</p>
                @elseif($urgencyLevel === 'high')
                    <p style="margin: 10px 0 0 0; font-size: 16px; font-weight: bold;">🚨 HIGH PRIORITY - DUE TOMORROW! 🚨</p>
                @elseif($urgencyLevel === 'overdue')
                    <p style="margin: 10px 0 0 0; font-size: 16px; font-weight: bold;">❌ PROJECT OVERDUE! ❌</p>
                @elseif($daysRemaining == 3 && $notificationTime === 'evening')
                    <p style="margin: 10px 0 0 0; font-size: 14px;">🌙 Evening Check-in Reminder</p>
                @endif
            @endif
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            @if($urgencyLevel === 'new_project')
                <div class="alert info">
                    <strong>🎉 New Project Assignment:</strong> You have been assigned to the project "<strong>{{ $project->name }}</strong>".
                    <br><strong>Please review the project details and start working on assigned tasks.</strong>
                </div>
            @elseif($daysRemaining == 3)
                <div class="alert">
                    <strong>📅 {{ $notificationTime === 'morning' ? 'Morning' : 'Evening' }} Reminder:</strong> The project "<strong>{{ $project->name }}</strong>" is due in <strong>3 days</strong>.
                    @if($notificationTime === 'morning')
                        <br>Start your day by reviewing the project progress and prioritizing tasks.
                    @else
                        <br>As the day ends, ensure all tasks are on track and address any blockers.
                    @endif
                </div>
            @elseif($daysRemaining == 2)
                <div class="alert">
                    <strong>⚠️ Moderate Alert:</strong> The project "<strong>{{ $project->name }}</strong>" is due in <strong>2 days</strong>.
                    <br><strong>Please prioritize remaining tasks and ensure timely completion.</strong>
                </div>
            @elseif($daysRemaining == 1)
                <div class="alert danger">
                    <strong>🚨 HIGH ALERT:</strong> The project "<strong>{{ $project->name }}</strong>" is due <strong>TOMORROW</strong>!
                    <br><strong style="font-size: 16px;">IMMEDIATE ATTENTION REQUIRED! Complete all remaining tasks today.</strong>
                </div>
            @elseif($daysRemaining == 0)
                <div class="alert danger">
                    <strong>🔴 CRITICAL ALERT:</strong> The project "<strong>{{ $project->name }}</strong>" is due <strong>TODAY</strong>!
                    <br><strong style="font-size: 16px;">URGENT: Complete all tasks immediately. Final deadline is today!</strong>
                </div>
            @else
                <div class="alert danger">
                    <strong>❌ OVERDUE:</strong> The project "<strong>{{ $project->name }}</strong>" was due <strong>{{ abs($daysRemaining) }} {{ Str::plural('day', abs($daysRemaining)) }} ago</strong>!
                    <br><strong style="font-size: 16px;">CRITICAL: This project is overdue. Take immediate action!</strong>
                </div>
            @endif

            <div class="project-info {{ $urgencyLevel }}">
                <h3>📋 Project Details</h3>

                <div class="info-row">
                    <span class="label">Project Name:</span>
                    <span class="value">{{ $project->name }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Priority:</span>
                    <span class="value">
                        <span class="priority-badge {{ $project->priority }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">Start Date:</span>
                    <span class="value">{{ $project->start_date->format('M d, Y') }}</span>
                </div>

                @if($project->end_date)
                <div class="info-row">
                    <span class="label">Due Date:</span>
                    <span class="value">{{ $project->end_date->format('M d, Y') }}</span>
                </div>
                @endif

                <div class="info-row">
                    <span class="label">Progress:</span>
                    <span class="value">{{ $project->progress }}%</span>
                </div>
            </div>

            <div>
                <strong>Progress:</strong>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $project->progress }}%">
                        {{ $project->progress }}%
                    </div>
                </div>
            </div>

            @if($project->description)
                <div style="margin: 20px 0;">
                    <strong>Description:</strong>
                    <p style="color: #666;">{{ $project->description }}</p>
                </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ url('/projects/' . $project->id) }}" class="button">
                    View Project Details
                </a>
            </div>

            <p style="margin-top: 30px; color: #666;">
                @if($urgencyLevel === 'new_project')
                    This is an automated notification from MyTime Project Management System.
                    <br>You've been assigned to this project. Please review the details and start working on assigned tasks.
                @else
                    This is an automated reminder from MyTime Project Management System.
                    @if($daysRemaining > 0)
                        Please ensure all tasks are completed before the due date.
                    @else
                        Please update the project status or extend the deadline if needed.
                    @endif
                @endif
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} MyTime - Project Management System</p>
            <p>You received this email because you are assigned to this project.</p>
        </div>
    </div>
</body>
</html>
