@extends('layouts.app')

@section('title', 'Time Logs - MyTime')

@push('styles')
<style>
.time-logs-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.time-log-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.time-log-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 0.5rem;
}

.time-log-row {
    background: #f8f9fa;
    transition: all 0.3s;
}

.time-log-row:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.time-log-row td {
    padding: 1rem;
    vertical-align: middle;
}

.time-log-row td:first-child {
    border-radius: 10px 0 0 10px;
}

.time-log-row td:last-child {
    border-radius: 0 10px 10px 0;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}

.status-running {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    animation: pulse 2s infinite;
}

.status-paused {
    background: #ffc107;
    color: #333;
}

.status-completed {
    background: #10b981;
    color: white;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.filter-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.modern-input, .modern-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem;
    transition: all 0.3s;
}

.modern-input:focus, .modern-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-success-modern {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: white;
}

.btn-danger-modern {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    color: white;
}

.project-pill {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    background: #e9ecef;
    font-size: 0.85rem;
    font-weight: 500;
}

.duration-display {
    font-size: 1.1rem;
    font-weight: 600;
    color: #667eea;
}

.modal-modern .modal-content {
    border-radius: 20px;
    border: none;
}

.modal-modern .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
}

.timer-display {
    font-size: 3rem;
    font-weight: bold;
    color: #667eea;
    text-align: center;
    margin: 2rem 0;
}
</style>
@endpush

@section('content')
<!-- Header -->
<div class="time-logs-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-clock me-2"></i>Time Logs</h1>
            <p class="mb-0 opacity-90">Track and manage your time entries</p>
        </div>
        <div>
            <button class="btn btn-light btn-modern" data-bs-toggle="modal" data-bs-target="#addTimeLogModal">
                <i class="fas fa-plus me-2"></i>Add Manual Entry
            </button>
            <button class="btn btn-success btn-modern ms-2" data-bs-toggle="modal" data-bs-target="#startTimerModal">
                <i class="fas fa-play me-2"></i>Start Timer
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value" style="color: #667eea;">{{ $todayHours }}h</div>
            <div class="stat-label">Today's Hours</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-value" style="color: #f5576c;">{{ $weekHours }}h</div>
            <div class="stat-label">This Week</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-value" style="color: #00f2fe;">{{ $totalHours }}h</div>
            <div class="stat-label">Total (Period)</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-value" style="color: #fa709a;">{{ $activeEntries }}</div>
            <div class="stat-label">Active Timers</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('time-logs.index') }}">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Period</label>
                <select name="period" class="form-select modern-select" onchange="this.form.submit()">
                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Project</label>
                <select name="project" class="form-select modern-select" onchange="this.form.submit()">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectFilter == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select modern-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="running" {{ $statusFilter == 'running' ? 'selected' : '' }}>Running</option>
                    <option value="paused" {{ $statusFilter == 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="col-md-3">
                <a href="{{ route('time-logs.index') }}" class="btn btn-outline-secondary w-100 btn-modern">
                    <i class="fas fa-redo me-2"></i>Reset Filters
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Time Logs Table -->
<div class="time-log-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Time Entries</h5>
        <span class="text-muted">{{ $timeLogs->total() }} entries found</span>
    </div>

    @if($timeLogs->count() > 0)
    <div class="table-responsive">
        <table class="time-log-table">
            <thead>
                <tr>
                    <th class="ps-3">Date & Time</th>
                    <th>Project</th>
                    <th>Task Description</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeLogs as $log)
                <tr class="time-log-row">
                    <td class="ps-3">
                        <div class="fw-bold">{{ $log->start_time->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $log->start_time->format('h:i A') }}</small>
                    </td>
                    <td>
                        @if($log->project)
                            <span class="project-pill">{{ $log->project->name }}</span>
                        @else
                            <span class="text-muted">No Project</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $log->task_description }}</div>
                        @if($log->notes)
                            <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>{{ Str::limit($log->notes, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="duration-display">{{ $log->formatted_duration }}</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $log->status }}">
                            @if($log->status === 'running')
                                <i class="fas fa-circle me-1"></i>Running
                            @elseif($log->status === 'paused')
                                <i class="fas fa-pause me-1"></i>Paused
                            @else
                                <i class="fas fa-check me-1"></i>Completed
                            @endif
                        </span>
                    </td>
                    <td>
                        @if($log->status === 'running')
                            <form action="{{ route('time-logs.stop', $log->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger-modern">
                                    <i class="fas fa-stop"></i>
                                </button>
                            </form>
                        @endif
                        <button class="btn btn-sm btn-outline-primary" onclick="editTimeLog({{ $log->id }}, '{{ $log->task_description }}', {{ $log->duration_minutes }}, '{{ $log->notes }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('time-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this time log?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $timeLogs->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-clock fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No time logs found</h5>
        <p class="text-muted">Start tracking your time to see entries here</p>
    </div>
    @endif
</div>

<!-- Add Manual Time Log Modal -->
<div class="modal fade modal-modern" id="addTimeLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Manual Time Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('time-logs.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Project *</label>
                        <select name="project_id" class="form-select modern-select" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Description *</label>
                        <input type="text" name="task_description" class="form-control modern-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Start Time *</label>
                        <input type="datetime-local" name="start_time" class="form-control modern-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Duration (minutes) *</label>
                        <input type="number" name="duration_minutes" class="form-control modern-input" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control modern-input" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-modern">Add Time Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Start Timer Modal -->
<div class="modal fade modal-modern" id="startTimerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-play me-2"></i>Start Time Tracking</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('time-logs.start') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Project *</label>
                        <select name="project_id" class="form-select modern-select" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">What are you working on? *</label>
                        <input type="text" name="task_description" class="form-control modern-input"
                               placeholder="e.g., Developing login feature" required>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Any currently running timer will be automatically paused.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success-modern">
                        <i class="fas fa-play me-2"></i>Start Tracking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Time Log Modal -->
<div class="modal fade modal-modern" id="editTimeLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Time Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTimeLogForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Description *</label>
                        <input type="text" name="task_description" id="edit_task_description" class="form-control modern-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Duration (minutes) *</label>
                        <input type="number" name="duration_minutes" id="edit_duration_minutes" class="form-control modern-input" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control modern-input" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-modern">Update Time Log</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editTimeLog(id, description, duration, notes) {
    document.getElementById('editTimeLogForm').action = `/time-logs/${id}`;
    document.getElementById('edit_task_description').value = description;
    document.getElementById('edit_duration_minutes').value = duration;
    document.getElementById('edit_notes').value = notes || '';

    const modal = new bootstrap.Modal(document.getElementById('editTimeLogModal'));
    modal.show();
}

// Auto-hide success messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 3000);
</script>
@endpush
