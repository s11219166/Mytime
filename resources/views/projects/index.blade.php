@extends('layouts.app')

@section('title', 'Projects - MyTime')

@section('content')
<!-- Modern Header with Light Green Gradient -->
<div class="modern-header mb-5" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-top: 4px solid #10b981;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="header-content">
                    <div class="d-flex align-items-center mb-3">
                        <div class="header-icon-wrapper" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16,185,129,.3);">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="ms-3">
                            <h1 class="display-5 fw-bold mb-0" style="color:#065f46;">Projects</h1>
                            <p class="mb-0" style="color:#047857;">{{ auth()->user()->isAdmin() ? 'Manage and track all projects' : 'View and track your assigned projects' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-lg-end">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('projects.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>Create New Project
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modern Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl col-lg-4 col-md-6">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total'] }}</h3>
                    <p class="stat-label">Total Projects</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <div class="stat-progress" style="width: 100%"></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <div class="stat-card stat-card-success">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['active'] }}</h3>
                    <p class="stat-label">Active</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <div class="stat-progress" style="width: {{ $stats['total'] > 0 ? ($stats['active'] / $stats['total'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['pending'] }}</h3>
                    <p class="stat-label">Pending</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <div class="stat-progress" style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <div class="stat-card stat-card-danger">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['overdue'] }}</h3>
                    <p class="stat-label">Overdue</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <div class="stat-progress" style="width: {{ $stats['total'] > 0 ? ($stats['overdue'] / $stats['total'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-md-6">
        <div class="stat-card stat-card-info">
            <div class="stat-card-body">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['completed'] }}</h3>
                    <p class="stat-label">Completed</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <div class="stat-progress" style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Projects Table -->
<div class="modern-card">
    <div class="modern-card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="mb-0 fw-bold">All Projects</h4>
            <div class="d-flex gap-2 flex-wrap">
                <form method="GET" action="{{ route('projects.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
                    <select name="status" class="modern-select" style="width: auto;" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inprogress" {{ request('status') == 'inprogress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review_pending" {{ request('status') == 'review_pending' ? 'selected' : '' }}>Review Pending</option>
                        <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                        <option value="awaiting_input" {{ request('status') == 'awaiting_input' ? 'selected' : '' }}>Awaiting Input</option>
                        <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div class="modern-search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="modern-search-input" placeholder="Search projects..." value="{{ request('search') }}">
                        <button class="search-btn" type="submit">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <select name="per_page" class="modern-select" style="width: auto;" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int)request('per_page', 25) === $size ? 'selected' : '' }}>
                                Show {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </form>
    </div>
    <div class="modern-card-body">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="modern-table">
                <thead>
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
                    @forelse($projects as $project)
                        <tr class="project-row">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="project-avatar project-avatar-{{ $project->priority == 'urgent' ? 'danger' : ($project->priority == 'high' ? 'warning' : ($project->priority == 'medium' ? 'info' : 'secondary')) }}">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="project-name mb-1">{{ $project->name }}</h6>
                                        <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $currentStatus = $project->current_status;
                                    $statusColors = [
                                        'active' => 'success',
                                        'inprogress' => 'primary',
                                        'review_pending' => 'warning',
                                        'revision_needed' => 'info',
                                        'awaiting_input' => 'info',
                                        'paused' => 'secondary',
                                        'overdue' => 'danger',
                                        'due' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'dark',
                                        'inactive' => 'light'
                                    ];
                                    $statusIcons = [
                                        'active' => 'fa-rocket',
                                        'inprogress' => 'fa-spinner',
                                        'review_pending' => 'fa-eye',
                                        'revision_needed' => 'fa-edit',
                                        'awaiting_input' => 'fa-hourglass-half',
                                        'paused' => 'fa-pause-circle',
                                        'overdue' => 'fa-exclamation-triangle',
                                        'due' => 'fa-clock',
                                        'completed' => 'fa-check-circle',
                                        'cancelled' => 'fa-times-circle',
                                        'inactive' => 'fa-minus-circle'
                                    ];
                                @endphp
                                <div class="status-badge-wrapper">
                                    <span class="modern-badge badge-{{ $statusColors[$currentStatus] ?? 'secondary' }}">
                                        <i class="fas {{ $statusIcons[$currentStatus] ?? 'fa-circle' }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                                    </span>
                                    <div class="small text-muted mt-1">
                                        {{ $project->time_status }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $priorityColors = [
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        'low' => 'secondary'
                                    ];
                                @endphp
                                <span class="priority-badge priority-{{ $priorityColors[$project->priority] ?? 'secondary' }}">
                                    <i class="fas fa-flag me-1"></i>
                                    {{ ucfirst($project->priority) }}
                                </span>
                            </td>
                            <td>
                                <div class="modern-progress-wrapper">
                                    <div class="modern-progress" onclick="openProgressModal({{ $project->id }}, {{ $project->progress }}, '{{ $project->name }}')">
                                        <div class="modern-progress-bar modern-progress-{{ $project->progress >= 75 ? 'success' : ($project->progress >= 50 ? 'info' : ($project->progress >= 25 ? 'warning' : 'danger')) }}"
                                             style="width: {{ $project->progress }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">{{ $project->progress }}%</small>
                                        <button class="btn-icon-sm" onclick="openProgressModal({{ $project->id }}, {{ $project->progress }}, '{{ $project->name }}')" title="Update Progress">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($project->budget)
                                    <div class="budget-display">
                                        <i class="fas fa-dollar-sign text-success me-1"></i>
                                        <span class="fw-semibold">${{ number_format($project->budget, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="timeline-display">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-check text-success me-2 small"></i>
                                        <small>{{ $project->start_date->format('M d, Y') }}</small>
                                    </div>
                                    @if($project->end_date)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-times text-danger me-2 small"></i>
                                            <small>{{ $project->end_date->format('M d, Y') }}</small>
                                        </div>
                                    @else
                                        <small class="text-muted">No end date</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="team-avatars">
                                    @if($project->creator)
                                        <div class="team-avatar team-avatar-primary" title="{{ $project->creator->name }} (Owner)">
                                            <span>{{ substr($project->creator->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    @foreach($project->teamMembers->take(3) as $member)
                                        <div class="team-avatar team-avatar-info" title="{{ $member->name }}">
                                            <span>{{ substr($member->name, 0, 1) }}</span>
                                        </div>
                                    @endforeach
                                    @if($project->teamMembers->count() > 3)
                                        <div class="team-avatar team-avatar-more">
                                            <span>+{{ $project->teamMembers->count() - 3 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="action-buttons">
                                    <a href="{{ route('projects.show', $project) }}" class="btn-action btn-action-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('projects.edit', $project) }}" class="btn-action btn-action-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-action btn-action-danger" title="Delete" onclick="deleteProject({{ $project->id }}, '{{ $project->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h5 class="empty-title">No projects found</h5>
                                    <p class="empty-description">Get started by creating your first project.</p>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('projects.create') }}" class="btn btn-gradient-primary">
                                            <i class="fas fa-plus me-2"></i>Create Project
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none px-3 py-2">
            @forelse($projects as $project)
                <div class="mobile-project-card priority-{{ $project->priority }}">
                    <!-- Header -->
                    <div class="mobile-project-header">
                        <div class="project-avatar project-avatar-{{ $project->priority == 'urgent' ? 'danger' : ($project->priority == 'high' ? 'warning' : ($project->priority == 'medium' ? 'info' : 'secondary')) }}">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="mobile-project-info">
                            <div class="mobile-project-name">{{ $project->name }}</div>
                            <div class="mobile-project-desc">{{ Str::limit($project->description, 60) }}</div>
                            @php
                                $currentStatus = $project->current_status;
                                $statusColors = [
                                    'active' => 'success',
                                    'inprogress' => 'primary',
                                    'review_pending' => 'warning',
                                    'revision_needed' => 'info',
                                    'awaiting_input' => 'info',
                                    'paused' => 'secondary',
                                    'overdue' => 'danger',
                                    'completed' => 'success',
                                    'cancelled' => 'dark',
                                ];
                                $statusIcons = [
                                    'active' => 'fa-rocket',
                                    'inprogress' => 'fa-spinner',
                                    'review_pending' => 'fa-eye',
                                    'revision_needed' => 'fa-edit',
                                    'awaiting_input' => 'fa-hourglass-half',
                                    'paused' => 'fa-pause-circle',
                                    'overdue' => 'fa-exclamation-triangle',
                                    'completed' => 'fa-check-circle',
                                    'cancelled' => 'fa-times-circle',
                                ];
                            @endphp
                            <span class="modern-badge badge-{{ $statusColors[$currentStatus] ?? 'secondary' }} mt-2">
                                <i class="fas {{ $statusIcons[$currentStatus] ?? 'fa-circle' }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Meta Info Grid -->
                    <div class="mobile-project-meta">
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">Priority</div>
                            <div class="mobile-meta-value">
                                <i class="fas fa-flag me-1"></i>{{ ucfirst($project->priority) }}
                            </div>
                        </div>
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">Budget</div>
                            <div class="mobile-meta-value">
                                @if($project->budget)
                                    <i class="fas fa-dollar-sign me-1"></i>${{ number_format($project->budget, 0) }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">Start Date</div>
                            <div class="mobile-meta-value">
                                <i class="fas fa-calendar-check me-1"></i>{{ $project->start_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">End Date</div>
                            <div class="mobile-meta-value">
                                @if($project->end_date)
                                    <i class="fas fa-calendar-times me-1"></i>{{ $project->end_date->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mobile-progress">
                        <div class="mobile-progress-header">
                            <span class="mobile-progress-label">Progress</span>
                            <span class="mobile-progress-value">{{ $project->progress }}%</span>
                        </div>
                        <div class="modern-progress" onclick="openProgressModal({{ $project->id }}, {{ $project->progress }}, '{{ $project->name }}')">
                            <div class="modern-progress-bar modern-progress-{{ $project->progress >= 75 ? 'success' : ($project->progress >= 50 ? 'info' : ($project->progress >= 25 ? 'warning' : 'danger')) }}"
                                 style="width: {{ $project->progress }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Team -->
                    @if($project->creator || $project->teamMembers->count() > 0)
                        <div class="mobile-meta-label mb-2">Team Members</div>
                        <div class="mobile-team">
                            @if($project->creator)
                                <div class="team-avatar team-avatar-primary" title="{{ $project->creator->name }} (Owner)">
                                    <span>{{ substr($project->creator->name, 0, 1) }}</span>
                                </div>
                            @endif
                            @foreach($project->teamMembers->take(5) as $member)
                                <div class="team-avatar team-avatar-info" title="{{ $member->name }}">
                                    <span>{{ substr($member->name, 0, 1) }}</span>
                                </div>
                            @endforeach
                            @if($project->teamMembers->count() > 5)
                                <div class="team-avatar team-avatar-more">
                                    <span>+{{ $project->teamMembers->count() - 5 }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mobile-actions mt-3">
                        <a href="{{ route('projects.show', $project) }}" class="mobile-btn mobile-btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('projects.edit', $project) }}" class="mobile-btn mobile-btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this project?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mobile-btn mobile-btn-danger w-100">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h5 class="empty-title">No projects found</h5>
                    <p class="empty-description">Get started by creating your first project.</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('projects.create') }}" class="btn btn-gradient-primary">
                            <i class="fas fa-plus me-2"></i>Create Project
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
    @if($projects->hasPages())
        <div class="modern-card-footer">
            <div class="row align-items-center gy-3">
                <div class="col-md-6">
                    <div class="pagination-info">
                        Showing <span class="fw-semibold">{{ $projects->firstItem() }}</span> to
                        <span class="fw-semibold">{{ $projects->lastItem() }}</span> of
                        <span class="fw-semibold">{{ $projects->total() }}</span> results
                    </div>
                </div>
                <div class="col-md-6">
                    <nav class="d-flex justify-content-md-end" aria-label="Projects pagination">
                        <ul class="modern-pagination">
                            @if ($projects->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $projects->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @foreach ($projects->onEachSide(2)->links()->elements[0] as $page => $url)
                                @if ($page == $projects->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            @if ($projects->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $projects->nextPageUrl() }}" rel="next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    @endif
</div>
<!-- Progress Update Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="progressModalLabel">
                    <i class="fas fa-tasks me-2"></i>Update Project Progress
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="progressForm">
                    <input type="hidden" id="projectId" name="project_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold" id="projectNameLabel">Project Name</label>
                    </div>

                    <div class="mb-4">
                        <label for="progressRange" class="form-label">Progress: <span id="progressValue" class="fw-bold text-success">0</span>%</label>
                        <input type="range" class="form-range" id="progressRange" name="progress" min="0" max="100" value="0" step="5">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                            <span>75%</span>
                            <span>100%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quick Select:</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setProgress(0)">0%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setProgress(25)">25%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setProgress(50)">50%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setProgress(75)">75%</button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="setProgress(100)">100%</button>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="previewProgressBar" role="progressbar" style="width: 0%">
                            <span id="previewProgressText">0%</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="updateProgress()">
                    <i class="fas fa-save me-2"></i>Update Progress
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Modern Header Styles */
.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 2.5rem;
    color: white;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.modern-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.header-icon-wrapper {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.header-icon-wrapper i {
    font-size: 32px;
    color: white;
}

.text-gradient {
    background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Modern Stat Cards */
.stat-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.stat-card-body {
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
}

.stat-card-primary .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card-success .stat-icon {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    color: white;
}

.stat-card-warning .stat-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card-danger .stat-icon {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.stat-card-info .stat-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stat-label {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card-footer {
    height: 4px;
    background: #e9ecef;
    position: relative;
}

.stat-progress {
    height: 100%;
    transition: width 1s ease;
}

.stat-card-primary .stat-progress { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card-success .stat-progress { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); }
.stat-card-warning .stat-progress { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card-danger .stat-progress { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card-info .stat-progress { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

/* Modern Card */
.modern-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.modern-card-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.modern-card-body {
    padding: 0;
}

.modern-card-footer {
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

/* Modern Select */
.modern-select {
    padding: 0.625rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: white;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s;
    cursor: pointer;
}

.modern-select:hover {
    border-color: #667eea;
}

.modern-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

/* Modern Search Box */
.modern-search-box {
    position: relative;
    display: flex;
    align-items: center;
    width: 300px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    color: #6c757d;
    pointer-events: none;
}

.modern-search-input {
    width: 100%;
    padding: 0.625rem 3rem 0.625rem 2.5rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.3s;
}

.modern-search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.search-btn {
    position: absolute;
    right: 0.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
}

.search-btn:hover {
    transform: scale(1.1);
}

/* Modern Table */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    border: none;
}

.modern-table thead th:first-child {
    border-top-left-radius: 0;
}

.modern-table thead th:last-child {
    border-top-right-radius: 0;
}

.modern-table tbody tr {
    transition: all 0.3s;
}

.modern-table tbody tr:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.modern-table tbody td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

/* Project Row Styles */
.project-avatar {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
    color: white;
}

.project-avatar-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.project-avatar-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.project-avatar-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.project-avatar-secondary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

.project-name {
    font-weight: 600;
    color: #212529;
    margin: 0;
    font-size: 0.95rem;
}

.modern-table tbody td {
    font-size: 0.875rem;
}

.modern-table thead th {
    font-size: 0.7rem;
}

.stat-number {
    font-size: 2rem;
}

.stat-label {
    font-size: 0.7rem;
}

/* Modern Badges */
.modern-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: linear-gradient(135deg, rgba(86, 171, 47, 0.1) 0%, rgba(168, 224, 99, 0.1) 100%);
    color: #56ab2f;
    border: 1.5px solid #56ab2f40;
}

.badge-primary {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: #667eea;
    border: 1.5px solid #667eea40;
}

.badge-warning {
    background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%);
    color: #f5576c;
    border: 1.5px solid #f5576c40;
}

.badge-danger {
    background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
    color: #fa709a;
    border: 1.5px solid #fa709a40;
}

.badge-info {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
    color: #4facfe;
    border: 1.5px solid #4facfe40;
}

.badge-secondary {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border: 1.5px solid #6c757d40;
}

.badge-dark {
    background: rgba(33, 37, 41, 0.1);
    color: #212529;
    border: 1.5px solid #21252940;
}

/* Priority Badge */
.priority-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.875rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.priority-danger {
    background: linear-gradient(135deg, rgba(250, 112, 154, 0.15) 0%, rgba(254, 225, 64, 0.15) 100%);
    color: #fa709a;
}

.priority-warning {
    background: linear-gradient(135deg, rgba(240, 147, 251, 0.15) 0%, rgba(245, 87, 108, 0.15) 100%);
    color: #f5576c;
}

.priority-info {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.15) 0%, rgba(0, 242, 254, 0.15) 100%);
    color: #4facfe;
}

.priority-secondary {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
}

/* Modern Progress */
.modern-progress-wrapper {
    min-width: 140px;
}

.modern-progress {
    height: 10px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}

.modern-progress:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.modern-progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}

.modern-progress-success { background: linear-gradient(90deg, #56ab2f 0%, #a8e063 100%); }
.modern-progress-info { background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%); }
.modern-progress-warning { background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%); }
.modern-progress-danger { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }

.btn-icon-sm {
    background: none;
    border: none;
    padding: 0.25rem 0.5rem;
    color: #667eea;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-icon-sm:hover {
    color: #764ba2;
    transform: scale(1.2);
}

/* Team Avatars */
.team-avatars {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.team-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.team-avatar-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.team-avatar-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.team-avatar-more {
    background: #e9ecef;
    color: #6c757d;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.875rem;
}

.btn-action-primary {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: #667eea;
}

.btn-action-primary:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-action-secondary {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.btn-action-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.btn-action-danger {
    background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
    color: #fa709a;
}

.btn-action-danger:hover {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);
}

/* Gradient Button */
.btn-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-gradient-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    color: white;
}

/* Empty State */
.empty-state {
    padding: 3rem 2rem;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon i {
    font-size: 48px;
    color: #667eea;
}

.empty-title {
    font-weight: 700;
    color: #212529;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

/* Modern Pagination */
.modern-pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.modern-pagination .page-link {
    padding: 0.625rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    color: #495057;
    background: white;
    transition: all 0.3s;
    font-weight: 500;
    text-decoration: none;
}

.modern-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
}

.modern-pagination .page-link:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
}

.modern-pagination .page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.875rem;
}

.form-range::-webkit-slider-thumb {
    background: #32CD32;
}

.form-range::-moz-range-thumb {
    background: #32CD32;
}

.form-range::-webkit-slider-runnable-track {
    background: linear-gradient(to right, #90EE90 0%, #32CD32 100%);
}

.modal-header.bg-success {
    background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%) !important;
}

/* ==========================================
   MOBILE RESPONSIVE STYLES
   ========================================== */

@media (max-width: 768px) {
    /* Header */
    .modern-header {
        padding: 1.5rem;
        border-radius: 16px;
    }

    .header-icon-wrapper {
        width: 50px;
        height: 50px;
    }

    .header-icon-wrapper i {
        font-size: 24px;
    }

    .modern-header h1 {
        font-size: 1.75rem !important;
    }

    .btn-gradient-primary {
        width: 100%;
        margin-top: 1rem;
    }

    /* Stat Cards */
    .stat-card-body {
        padding: 1.25rem;
        gap: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }

    .stat-number {
        font-size: 1.75rem;
    }

    .stat-label {
        font-size: 0.75rem;
    }

    /* Modern Card */
    .modern-card-header {
        padding: 1rem;
    }

    .modern-card-header h4 {
        font-size: 1rem;
    }

    /* Search and Filters */
    .modern-search-box {
        width: 100%;
        order: 1;
    }

    .modern-select {
        width: 100% !important;
    }

    .modern-card-header .d-flex {
        flex-direction: column;
    }

    .modern-card-header form {
        width: 100%;
    }

    /* Table Responsiveness */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Hide table on mobile, show card layout */
    .modern-table {
        display: none;
    }

    /* Mobile Card Layout for Projects */
    .mobile-project-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #667eea;
    }

    .mobile-project-card.priority-urgent {
        border-left-color: #fa709a;
    }

    .mobile-project-card.priority-high {
        border-left-color: #f5576c;
    }

    .mobile-project-card.priority-medium {
        border-left-color: #4facfe;
    }

    .mobile-project-card.priority-low {
        border-left-color: #6c757d;
    }

    .mobile-project-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .mobile-project-info {
        flex: 1;
    }

    .mobile-project-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .mobile-project-desc {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }

    .mobile-project-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .mobile-meta-item {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 10px;
    }

    .mobile-meta-label {
        font-size: 0.625rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .mobile-meta-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #212529;
    }

    .mobile-progress {
        margin-bottom: 1rem;
    }

    .mobile-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .mobile-progress-label {
        font-size: 0.625rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
    }

    .mobile-progress-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: #56ab2f;
    }

    .mobile-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .mobile-btn {
        flex: 1;
        padding: 0.625rem 1rem;
        border-radius: 10px;
        border: none;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .mobile-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .mobile-btn-secondary {
        background: #6c757d;
        color: white;
    }

    .mobile-btn-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    /* Pagination */
    .modern-pagination {
        justify-content: center;
        flex-wrap: wrap;
    }

    .modern-pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .pagination-info {
        text-align: center;
        margin-bottom: 1rem;
    }

    .modern-card-footer .row {
        flex-direction: column-reverse;
    }

    /* Modal */
    .modal-dialog {
        margin: 0.5rem;
    }

    .modal-body {
        padding: 1.25rem;
    }

    /* Budget Display */
    .mobile-meta-item .budget-display {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Timeline */
    .mobile-meta-item .timeline-display {
        font-size: 0.75rem;
    }

    .mobile-meta-item .timeline-display i {
        font-size: 0.625rem;
    }

    /* Team Avatars */
    .mobile-team {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }

    .mobile-team .team-avatar {
        width: 28px;
        height: 28px;
        font-size: 0.625rem;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem 1rem;
    }

    .empty-icon {
        width: 70px;
        height: 70px;
    }

    .empty-icon i {
        font-size: 32px;
    }
}

@media (max-width: 576px) {
    .modern-header h1 {
        font-size: 1.5rem !important;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .mobile-project-meta {
        grid-template-columns: 1fr;
    }

    .mobile-actions {
        flex-direction: column;
    }

    .mobile-btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
let progressModal;

document.addEventListener('DOMContentLoaded', function() {
    progressModal = new bootstrap.Modal(document.getElementById('progressModal'));

    // Update progress value display when slider moves
    const progressRange = document.getElementById('progressRange');
    const progressValue = document.getElementById('progressValue');
    const previewProgressBar = document.getElementById('previewProgressBar');
    const previewProgressText = document.getElementById('previewProgressText');

    progressRange.addEventListener('input', function() {
        const value = this.value;
        progressValue.textContent = value;
        previewProgressBar.style.width = value + '%';
        previewProgressText.textContent = value + '%';

        // Change color based on progress
        if (value < 25) {
            previewProgressBar.className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
        } else if (value < 50) {
            previewProgressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
        } else if (value < 75) {
            previewProgressBar.className = 'progress-bar bg-info progress-bar-striped progress-bar-animated';
        } else {
            previewProgressBar.className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
        }
    });
});

function openProgressModal(projectId, currentProgress, projectName) {
    document.getElementById('projectId').value = projectId;
    document.getElementById('progressRange').value = currentProgress;
    document.getElementById('progressValue').textContent = currentProgress;
    document.getElementById('projectNameLabel').textContent = projectName;

    const previewProgressBar = document.getElementById('previewProgressBar');
    const previewProgressText = document.getElementById('previewProgressText');
    previewProgressBar.style.width = currentProgress + '%';
    previewProgressText.textContent = currentProgress + '%';

    // Set initial color
    if (currentProgress < 25) {
        previewProgressBar.className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
    } else if (currentProgress < 50) {
        previewProgressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
    } else if (currentProgress < 75) {
        previewProgressBar.className = 'progress-bar bg-info progress-bar-striped progress-bar-animated';
    } else {
        previewProgressBar.className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
    }

    progressModal.show();
}

function setProgress(value) {
    document.getElementById('progressRange').value = value;
    document.getElementById('progressValue').textContent = value;

    const previewProgressBar = document.getElementById('previewProgressBar');
    const previewProgressText = document.getElementById('previewProgressText');
    previewProgressBar.style.width = value + '%';
    previewProgressText.textContent = value + '%';

    // Change color based on progress
    if (value < 25) {
        previewProgressBar.className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
    } else if (value < 50) {
        previewProgressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
    } else if (value < 75) {
        previewProgressBar.className = 'progress-bar bg-info progress-bar-striped progress-bar-animated';
    } else {
        previewProgressBar.className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
    }
}

function updateProgress() {
    const projectId = document.getElementById('projectId').value;
    const progress = document.getElementById('progressRange').value;

    fetch(`/projects/${projectId}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ progress: parseInt(progress) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            progressModal.hide();

            // Reload page to show updated progress
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('Error updating progress', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating progress', 'danger');
    });
}

function deleteProject(projectId, projectName) {
    if (confirm(`Are you sure you want to delete "${projectName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/projects/${projectId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
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

    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endpush
