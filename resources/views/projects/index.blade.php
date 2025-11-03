@extends('layouts.app')

@section('title', 'Projects - MyTime')

@section('content')
<!-- Header Section -->
<div class="mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-3">
                <div style="width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102,126,234,.3);">
                    <i class="fas fa-layer-group" style="font-size: 32px; color: white;"></i>
                </div>
                <div class="ms-3">
                    <h1 class="display-5 fw-bold mb-0" style="color: white;">Projects</h1>
                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">{{ auth()->user()->isAdmin() ? 'Manage and track all projects' : 'View and track your assigned projects' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('projects.create') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-plus me-2"></i>Create New Project
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="total">{{ $stats['total'] }}</h3>
                        <p class="mb-0 text-muted small">Total Projects</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="active">{{ $stats['active'] }}</h3>
                        <p class="mb-0 text-muted small">Active</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="pending">{{ $stats['pending'] }}</h3>
                        <p class="mb-0 text-muted small">Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="overdue">{{ $stats['overdue'] }}</h3>
                        <p class="mb-0 text-muted small">Overdue</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0" style="font-size: 2rem; font-weight: 700;" data-stat="completed">{{ $stats['completed'] }}</h3>
                        <p class="mb-0 text-muted small">Completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects Section -->
<div class="card border-0 shadow-sm">
    <!-- Card Header with Filters -->
    <div class="card-header bg-white border-0 p-3 p-md-4">
        <div class="row align-items-center g-3">
            <div class="col-12">
                <h5 class="mb-0 fw-bold">All Projects</h5>
            </div>
            <div class="col-12">
                <form method="GET" action="{{ route('projects.index') }}" class="row g-2 align-items-end">
                    <div class="col-12 col-md-auto">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inprogress" {{ request('status') == 'inprogress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review_pending" {{ request('status') == 'review_pending' ? 'selected' : '' }}>Review Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 col-md">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search projects..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int)request('per_page', 25) === $size ? 'selected' : '' }}>
                                    Show {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body p-0">
        @if($projects->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Budget</th>
                            <th>Timeline</th>
                            <th>Team</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr data-project-id="{{ $project->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); flex-shrink: 0;">
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">{{ $project->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td data-field="status">
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inprogress' => 'primary',
                                            'review_pending' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'dark',
                                        ];
                                        $statusColor = $statusColors[$project->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                                </td>
                                <td data-field="priority">
                                    @php
                                        $priorityColors = [
                                            'urgent' => 'danger',
                                            'high' => 'warning',
                                            'medium' => 'info',
                                            'low' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $priorityColors[$project->priority] ?? 'secondary' }}">
                                        <i class="fas fa-flag me-1"></i>{{ ucfirst($project->priority) }}
                                    </span>
                                </td>
                                <td data-field="progress">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 100px; height: 20px; cursor: pointer;" title="Click to update progress" onclick="showProgressModal({{ $project->id }}, {{ $project->progress }})">
                                            <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                                        </div>
                                        <small>{{ $project->progress }}%</small>
                                        @if($project->progress < 100)
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-success btn-sm" title="Add 5%" onclick="quickUpdateProgress({{ $project->id }}, 5)">
                                                    <i class="fas fa-plus"></i>5%
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm" title="Add 10%" onclick="quickUpdateProgress({{ $project->id }}, 10)">
                                                    <i class="fas fa-plus"></i>10%
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm" title="Add 25%" onclick="quickUpdateProgress({{ $project->id }}, 25)">
                                                    <i class="fas fa-plus"></i>25%
                                                </button>
                                            </div>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Complete</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-field="budget">
                                    @if($project->budget)
                                        <span class="text-success fw-semibold">${{ number_format($project->budget, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <div><i class="fas fa-calendar-check text-success me-1"></i>{{ $project->start_date->format('M d, Y') }}</div>
                                        @if($project->end_date)
                                            <div><i class="fas fa-calendar-times text-danger me-1"></i>{{ $project->end_date->format('M d, Y') }}</div>
                                        @else
                                            <div class="text-muted">No end date</div>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($project->creator)
                                            <div class="avatar-sm" title="{{ $project->creator->name }} (Owner)" style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 600;">
                                                {{ substr($project->creator->name, 0, 1) }}
                                            </div>
                                        @endif
                                        @foreach($project->teamMembers->take(2) as $member)
                                            <div class="avatar-sm" title="{{ $member->name }}" style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 600;">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($project->teamMembers->count() > 2)
                                            <div class="avatar-sm" style="width: 36px; height: 36px; border-radius: 10px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.75rem; font-weight: 600;">
                                                +{{ $project->teamMembers->count() - 2 }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" title="Delete" onclick="deleteProject({{ $project->id }}, '{{ $project->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none p-3">
                @foreach($projects as $project)
                    <div class="card mb-3 border-start border-4" style="border-color: {{ $project->priority === 'urgent' ? '#fa709a' : ($project->priority === 'high' ? '#f5576c' : ($project->priority === 'medium' ? '#4facfe' : '#6c757d')) }};">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <div style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); flex-shrink: 0;">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $project->name }}</h6>
                                    <small class="text-muted">{{ Str::limit($project->description, 60) }}</small>
                                </div>
                            </div>

                            <div class="row g-2 mb-3 small">
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">
                                        <div class="text-muted">Status</div>
                                        <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">
                                        <div class="text-muted">Priority</div>
                                        <span class="badge bg-{{ $priorityColors[$project->priority] ?? 'secondary' }}">{{ ucfirst($project->priority) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">
                                        <div class="text-muted">Budget</div>
                                        @if($project->budget)
                                            <span class="text-success fw-semibold">${{ number_format($project->budget, 0) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded">
                                        <div class="text-muted">Progress</div>
                                        <span class="fw-semibold">{{ $project->progress }}%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProject({{ $project->id }}, '{{ $project->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div style="width: 100px; height: 100px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-folder-open" style="font-size: 48px; color: #667eea;"></i>
                </div>
                <h5 class="fw-bold">No projects found</h5>
                <p class="text-muted mb-3">Get started by creating your first project.</p>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Project
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center gy-3">
                <div class="col-md-6">
                    <small class="text-muted">
                        Showing <strong>{{ $projects->firstItem() }}</strong> to
                        <strong>{{ $projects->lastItem() }}</strong> of
                        <strong>{{ $projects->total() }}</strong> results
                    </small>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Projects pagination">
                        {{ $projects->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function deleteProject(projectId, projectName) {
    if (confirm(`Are you sure you want to delete "${projectName}"? This action cannot be undone.`)) {
        const deleteBtn = event.target.closest('button');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`/projects/${projectId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reload page after 1 second to show updated list with cache-busting
                setTimeout(() => {
                    window.location.href = '/projects?t=' + Date.now();
                }, 1000);
            } else {
                showToast(data.message || 'Error deleting project', 'danger');
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting project: ' + error.message, 'danger');
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            }
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

function quickUpdateProgress(projectId, increment) {
    const btn = event.target.closest('button');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    fetch(`/projects/${projectId}/quick-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            increment: increment
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the progress bar and percentage
            const row = document.querySelector(`tr[data-project-id="${projectId}"]`);
            if (row) {
                const progressCell = row.querySelector('[data-field="progress"]');
                const progressBar = progressCell.querySelector('.progress-bar');
                const progressText = progressCell.querySelector('small');
                
                progressBar.style.width = data.progress + '%';
                progressText.textContent = data.progress + '%';

                // If auto-completed, update status and show message
                if (data.auto_completed) {
                    showToast('🎉 Project completed! Status automatically updated to Complete.', 'success');
                    
                    // Update status badge
                    const statusCell = row.querySelector('[data-field="status"]');
                    statusCell.innerHTML = '<span class="badge bg-success">Completed</span>';
                    
                    // Replace quick buttons with complete badge
                    const btnGroup = progressCell.querySelector('.btn-group');
                    if (btnGroup) {
                        btnGroup.remove();
                    }
                    const completeSpan = document.createElement('span');
                    completeSpan.className = 'badge bg-success';
                    completeSpan.innerHTML = '<i class="fas fa-check-circle me-1"></i>Complete';
                    progressCell.appendChild(completeSpan);
                    
                    // Reload page after 2 seconds to refresh stats
                    setTimeout(() => {
                        window.location.href = '/projects?t=' + Date.now();
                    }, 2000);
                } else {
                    showToast(`Progress updated to ${data.progress}%`, 'success');
                }
            }
            
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i>' + increment + '%';
            }
        } else {
            showToast(data.message || 'Error updating progress', 'danger');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i>' + increment + '%';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating progress: ' + error.message, 'danger');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i>' + increment + '%';
        }
    });
}

function showProgressModal(projectId, currentProgress) {
    const newProgress = prompt(`Enter new progress percentage (0-100):\n\nCurrent: ${currentProgress}%`, currentProgress);
    
    if (newProgress !== null) {
        const progress = parseInt(newProgress);
        
        if (isNaN(progress) || progress < 0 || progress > 100) {
            showToast('Please enter a valid number between 0 and 100', 'warning');
            return;
        }

        fetch(`/projects/${projectId}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                progress: progress
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update the progress bar and percentage
                const row = document.querySelector(`tr[data-project-id="${projectId}"]`);
                if (row) {
                    const progressCell = row.querySelector('[data-field="progress"]');
                    const progressBar = progressCell.querySelector('.progress-bar');
                    const progressText = progressCell.querySelector('small');
                    
                    progressBar.style.width = data.progress + '%';
                    progressText.textContent = data.progress + '%';

                    // If auto-completed, update status and show message
                    if (data.auto_completed) {
                        showToast('🎉 Project completed! Status automatically updated to Complete.', 'success');
                        
                        // Update status badge
                        const statusCell = row.querySelector('[data-field="status"]');
                        statusCell.innerHTML = '<span class="badge bg-success">Completed</span>';
                        
                        // Replace quick buttons with complete badge
                        const btnGroup = progressCell.querySelector('.btn-group');
                        if (btnGroup) {
                            btnGroup.remove();
                        }
                        const completeSpan = document.createElement('span');
                        completeSpan.className = 'badge bg-success';
                        completeSpan.innerHTML = '<i class="fas fa-check-circle me-1"></i>Complete';
                        progressCell.appendChild(completeSpan);
                        
                        // Reload page after 2 seconds to refresh stats
                        setTimeout(() => {
                            window.location.href = '/projects?t=' + Date.now();
                        }, 2000);
                    } else {
                        showToast(`Progress updated to ${data.progress}%`, 'success');
                    }
                }
            } else {
                showToast(data.message || 'Error updating progress', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating progress: ' + error.message, 'danger');
        });
    }
}
</script>
@endpush
