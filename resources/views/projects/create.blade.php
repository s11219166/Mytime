@extends('layouts.app')

@section('title', 'Create Project - MyTime')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <!-- Header Section -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12">
            <div class="p-3 p-md-4 rounded-3" style="background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%); border-top: 4px solid #7c3aed;">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:56px;height:56px;background:linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);color:#fff;box-shadow:0 4px 12px rgba(124,58,237,.3);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h1 class="h4 h-md-3 mb-1" style="color:#5b21b6 !important;">Create New Project</h1>
                            <p class="mb-0" style="color:#6d28d9;"><i class="fas fa-mobile-alt me-2"></i>Fill in the project details below</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 w-100 w-md-auto">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary w-100 w-md-auto">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0" style="color:#6d28d9;"><i class="fas fa-info-circle me-2" style="color:#7c3aed;"></i>Project Details</h5>
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

                    <form method="POST" action="{{ route('projects.store') }}" id="projectForm" novalidate>
                        @csrf

                        <!-- Project Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required 
                                   placeholder="e.g., Marketing Website Revamp" autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Describe the project goals, scope, and deliverables...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dates Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                <div class="form-text">Format: YYYY-MM-DD</div>
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="end_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                <div class="form-text">Must be after the start date</div>
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
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inprogress" {{ old('status') === 'inprogress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review_pending" {{ old('status') === 'review_pending' ? 'selected' : '' }}>Review Pending</option>
                                    <option value="revision_needed" {{ old('status') === 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                    <option value="awaiting_input" {{ old('status') === 'awaiting_input' ? 'selected' : '' }}>Awaiting Input</option>
                                    <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="priority" class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Budget and Tags Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="budget" class="form-label fw-semibold">Budget <span class="text-muted">(Optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-success">$</span>
                                    <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0" placeholder="0.00">
                                </div>
                                @error('budget')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tags" class="form-label fw-semibold">Tags <span class="text-muted">(Comma separated)</span></label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                       id="tags" name="tags" value="{{ old('tags') }}" placeholder="e.g., Web, Design, Q4">
                                @error('tags')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Team Members Section -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Team Members <span class="text-muted">(Optional)</span></label>
                            <div id="teamMembersContainer">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No team members added yet. You can add them after creating the project.
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid d-md-flex justify-content-md-end gap-2 mt-4">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Create Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Tips -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0" style="color:#047857;"><i class="fas fa-lightbulb me-2 text-warning"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Start date defaults to today</li>
                        <li class="mb-2">Due date is required and must be after start date</li>
                        <li class="mb-2">Priority helps with project organization</li>
                        <li>You can add team members after creating the project</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0" style="color:#047857;"><i class="fas fa-mobile-alt me-2 text-success"></i>Mobile Friendly</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2 small">This form is optimized for mobile devices:</p>
                    <ul class="mb-0 ps-3 small">
                        <li>Large tap targets</li>
                        <li>Native date pickers</li>
                        <li>Responsive layout</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card { 
    border-top: 3px solid #7c3aed; 
    border-radius: 1rem; 
}
.form-label { 
    color: #5b21b6; 
}
.form-control:focus, .form-select:focus { 
    border-color: #7c3aed; 
    box-shadow: 0 0 0 .2rem rgba(124,58,237,.15); 
}
.btn-success { 
    background-color: #7c3aed; 
    border-color: #7c3aed; 
}
.btn-success:hover { 
    background-color: #6d28d9; 
    border-color: #6d28d9; 
}
.btn-outline-primary { 
    border-color: #7c3aed; 
    color: #7c3aed; 
}
.btn-outline-primary:hover { 
    background: #7c3aed; 
    color: #fff; 
}

@media (max-width: 576px) {
    .card-body { 
        padding: 1rem; 
    }
    .form-control, .form-select { 
        font-size: 1rem; 
        padding: .65rem .75rem; 
    }
    .btn { 
        padding: .65rem .75rem; 
        font-size: 1rem; 
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const form = document.getElementById('projectForm');
    let isSubmitting = false;
    let submitAttempts = 0;

    // Set minimum end date when start date changes
    function updateEndDateMin() {
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            startDate.setDate(startDate.getDate() + 1);
            const minDate = startDate.toISOString().split('T')[0];
            endDateInput.min = minDate;
            
            // Clear end date if it's before the new minimum
            if (endDateInput.value && endDateInput.value <= startDateInput.value) {
                endDateInput.value = '';
            }
        }
    }

    startDateInput.addEventListener('change', updateEndDateMin);
    updateEndDateMin();

    // Prevent duplicate form submissions - STRICT VERSION
    form.addEventListener('submit', function(e) {
        submitAttempts++;
        
        // Prevent any submission after the first one
        if (isSubmitting || submitAttempts > 1) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.warn('Form submission blocked - already submitting');
            return false;
        }
        
        isSubmitting = true;
        
        // Disable submit button immediately
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.6';
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Project...';
        }
        
        // Disable all form inputs
        const inputs = form.querySelectorAll('input, select, textarea, button');
        inputs.forEach(input => {
            if (input !== submitBtn) {
                input.disabled = true;
            }
        });
        
        // Set a timeout to prevent any further submissions for 5 seconds
        setTimeout(() => {
            isSubmitting = false;
        }, 5000);
    });

    // Also prevent submission on Enter key in input fields
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Prevent double-click on submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        });
    }

    // Auto-focus name field on desktop
    if (window.innerWidth > 768) {
        document.getElementById('name').focus();
    }
});
</script>
@endpush
