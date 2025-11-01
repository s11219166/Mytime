@extends('layouts.app')

@section('title', 'Create Project - MyTime')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="p-3 p-md-4 rounded-3" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-top: 4px solid #10b981;">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:56px;height:56px;background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,.3);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h1 class="h4 h-md-3 mb-1" style="color:#065f46 !important;">Create New Project</h1>
                            <p class="mb-0" style="color:#047857;"><i class="fas fa-mobile-alt me-2"></i>Mobile-optimized form with proper date handling</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 w-100 w-md-auto">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-success w-100 w-md-auto">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0" style="color:#047857;"><i class="fas fa-info-circle me-2 text-success"></i>Project Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.store') }}" id="projectForm" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Marketing Website Revamp" autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required inputmode="none">
                                <div class="form-text">Format: YYYY-MM-DD</div>
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="end_date" class="form-label fw-semibold">Due Date <span class="text-muted">(Optional)</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" inputmode="none">
                                <div class="form-text">Must be after the start date</div>
                                @error('end_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inprogress" {{ old('status') === 'inprogress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review_pending" {{ old('status') === 'review_pending' ? 'selected' : '' }}>Review Pending</option>
                                    <option value="revision_needed" {{ old('status') === 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                    <option value="awaiting_input" {{ old('status') === 'awaiting_input' ? 'selected' : '' }}>Awaiting Input</option>
                                    <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                                    <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="priority" class="form-label fw-semibold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority','medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label for="budget" class="form-label fw-semibold">Budget <span class="text-muted">(Optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-success">$</span>
                                    <input type="number" class="form-control" id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0" placeholder="0.00" inputmode="decimal">
                                </div>
                                @error('budget')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tags" class="form-label fw-semibold">Tags <span class="text-muted">(Comma separated)</span></label>
                                <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}" placeholder="e.g., Web, Design, Q4">
                                @error('tags')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the project goals, scope, and deliverables...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid d-md-flex justify-content-md-end gap-2 mt-4">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Create Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0" style="color:#047857;"><i class="fas fa-mobile-alt me-2 text-success"></i>Mobile Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li>Use the native date pickers for Start/Due dates.</li>
                        <li>Rotate your phone if the keyboard covers inputs.</li>
                        <li>All fields are mobile-optimized with large tap targets.</li>
                    </ul>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0" style="color:#047857;"><i class="fas fa-lightbulb me-2 text-warning"></i>Hints</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">- Start date defaults to today.</p>
                    <p class="mb-0">- Due date is optional but must be after the start date.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/**** Mobile-first enhancements ****/
.card { border-top: 3px solid #10b981; border-radius: 1rem; }
.form-label { color:#065f46; }
.form-control:focus, .form-select:focus { border-color:#10b981; box-shadow:0 0 0 .2rem rgba(16,185,129,.15); }
.btn-success { background-color:#10b981; border-color:#10b981; }
.btn-outline-success { border-color:#10b981; color:#047857; }
.btn-outline-success:hover { background:#10b981; color:#fff; }

@media (max-width: 576px){
  .card-body{ padding:1rem; }
  .form-control, .form-select{ font-size:1rem; padding:.65rem .75rem; }
  .btn{ padding:.65rem .75rem; font-size:1rem; }
}
</style>
@endpush

@push('scripts')
<script>
(function(){
  const start = document.getElementById('start_date');
  const end = document.getElementById('end_date');
  const form = document.getElementById('projectForm');

  function toYMD(d){
    const dt = new Date(d.getTime() - d.getTimezoneOffset()*60000);
    return dt.toISOString().slice(0,10);
  }

  function addDays(ymd, days){
    const d = new Date(ymd);
    d.setDate(d.getDate()+days);
    return toYMD(d);
  }

  function setMins(){
    if (start && start.value) {
      // due must be strictly after start (server rule: after:start_date)
      end.min = addDays(start.value, 1);
      if (end.value && end.value <= start.value){ end.value = ''; }
    } else {
      end.removeAttribute('min');
    }
  }

  function clearForm(){
    if (form) {
      form.reset();
      // Reset start_date to today
      const today = toYMD(new Date());
      document.getElementById('start_date').value = today;
      // Reset status to active
      document.getElementById('status').value = 'active';
      // Reset priority to medium
      document.getElementById('priority').value = 'medium';
      // Recalculate min date for end_date
      setMins();
    }
  }

  document.addEventListener('DOMContentLoaded', function(){
    // Autofocus name on desktop only to prevent zoom jump on mobile
    if (window.innerWidth > 768) {
      const name = document.getElementById('name');
      if (name) name.focus();
    }
    setMins();

    // Handle form submission with AJAX to clear form on success
    form.addEventListener('submit', function(e){
      // Allow normal form submission but intercept with fetch
      e.preventDefault();
      
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
          // Show success message
          showSuccessMessage('Project created successfully!');
          // Clear the form
          clearForm();
          // Redirect to projects index immediately
          setTimeout(() => {
            window.location.href = '{{ route("projects.index") }}';
          }, 500);
        } else if (response.status === 422) {
          // Validation errors
          return response.json().then(data => {
            showErrorMessage('Please fix the errors below');
            // Re-populate form with old values
            Object.keys(data.errors).forEach(field => {
              const input = document.getElementById(field);
              if (input) {
                input.classList.add('is-invalid');
              }
            });
          });
        } else {
          showErrorMessage('An error occurred. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred. Please try again.');
      });
    });
  });

  function showSuccessMessage(message){
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
      <i class="fas fa-check-circle me-2"></i>${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const form = document.getElementById('projectForm');
    form.parentElement.insertBefore(alertDiv, form);
    
    // Auto-dismiss after 5 seconds
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
    
    const form = document.getElementById('projectForm');
    form.parentElement.insertBefore(alertDiv, form);
  }

  start.addEventListener('change', setMins);
})();
</script>
@endpush
