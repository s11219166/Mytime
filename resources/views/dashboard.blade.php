@extends('layouts.app')

@section('title', 'Dashboard - MyTime')

@push('styles')
<style>
    .session-timer {
        background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
        border-radius: 10px;
        padding: 2rem;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .session-time {
        font-size: 2.5rem;
        font-weight: 600;
        text-align: center;
        margin: 1rem 0;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }
    .text-purple { color: #6f42c1 !important; }
    .btn-purple { background-color: #6f42c1; color: white; }
    .btn-purple:hover { background-color: #5a32a3; color: white; }
    .border-purple { border-color: #6f42c1 !important; }

    .text-orange { color: #fd7e14 !important; }
    .btn-orange { background-color: #fd7e14; color: white; }
    .btn-orange:hover { background-color: #dc6a10; color: white; }
    .border-orange { border-color: #fd7e14 !important; }

    .text-teal { color: #20c997 !important; }
    .btn-teal { background-color: #20c997; color: white; }
    .btn-teal:hover { background-color: #1ba37f; color: white; }
    .border-teal { border-color: #20c997 !important; }

    .text-pink { color: #e83e8c !important; }
    .btn-pink { background-color: #e83e8c; color: white; }
    .btn-pink:hover { background-color: #d4257d; color: white; }
    .border-pink { border-color: #e83e8c !important; }

    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8 col-lg-9">
            <h1 class="h3 mb-0">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-muted">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="col-md-4 col-lg-3 text-md-end">
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>End Session & Logout
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="session-timer mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Current Session</h5>
                    <span class="status-badge bg-light text-dark">
                        <i class="fas fa-circle text-success me-1"></i>Active
                    </span>
                </div>
                <div class="session-time" id="sessionTimer">{{ $sessionTime ?? '00:00:00' }}</div>
                <div class="row text-center">
                    <div class="col">
                        <h5 class="mb-1">Session Start</h5>
                        <p class="mb-0">{{ $sessionStart ?? now()->format('h:i A') }}</p>
                    </div>
                    <div class="col">
                        <h5 class="mb-1">Last Activity</h5>
                        <p class="mb-0" id="lastActivity">Just now</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center">
                            <div class="display-4 text-primary mb-3">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h5 class="card-title">Projects</h5>
                            <p class="card-text">Manage your active projects and tasks</p>
                            <a href="{{ route('projects.index') }}" class="btn btn-primary">View Projects</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-info">
                        <div class="card-body text-center">
                            <div class="display-4 text-info mb-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title">Analytics</h5>
                            <p class="card-text">View detailed performance reports</p>
                            <a href="{{ route('analytics') }}" class="btn btn-info">View Analytics</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-warning">
                        <div class="card-body text-center">
                            <div class="display-4 text-warning mb-3">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h5 class="card-title">Financial</h5>
                            <p class="card-text">Manage your finances and transactions</p>
                            <a href="{{ route('financial.index') }}" class="btn btn-warning">View Financial</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-secondary">
                        <div class="card-body text-center">
                            <div class="display-4 text-secondary mb-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title">Time Logs</h5>
                            <p class="card-text">Review your time entries and sessions</p>
                            <a href="#" class="btn btn-secondary">View Time Logs</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-danger">
                        <div class="card-body text-center">
                            <div class="display-4 text-danger mb-3">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <h5 class="card-title">Profile</h5>
                            <p class="card-text">Manage your account settings</p>
                            <a href="{{ route('profile') }}" class="btn btn-danger">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Today's Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total Sessions</span>
                        <span class="badge bg-primary">{{ $todayStats['sessions'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total Time</span>
                        <span class="badge bg-success">{{ $todayStats['total_time'] ?? '0h 0m' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Avg. Session Length</span>
                        <span class="badge bg-info">{{ $todayStats['avg_session'] ?? '0m' }}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="fas fa-coffee me-1"></i>Start Break
                        </button>
                        <a href="{{ route('projects.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i>New Project
                        </a>
                        <a href="{{ route('financial.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-wallet me-1"></i>Financial Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Activity Feed</h5>
                    <small class="text-muted">Today</small>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success rounded-circle p-2 me-3">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div>
                                    <p class="mb-0">Started work session</p>
                                    <small class="text-muted">{{ now()->format('h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-purple">
                        <div class="card-body text-center">
                            <div class="display-4 text-purple mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5 class="card-title">Calendar</h5>
                            <p class="card-text">View your schedule and deadlines</p>
                            <a href="#" class="btn btn-purple">Open Calendar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-orange">
                        <div class="card-body text-center">
                            <div class="display-4 text-orange mb-3">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h5 class="card-title">Reports</h5>
                            <p class="card-text">Generate and download reports</p>
                            <a href="#" class="btn btn-orange">View Reports</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-teal">
                        <div class="card-body text-center">
                            <div class="display-4 text-teal mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Team</h5>
                            <p class="card-text">Collaborate with team members</p>
                            <a href="#" class="btn btn-teal">View Team</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-pink">
                        <div class="card-body text-center">
                            <div class="display-4 text-pink mb-3">
                                <i class="fas fa-cog"></i>
                            </div>
                            <h5 class="card-title">Settings</h5>
                            <p class="card-text">Configure your preferences</p>
                            <a href="#" class="btn btn-pink">Open Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Sessions</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Current Session</h6>
                                    <p class="mb-0 text-muted small">Started at {{ $sessionStart }}</p>
                                </div>
                                <span class="badge bg-primary">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update session timer
    const sessionTimer = document.getElementById('sessionTimer');
    const lastActivity = document.getElementById('lastActivity');
    const startTime = new Date('{{ $currentSession->start_time }}').getTime();

    function updateSessionTime() {
        const now = new Date().getTime();
        const diff = Math.floor((now - startTime) / 1000);

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        sessionTimer.textContent =
            `${hours.toString().padStart(2, '0')}:` +
            `${minutes.toString().padStart(2, '0')}:` +
            `${seconds.toString().padStart(2, '0')}`;

        lastActivity.textContent = 'Just now';
    }

    setInterval(updateSessionTime, 1000);

    // Handle note form submission
    const noteForm = document.getElementById('noteForm');
    noteForm.addEventListener('submit', async function(e) {
    if (!taskDescription.value) {
        alert('Please enter a task description');
        return;
    }

    try {
        const response = await fetch('/timer/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                task_description: taskDescription.value,
                project_id: projectId.value || null
            })
        });

        const data = await response.json();
        currentEntryId = data.id;
        startTime = new Date(data.start_time).getTime();
        timerInterval = setInterval(updateTimer, 1000);
        isRunning = true;

        startBtn.disabled = true;
        pauseBtn.disabled = false;
        stopBtn.disabled = false;
        taskDescription.disabled = true;
        projectId.disabled = true;

    } catch (error) {
        console.error('Error starting timer:', error);
        alert('Failed to start timer');
    }
}

async function pauseTimer() {
    if (!isRunning) return;

    try {
        await fetch('/timer/pause', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        clearInterval(timerInterval);
        isRunning = false;

        startBtn.disabled = false;
        pauseBtn.disabled = true;
        stopBtn.disabled = false;

    } catch (error) {
        console.error('Error pausing timer:', error);
        alert('Failed to pause timer');
    }
}

async function resumeTimer() {
    try {
        const response = await fetch('/timer/resume', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        startTime = new Date(data.start_time).getTime();
        timerInterval = setInterval(updateTimer, 1000);
        isRunning = true;

        startBtn.disabled = true;
        pauseBtn.disabled = false;
        stopBtn.disabled = false;

    } catch (error) {
        console.error('Error resuming timer:', error);
        alert('Failed to resume timer');
    }
}

async function stopTimer() {
    try {
        await fetch('/timer/stop', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        clearInterval(timerInterval);
        isRunning = false;
        elapsedTime = 0;
        currentEntryId = null;
        timerDisplay.textContent = '00:00:00';

        startBtn.disabled = false;
        pauseBtn.disabled = true;
        stopBtn.disabled = true;
        taskDescription.disabled = false;
        projectId.disabled = false;
        taskDescription.value = '';
        projectId.value = '';

        // Reload the page to update the summary and time entries
        window.location.reload();

    } catch (error) {
        console.error('Error stopping timer:', error);
        alert('Failed to stop timer');
    }
}

startBtn.addEventListener('click', function() {
    if (!isRunning) {
        if (currentEntryId) {
            resumeTimer();
        } else {
            startTimer();
        }
    }
});

pauseBtn.addEventListener('click', function() {
    if (isRunning) {
        pauseTimer();
    }
});

stopBtn.addEventListener('click', function() {
    if (confirm('Are you sure you want to stop the timer?')) {
        stopTimer();
    }
});

// Prevent form submission
timerForm.addEventListener('submit', function(e) {
    e.preventDefault();
});
</script>
@endpush
