@extends('layouts.app')

@section('title', $project->name . ' - Project Details')

@section('content')
<!-- Header Section -->
<div class="mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102,126,234,.3);">
                    <i class="fas fa-layer-group" style="font-size: 36px; color: white;"></i>
                </div>
                <div>
                    <h1 class="display-6 fw-bold mb-1" style="color: white;">{{ $project->name }}</h1>
                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">Detailed overview and project management</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-light">
                        <i class="fas fa-edit me-2"></i>Edit Project
                    </a>
                @endif
                <a href="{{ route('projects.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Project Overview Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Project Overview</h5>
                    @php
                        $statusColors = [
                            'active' => 'success',
                            'inprogress' => 'primary',
                            'review_pending' => 'warning',
                            'completed' => 'success',
                            'cancelled' => 'dark',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Description -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Description</h6>
                    <p class="text-secondary mb-0">{{ $project->description ?? 'No description provided.' }}</p>
                </div>

                <!-- Tags -->
                @if(!empty($project->tags))
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Tags</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($project->tags as $tag)
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-tag me-1"></i>{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Progress -->
                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Progress Status</h6>
                    <div class="progress mb-2" style="height: 24px;">
                        <div class="progress-bar" style="width: {{ $project->progress }}%">
                            <span style="color: white; font-size: 0.75rem; font-weight: 700;">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    <small class="text-muted">{{ $project->progress }}% complete</small>
                </div>
            </div>
        </div>

        <!-- Team Members Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Team Members</h5>
            </div>
            <div class="card-body">
                <!-- Project Owner -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="d-flex align-items-center">
                        <div style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; font-weight: 700; flex-shrink: 0;">
                            {{ substr($project->creator->name, 0, 1) }}
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="fw-bold mb-0">{{ $project->creator->name }}</div>
                            <small class="text-muted">Project Owner</small>
                        </div>
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.125rem;">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>

                @if($project->teamMembers->isEmpty())
                    <div class="text-center py-4">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="font-size: 32px; color: #667eea;"></i>
                        </div>
                        <p class="text-muted mb-0">No additional team members assigned.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($project->teamMembers as $member)
                            <div class="p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; font-weight: 700; flex-shrink: 0;">
                                        {{ substr($member->name, 0, 1) }}
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Project Details</h5>
            </div>
            <div class="card-body">
                <!-- Priority -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="text-uppercase text-muted small fw-bold mb-2">Priority</div>
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
                </div>

                <!-- Budget -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="text-uppercase text-muted small fw-bold mb-2">Budget</div>
                    <div class="fw-bold text-success">
                        {{ $project->budget ? '$' . number_format($project->budget, 2) : 'Not set' }}
                    </div>
                </div>

                <!-- Start Date -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="text-uppercase text-muted small fw-bold mb-2">Start Date</div>
                    <div>{{ $project->start_date->format('M d, Y') }}</div>
                </div>

                <!-- End Date -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="text-uppercase text-muted small fw-bold mb-2">End Date</div>
                    <div>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</div>
                </div>

                <!-- Last Updated -->
                <div class="p-3 bg-light rounded">
                    <div class="text-uppercase text-muted small fw-bold mb-2">Last Updated</div>
                    <div>{{ $project->updated_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Danger Zone Card -->
        @if(auth()->user()->isAdmin())
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Deleting a project is irreversible. Make sure this is what you want to do.</p>
                    <button type="button" class="btn btn-danger w-100" onclick="deleteProject({{ $project->id }}, '{{ $project->name }}')">
                        <i class="fas fa-trash me-2"></i>Delete Project
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteProject(projectId, projectName) {
    if (confirm(`Are you sure you want to delete "${projectName}"? This action cannot be undone.`)) {
        const deleteBtn = event.target.closest('button');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
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
            console.log('Delete response:', data);
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("projects.index") }}';
                }, 1000);
            } else {
                showToast(data.message || 'Error deleting project', 'danger');
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Delete Project';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting project: ' + error.message, 'danger');
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Delete Project';
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
</script>
@endpush
