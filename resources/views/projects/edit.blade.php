@extends('layouts.app')

@section('title', 'Edit Project - ' . $project->name)

@section('content')
<!-- Modern Page Header -->
<div class="modern-form-header mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-bold mb-1 text-white">Edit Project</h1>
                        <p class="text-white-50 mb-0">Update project details and team assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Project
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="modern-form-card">
            <div class="modern-form-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Project Information
                </h5>
            </div>
            <div class="modern-form-card-body">
                <form method="POST" action="{{ route('projects.update', $project) }}" id="editProjectForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="modern-label">Project Name <span class="text-danger">*</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-project-diagram input-icon"></i>
                                <input type="text" class="modern-input @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $project->name) }}" required placeholder="Enter project name">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="status" class="modern-label">Status <span class="text-danger">*</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-info-circle input-icon"></i>
                                <select class="modern-input @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    @foreach(['active','inprogress','review_pending','revision_needed','awaiting_input','paused','overdue','completed','cancelled','inactive'] as $status)
                                        <option value="{{ $status }}" {{ old('status', $project->status) === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="modern-label">Description</label>
                        <div class="modern-input-group">
                            <i class="fas fa-align-left input-icon"></i>
                            <textarea class="modern-input modern-textarea @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Enter project description...">{{ old('description', $project->description) }}</textarea>
                        </div>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="start_date" class="modern-label">Start Date <span class="text-danger">*</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-calendar-check input-icon"></i>
                                <input type="date" class="modern-input @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
                            </div>
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="end_date" class="modern-label">End Date</label>
                            <div class="modern-input-group">
                                <i class="fas fa-calendar-times input-icon"></i>
                                <input type="date" class="modern-input @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}">
                            </div>
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="priority" class="modern-label">Priority</label>
                            <div class="modern-input-group">
                                <i class="fas fa-flag input-icon"></i>
                                <select class="modern-input" id="priority" name="priority">
                                    @foreach(['low','medium','high','urgent'] as $priority)
                                        <option value="{{ $priority }}" {{ old('priority', $project->priority) === $priority ? 'selected' : '' }}>
                                            {{ ucfirst($priority) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="budget" class="modern-label">Budget</label>
                            <div class="modern-input-group">
                                <i class="fas fa-dollar-sign input-icon"></i>
                                <input type="number" class="modern-input" id="budget" name="budget"
                                       value="{{ old('budget', $project->budget) }}" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="progress" class="modern-label">Progress (%)</label>
                        <div class="modern-input-group">
                            <i class="fas fa-tasks input-icon"></i>
                            <input type="number" class="modern-input @error('progress') is-invalid @enderror" id="progress" name="progress"
                                   value="{{ old('progress', $project->progress) }}" min="0" max="100" placeholder="0">
                        </div>
                        @error('progress')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tags" class="modern-label">Tags</label>
                        <div class="modern-input-group">
                            <i class="fas fa-tags input-icon"></i>
                            <input type="text" class="modern-input" id="tags" name="tags"
                                   value="{{ old('tags', $project->tags ? implode(',', $project->tags) : '') }}" placeholder="web development, frontend, react">
                        </div>
                        <small class="form-text text-muted">Enter tags separated by commas</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary-modern">Cancel</a>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function(){
  const form = document.getElementById('editProjectForm');
  
  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      
      // Disable submit button to prevent multiple submissions
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
      
      const formData = new FormData(form);
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (response.ok) {
          showSuccessMessage('Project updated successfully!');
          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          setTimeout(() => {
            window.location.href = '{{ route("projects.index") }}';
          }, 1000);
        } else if (response.status === 422) {
          return response.json().then(data => {
            showErrorMessage('Please fix the errors below');
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          });
        } else {
          showErrorMessage('An error occurred. Please try again.');
          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred. Please try again.');
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      });
    });
  }

  function showSuccessMessage(message){
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
      <i class="fas fa-check-circle me-2"></i>${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const form = document.getElementById('editProjectForm');
    form.parentElement.insertBefore(alertDiv, form);
    
    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }

  function showErrorMessage(message){
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
      <i class="fas fa-exclamation-circle me-2"></i>${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const form = document.getElementById('editProjectForm');
    form.parentElement.insertBefore(alertDiv, form);
  }
})();
</script>
@endpush

    <div class="col-lg-4">
        <!-- Project Owner Card -->
        <div class="modern-sidebar-card mb-4">
            <div class="modern-sidebar-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-crown me-2 text-warning"></i>Project Owner
                </h5>
            </div>
            <div class="modern-sidebar-card-body">
                <div class="owner-info-card">
                    <div class="d-flex align-items-center">
                        <div class="owner-avatar">
                            <span>{{ substr($project->creator->name, 0, 1) }}</span>
                        </div>
                        <div class="ms-3">
                            <div class="owner-name">{{ $project->creator->name }}</div>
                            <small class="owner-meta">Created on {{ $project->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members Card -->
        <div class="modern-sidebar-card">
            <div class="modern-sidebar-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-users me-2 text-primary"></i>Team Members
                </h5>
            </div>
            <div class="modern-sidebar-card-body">
                @if($project->teamMembers->isEmpty())
                    <div class="text-center py-3">
                        <div class="empty-icon-small mb-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="text-muted small mb-0">No team members assigned to this project.</p>
                    </div>
                @else
                    <div class="team-members-compact">
                        @foreach($project->teamMembers as $member)
                            <div class="team-member-compact">
                                <div class="d-flex align-items-center">
                                    <div class="member-avatar-compact">
                                        <span>{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ms-3">
                                        <div class="member-name-compact">{{ $member->name }}</div>
                                        <small class="member-role-compact">{{ $member->pivot->role ?? 'member' }}</small>
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

@push('styles')
<style>
/* Modern Form Header */
.modern-form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
}

.modern-form-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.form-header-icon {
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

.form-header-icon i {
    font-size: 32px;
    color: white;
}

/* Modern Form Card */
.modern-form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.modern-form-card-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.modern-form-card-body {
    padding: 2rem;
}

/* Modern Labels */
.modern-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Modern Input Group */
.modern-input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 1;
    pointer-events: none;
}

.modern-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 2.75rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.3s;
    background: white;
}

.modern-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modern-input.is-invalid {
    border-color: #dc3545;
}

.modern-textarea {
    resize: vertical;
    min-height: 120px;
}

/* Modern Sidebar Cards */
.modern-sidebar-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.modern-sidebar-card-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.modern-sidebar-card-body {
    padding: 1.5rem;
}

/* Owner Info Card */
.owner-info-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
}

.owner-avatar {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}

.owner-name {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.owner-meta {
    color: #6c757d;
    font-size: 0.8125rem;
}

/* Team Members Compact */
.team-members-compact {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.team-member-compact {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.3s;
}

.team-member-compact:hover {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.05) 0%, rgba(0, 242, 254, 0.05) 100%);
    transform: translateX(5px);
}

.member-avatar-compact {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
    font-weight: 700;
    flex-shrink: 0;
}

.member-name-compact {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.125rem;
}

.member-role-compact {
    color: #6c757d;
    font-size: 0.8125rem;
    text-transform: capitalize;
}

/* Empty Icon Small */
.empty-icon-small {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon-small i {
    font-size: 24px;
    color: #667eea;
}

/* Button Styles */
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

.btn-secondary-modern {
    background: #6c757d;
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-secondary-modern:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    color: white;
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
</style>
@endpush
