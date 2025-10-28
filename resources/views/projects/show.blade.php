@extends('layouts.app')

@section('title', $project->name . ' - Project Details')

@section('content')
<!-- Modern Project Header -->
<div class="project-detail-header mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-start gap-3">
                    <div class="project-detail-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-bold mb-2 text-white">{{ $project->name }}</h1>
                        <p class="text-white-50 mb-0">Detailed overview and project management</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-light">
                        <i class="fas fa-edit me-2"></i>Edit Project
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Project Overview Card -->
        <div class="modern-detail-card mb-4">
            <div class="modern-detail-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Project Overview</h5>
                    <span class="modern-badge badge-{{ $project->status === 'completed' ? 'success' : ($project->status === 'overdue' ? 'danger' : 'primary') }}">
                        <i class="fas {{ $project->status === 'completed' ? 'fa-check-circle' : ($project->status === 'overdue' ? 'fa-exclamation-triangle' : 'fa-rocket') }} me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
            </div>
            <div class="modern-detail-card-body">
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Description</h6>
                    <p class="text-secondary mb-0">{{ $project->description ?? 'No description provided.' }}</p>
                </div>

                @if(!empty($project->tags))
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Tags</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($project->tags as $tag)
                                <span class="tag-pill">
                                    <i class="fas fa-tag me-1"></i>{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Progress Status</h6>
                    <div class="modern-progress-large mb-2">
                        <div class="modern-progress-bar-large modern-progress-{{ $project->progress >= 75 ? 'success' : ($project->progress >= 50 ? 'info' : ($project->progress >= 25 ? 'warning' : 'danger')) }}"
                             style="width: {{ $project->progress }}%">
                            <span class="progress-percentage">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    <small class="text-muted">{{ $project->progress }}% complete</small>
                </div>
            </div>
        </div>

        <!-- Team Members Card -->
        <div class="modern-detail-card">
            <div class="modern-detail-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-users me-2 text-primary"></i>Team Members
                </h5>
            </div>
            <div class="modern-detail-card-body">
                <!-- Project Owner -->
                <div class="team-member-item team-member-owner">
                    <div class="d-flex align-items-center">
                        <div class="team-member-avatar team-member-avatar-primary">
                            <span>{{ substr($project->creator->name, 0, 1) }}</span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="fw-bold mb-0">{{ $project->creator->name }}</div>
                            <small class="text-muted">Project Owner</small>
                        </div>
                        <div class="owner-badge">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>

                @if($project->teamMembers->isEmpty())
                    <div class="text-center py-4">
                        <div class="empty-icon-small mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="text-muted mb-0">No additional team members assigned.</p>
                    </div>
                @else
                    <div class="team-members-list">
                        @foreach($project->teamMembers as $member)
                            <div class="team-member-item">
                                <div class="d-flex align-items-center">
                                    <div class="team-member-avatar team-member-avatar-info">
                                        <span>{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-semibold mb-0">{{ $member->name }}</div>
                                        <small class="text-muted text-capitalize">{{ $member->pivot->role ?? 'member' }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Project Details Card -->
        <div class="modern-detail-card mb-4">
            <div class="modern-detail-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Project Details
                </h5>
            </div>
            <div class="modern-detail-card-body">
                <div class="detail-item">
                    <div class="detail-icon detail-icon-danger">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="detail-content">
                        <small class="detail-label">Priority</small>
                        <div class="detail-value">
                            <span class="priority-badge priority-{{ match($project->priority) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                default => 'secondary'
                            } }}">
                                <i class="fas fa-flag me-1"></i>{{ ucfirst($project->priority) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon detail-icon-success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="detail-content">
                        <small class="detail-label">Budget</small>
                        <div class="detail-value fw-bold text-success">
                            {{ $project->budget ? '$' . number_format($project->budget, 2) : 'Not set' }}
                        </div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon detail-icon-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="detail-content">
                        <small class="detail-label">Start Date</small>
                        <div class="detail-value">{{ $project->start_date->format('M d, Y') }}</div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon detail-icon-danger">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="detail-content">
                        <small class="detail-label">End Date</small>
                        <div class="detail-value">{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon detail-icon-info">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="detail-content">
                        <small class="detail-label">Last Updated</small>
                        <div class="detail-value">{{ $project->updated_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="modern-detail-card danger-zone-card">
            <div class="modern-detail-card-header">
                <h5 class="mb-0 fw-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                </h5>
            </div>
            <div class="modern-detail-card-body">
                <p class="text-muted small mb-3">Deleting a project is irreversible. Make sure this is what you want to do.</p>
                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Project Detail Header */
.project-detail-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
}

.project-detail-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.project-detail-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.project-detail-icon i {
    font-size: 36px;
    color: white;
}

/* Modern Detail Cards */
.modern-detail-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s;
}

.modern-detail-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.modern-detail-card-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.modern-detail-card-body {
    padding: 2rem;
}

/* Tag Pills */
.tag-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1.5px solid rgba(102, 126, 234, 0.3);
    border-radius: 12px;
    color: #667eea;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s;
}

.tag-pill:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
}

/* Modern Progress Large */
.modern-progress-large {
    height: 24px;
    background: #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modern-progress-bar-large {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 0.75rem;
    border-radius: 12px;
    transition: width 1s ease;
    position: relative;
}

.modern-progress-bar-large::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-percentage {
    position: relative;
    z-index: 1;
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.modern-progress-success { background: linear-gradient(90deg, #56ab2f 0%, #a8e063 100%); }
.modern-progress-info { background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%); }
.modern-progress-warning { background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%); }
.modern-progress-danger { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }

/* Team Member Items */
.team-member-item {
    padding: 1.25rem;
    border-radius: 12px;
    transition: all 0.3s;
    margin-bottom: 0.75rem;
}

.team-member-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.team-member-owner {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 2px solid rgba(102, 126, 234, 0.2);
}

.team-member-avatar {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.team-member-avatar-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.team-member-avatar-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

.owner-badge {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
}

.team-members-list {
    margin-top: 1rem;
}

/* Detail Items */
.detail-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
    transition: all 0.3s;
}

.detail-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.detail-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.detail-icon-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.detail-icon-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); }
.detail-icon-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.detail-icon-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

.detail-content {
    flex-grow: 1;
}

.detail-label {
    display: block;
    text-transform: uppercase;
    font-size: 0.7rem;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
}

/* Danger Zone */
.danger-zone-card {
    border: 2px solid rgba(250, 112, 154, 0.2);
}

.danger-zone-card .modern-detail-card-header {
    background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
    border-bottom-color: rgba(250, 112, 154, 0.3);
}

/* Empty State Small */
.empty-icon-small {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon-small i {
    font-size: 32px;
    color: #667eea;
}

/* Modern Badges (reuse from index) */
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

.badge-danger {
    background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
    color: #fa709a;
    border: 1.5px solid #fa709a40;
}

/* Priority Badge (reuse from index) */
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

/* Button Styles */
.btn-light {
    background: white;
    border: none;
    color: #667eea;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.btn-light:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
    color: #667eea;
}

.btn-outline-light {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    color: white;
    transform: translateY(-2px);
}

.btn-danger {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(250, 112, 154, 0.5);
}
</style>
@endpush
