@extends('layouts.app')

@section('title', 'Edit Project - ' . $project->name)

@section('content')
<!-- Header Section -->
<div class="mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102,126,234,.3);">
                    <i class="fas fa-edit" style="font-size: 32px; color: white;"></i>
                </div>
                <div>
                    <h1 class="display-6 fw-bold mb-1" style="color: white;">Edit Project</h1>
                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">Update project details and team assignments</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Project
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Project Information</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('projects.update', $project) }}" id="editProjectForm">
                    @csrf
                    @method('PUT')

                    <!-- Project Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $project->name) }}" required 
                               placeholder="Enter project name">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Enter project description...">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="end_date" class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status and Priority Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Select Status</option>
                                @foreach(['active','inprogress','review_pending','revision_needed','awaiting_input','paused','completed','cancelled'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $project->status) === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="priority" class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                @foreach(['low','medium','high','urgent'] as $priority)
                                    <option value="{{ $priority }}" {{ old('priority', $project->priority) === $priority ? 'selected' : '' }}>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Budget and Progress Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="budget" class="form-label fw-semibold">Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                       id="budget" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0" placeholder="0.00">
                            </div>
                            @error('budget')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="progress" class="form-label fw-semibold">Progress (%)</label>
                            <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                   id="progress" name="progress" value="{{ old('progress', $project->progress) }}" min="0" max="100" placeholder="0">
                            @error('progress')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label fw-semibold">Tags <span class="text-muted">(Comma separated)</span></label>
                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                               id="tags" name="tags" value="{{ old('tags', $project->tags ? implode(',', $project->tags) : '') }}" 
                               placeholder="web development, frontend, react">
                        @error('tags')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Project Owner Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0"><i class="fas fa-crown me-2 text-warning"></i>Project Owner</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 700; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); flex-shrink: 0;">
                        {{ substr($project->creator->name, 0, 1) }}
                    </div>
                    <div class="ms-3">
                        <div class="fw-semibold">{{ $project->creator->name }}</div>
                        <small class="text-muted">Created on {{ $project->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Team Members</h6>
            </div>
            <div class="card-body">
                @if($project->teamMembers->isEmpty())
                    <div class="text-center py-3">
                        <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="font-size: 24px; color: #667eea;"></i>
                        </div>
                        <p class="text-muted small mb-0">No team members assigned to this project.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($project->teamMembers as $member)
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.125rem; font-weight: 700; flex-shrink: 0;">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <div class="ms-2">
                                        <div class="fw-semibold small">{{ $member->name }}</div>
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Set minimum end date when start date changes
    function updateEndDateMin() {
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            startDate.setDate(startDate.getDate() + 1);
            const minDate = startDate.toISOString().split('T')[0];
            endDateInput.min = minDate;
        }
    }

    startDateInput.addEventListener('change', updateEndDateMin);
    updateEndDateMin();
});
</script>
@endpush
