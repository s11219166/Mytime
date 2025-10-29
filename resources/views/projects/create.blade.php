@extends('layouts.app')

@section('title', 'Add Project - MyTime')

@section('content')
<!-- Modern Page Header -->
<div class="modern-form-header mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-header-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-bold mb-1 text-white">Create New Project</h1>
                        <p class="text-white-50 mb-0">Set up a new project to track time and manage tasks <span class="badge bg-light text-primary ms-2"><i class="fas fa-keyboard me-1"></i>Ctrl+Enter to Save</span></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <button type="button" id="quickAddToggle" class="btn btn-light me-2" onclick="toggleQuickAdd()">
                    <i class="fas fa-bolt me-2"></i><span id="quickAddText">Quick Add</span>
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back
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
                <form method="POST" action="{{ route('projects.store') }}" id="projectForm" novalidate>
                    @csrf
                    <input type="hidden" name="create_another" id="createAnotherField" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="modern-label">Project Name <span class="text-danger">*</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-project-diagram input-icon"></i>
                                <input type="text" class="modern-input @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Enter project name" aria-label="Project Name"
                                       autocomplete="off" autofocus data-validate="required">
                                <span class="validation-icon"><i class="fas fa-check"></i></span>
                            </div>
                            <div class="invalid-feedback">Please enter a project name</div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4 optional-field">
                            <label for="status" class="modern-label">Status <span class="text-muted small">(Optional)</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-info-circle input-icon"></i>
                                <select class="modern-input status-select @error('status') is-invalid @enderror" id="status" name="status" aria-label="Project Status">
                                    <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }} data-color="info">📋 Planning</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }} data-color="success">🚀 Active</option>
                                    <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }} data-color="warning">⏸️ On Hold</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }} data-color="primary">✅ Completed</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 optional-field">
                        <label for="description" class="modern-label d-flex justify-content-between align-items-center">
                            <span>Description <span class="text-muted small">(Optional)</span></span>
                            <small class="text-muted" id="charCount">0 / 1000 characters</small>
                        </label>
                        <div class="modern-input-group">
                            <i class="fas fa-align-left input-icon"></i>
                            <textarea class="modern-input modern-textarea auto-resize @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3" maxlength="1000"
                                      placeholder="Describe the project goals, scope, and key deliverables..."
                                      aria-label="Project Description">{{ old('description') }}</textarea>
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
                                <input type="text" class="modern-input date-input @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                                       required placeholder="Type 'today', 'tomorrow' or select date"
                                       aria-label="Start Date" data-validate="required" autocomplete="off">
                                <input type="date" class="modern-input date-picker" id="start_date_picker" style="display:none;">
                                <span class="validation-icon"><i class="fas fa-check"></i></span>
                            </div>
                            <div class="date-quick-actions mt-2">
                                <button type="button" class="btn-quick-date" onclick="setDate('start_date', 0)">Today</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('start_date', 1)">Tomorrow</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('start_date', 7)">Next Week</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('start_date', 30)">Next Month</button>
                            </div>
                            <div class="invalid-feedback">Please select a start date</div>
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4 optional-field">
                            <label for="end_date" class="modern-label">End Date <span class="text-muted small">(Optional)</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-calendar-times input-icon"></i>
                                <input type="text" class="modern-input date-input @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}"
                                       placeholder="Type 'tomorrow', 'next week' or select"
                                       aria-label="End Date" autocomplete="off">
                                <input type="date" class="modern-input date-picker" id="end_date_picker" style="display:none;">
                            </div>
                            <div class="date-quick-actions mt-2">
                                <button type="button" class="btn-quick-date" onclick="setDate('end_date', 7)">1 Week</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('end_date', 14)">2 Weeks</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('end_date', 30)">1 Month</button>
                                <button type="button" class="btn-quick-date" onclick="setDate('end_date', 90)">3 Months</button>
                            </div>
                            <div class="invalid-feedback">End date must be after start date</div>
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row optional-field">
                        <div class="col-md-6 mb-4">
                            <label for="priority" class="modern-label">Priority <span class="text-muted small">(Optional)</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-flag input-icon"></i>
                                <select class="modern-input" id="priority" name="priority" aria-label="Priority Level">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🔵 Low</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="budget" class="modern-label">Budget <span class="text-muted small">(Optional)</span></label>
                            <div class="modern-input-group">
                                <i class="fas fa-dollar-sign input-icon"></i>
                                <input type="number" class="modern-input" id="budget" name="budget"
                                       value="{{ old('budget') }}" step="0.01" min="0"
                                       placeholder="Enter project budget" aria-label="Budget">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 optional-field">
                        <label for="tags" class="modern-label">Tags <span class="text-muted small">(Optional)</span></label>
                        <div class="modern-input-group">
                            <i class="fas fa-tags input-icon"></i>
                            <input type="text" class="modern-input" id="tags-input"
                                   placeholder="Type and press Enter or comma to add tags"
                                   aria-label="Tags" autocomplete="off">
                            <input type="hidden" id="tags" name="tags" value="{{ old('tags') }}">
                        </div>
                        <div id="tags-container" class="tags-pills-container mt-2"></div>
                        <div id="tags-suggestions" class="tags-autocomplete"></div>
                        <small class="form-text text-muted">Common tags: Web Development, Mobile App, Design, Backend, Frontend</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-5 pt-4 border-top">
                        <button type="button" class="btn btn-secondary-modern" onclick="handleCancel()" title="Press Escape">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary-modern" onclick="submitForm(true)">
                                <i class="fas fa-plus me-2"></i>Create & Add Another
                            </button>
                            <button type="submit" class="btn btn-gradient-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Create Project
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Tips Card -->
        <div class="modern-sidebar-card mb-4">
            <div class="modern-sidebar-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>Quick Tips
                </h5>
            </div>
            <div class="modern-sidebar-card-body">
                <div class="tip-item">
                    <div class="tip-icon tip-icon-primary">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <div class="tip-content">
                        <h6 class="tip-title">Keyboard Shortcuts</h6>
                        <p class="tip-description"><kbd>Ctrl+Enter</kbd> to save • <kbd>Esc</kbd> to cancel • <kbd>Tab</kbd> to navigate</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon tip-icon-info">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="tip-content">
                        <h6 class="tip-title">Natural Dates</h6>
                        <p class="tip-description">Type "tomorrow", "next week", or use quick action buttons for dates.</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon tip-icon-success">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="tip-content">
                        <h6 class="tip-title">Smart Tags</h6>
                        <p class="tip-description">Press <kbd>Enter</kbd> or <kbd>,</kbd> to add tags. Auto-complete will suggest common tags.</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon tip-icon-warning">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="tip-content">
                        <h6 class="tip-title">Quick Add Mode</h6>
                        <p class="tip-description">Toggle Quick Add to show only essential fields for faster project creation.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects Card -->
        <div class="modern-sidebar-card">
            <div class="modern-sidebar-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i>Recent Projects
                </h5>
            </div>
            <div class="modern-sidebar-card-body" id="recentProjects">
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-clock fa-2x mb-2 opacity-50"></i>
                    <p class="small mb-0">Your recent projects will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// State management
let tags = [];
let isQuickAddMode = false;
const commonTags = ['Web Development', 'Mobile App', 'Design', 'Backend', 'Frontend', 'API', 'Database', 'Testing', 'DevOps', 'UI/UX'];
let recentProjects = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on project name
    document.getElementById('name').focus();

    // Load tags from old input
    const oldTags = document.getElementById('tags').value;
    if (oldTags) {
        tags = oldTags.split(',').map(t => t.trim()).filter(t => t);
        renderTags();
    }

    // Load recent projects from localStorage
    loadRecentProjects();

    // Initialize character counter
    updateCharCount();

    // Setup auto-resize textarea
    setupAutoResize();

    // Setup inline validation
    setupValidation();

    // Setup keyboard shortcuts
    setupKeyboardShortcuts();

    // Setup date inputs
    setupDateInputs();

    // Setup tags input
    setupTagsInput();

    // Setup form submission
    setupFormSubmission();
});

// Natural language date parser
function parseNaturalDate(input) {
    const today = new Date();
    const lower = input.toLowerCase().trim();

    if (lower === 'today') return today;
    if (lower === 'tomorrow') {
        today.setDate(today.getDate() + 1);
        return today;
    }
    if (lower === 'next week') {
        today.setDate(today.getDate() + 7);
        return today;
    }
    if (lower === 'next month') {
        today.setMonth(today.getMonth() + 1);
        return today;
    }

    // Try parsing as regular date
    const parsed = new Date(input);
    return isNaN(parsed.getTime()) ? null : parsed;
}

// Format date to YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Set date field
function setDate(fieldId, daysFromToday) {
    const today = new Date();
    today.setDate(today.getDate() + daysFromToday);
    const formatted = formatDate(today);
    document.getElementById(fieldId).value = formatted;
    validateField(document.getElementById(fieldId));
}

// Setup date inputs
function setupDateInputs() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    // Handle natural language input
    [startDate, endDate].forEach(field => {
        field.addEventListener('blur', function() {
            const parsed = parseNaturalDate(this.value);
            if (parsed) {
                this.value = formatDate(parsed);
                validateField(this);
            }
        });

        field.addEventListener('change', function() {
            validateDates();
        });
    });
}

// Validate dates
function validateDates() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (end < start) {
            endDate.classList.add('is-invalid');
            endDate.nextElementSibling?.classList.add('d-block');
            return false;
        } else {
            endDate.classList.remove('is-invalid');
            endDate.nextElementSibling?.classList.remove('d-block');
        }
    }
    return true;
}

// Setup tags input
function setupTagsInput() {
    const tagsInput = document.getElementById('tags-input');
    const suggestionsBox = document.getElementById('tags-suggestions');

    // Add tag on Enter or comma
    tagsInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(this.value.trim());
            this.value = '';
            suggestionsBox.innerHTML = '';
        }
    });

    // Show autocomplete suggestions
    tagsInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        if (value.length < 2) {
            suggestionsBox.innerHTML = '';
            return;
        }

        const matches = commonTags.filter(tag =>
            tag.toLowerCase().includes(value) && !tags.includes(tag)
        );

        if (matches.length > 0) {
            suggestionsBox.innerHTML = matches.map(tag =>
                `<div class="tag-suggestion" onclick="addTag('${tag}'); document.getElementById('tags-input').value = ''; document.getElementById('tags-suggestions').innerHTML = '';">${tag}</div>`
            ).join('');
        } else {
            suggestionsBox.innerHTML = '';
        }
    });
}

// Add tag
function addTag(tag) {
    if (!tag || tags.includes(tag)) return;
    tags.push(tag);
    renderTags();
    document.getElementById('tags').value = tags.join(',');
}

// Remove tag
function removeTag(tag) {
    tags = tags.filter(t => t !== tag);
    renderTags();
    document.getElementById('tags').value = tags.join(',');
}

// Render tags
function renderTags() {
    const container = document.getElementById('tags-container');
    container.innerHTML = tags.map(tag =>
        `<span class="tag-pill-removable">
            <i class="fas fa-tag me-1"></i>${tag}
            <button type="button" class="tag-remove" onclick="removeTag('${tag}')" aria-label="Remove ${tag}">
                <i class="fas fa-times"></i>
            </button>
        </span>`
    ).join('');
}

// Setup auto-resize textarea
function setupAutoResize() {
    const textarea = document.getElementById('description');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        updateCharCount();
    });
}

// Update character count
function updateCharCount() {
    const textarea = document.getElementById('description');
    const counter = document.getElementById('charCount');
    const count = textarea.value.length;
    counter.textContent = `${count} / 1000 characters`;

    if (count > 900) {
        counter.classList.add('text-danger');
    } else if (count > 700) {
        counter.classList.add('text-warning');
        counter.classList.remove('text-danger');
    } else {
        counter.classList.remove('text-warning', 'text-danger');
    }
}

// Setup inline validation
function setupValidation() {
    const form = document.getElementById('projectForm');
    const inputs = form.querySelectorAll('[data-validate="required"]');

    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => validateField(input));
    });
}

// Validate individual field
function validateField(field) {
    const isValid = field.checkValidity() && field.value.trim() !== '';

    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        field.parentElement.querySelector('.validation-icon')?.classList.add('show');
    } else {
        field.classList.remove('is-valid');
        field.parentElement.querySelector('.validation-icon')?.classList.remove('show');
    }

    return isValid;
}

// Setup keyboard shortcuts
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('projectForm').requestSubmit();
        }

        // Escape to cancel
        if (e.key === 'Escape') {
            e.preventDefault();
            handleCancel();
        }
    });
}

// Setup form submission
function setupFormSubmission() {
    const form = document.getElementById('projectForm');

    form.addEventListener('submit', function(e) {
        // Validate all required fields
        const requiredFields = form.querySelectorAll('[data-validate="required"]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Validate dates
        if (!validateDates()) {
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });
}

// Submit form
function submitForm(createAnother = false) {
    document.getElementById('createAnotherField').value = createAnother ? '1' : '0';
    document.getElementById('projectForm').requestSubmit();
}

// Handle cancel
function handleCancel() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
        window.location.href = '{{ route('projects.index') }}';
    }
}

// Toggle quick add mode
function toggleQuickAdd() {
    isQuickAddMode = !isQuickAddMode;
    const optionalFields = document.querySelectorAll('.optional-field');
    const toggleBtn = document.getElementById('quickAddText');

    optionalFields.forEach(field => {
        if (isQuickAddMode) {
            field.style.display = 'none';
            toggleBtn.textContent = 'Show All Fields';
        } else {
            field.style.display = '';
            toggleBtn.textContent = 'Quick Add';
        }
    });
}

// Load recent projects
function loadRecentProjects() {
    const stored = localStorage.getItem('recentProjects');
    if (stored) {
        recentProjects = JSON.parse(stored);
        renderRecentProjects();
    }
}

// Render recent projects
function renderRecentProjects() {
    const container = document.getElementById('recentProjects');
    if (recentProjects.length === 0) return;

    container.innerHTML = recentProjects.slice(0, 5).map(project => `
        <div class="recent-project-item">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${project.name}</h6>
                    <small class="text-muted">${project.status || 'Planning'}</small>
                </div>
                <button type="button" class="btn-duplicate" onclick="duplicateProject(${project.id})" title="Duplicate this project">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    `).join('');
}

// Duplicate project
function duplicateProject(projectId) {
    const project = recentProjects.find(p => p.id === projectId);
    if (!project) return;

    // Fill form with project data
    document.getElementById('name').value = project.name + ' (Copy)';
    document.getElementById('description').value = project.description || '';
    document.getElementById('status').value = project.status || 'planning';
    document.getElementById('priority').value = project.priority || 'medium';
    document.getElementById('budget').value = project.budget || '';

    if (project.tags) {
        tags = project.tags.split(',').map(t => t.trim()).filter(t => t);
        renderTags();
        document.getElementById('tags').value = tags.join(',');
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    document.getElementById('name').focus();
    document.getElementById('name').select();
}

// Show success message after creation (if redirected back)
@if(session('success'))
    setTimeout(() => {
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.innerHTML = `
            <div class="success-content">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h5>{{ session('success') }}</h5>
                <div class="mt-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-light me-2">View All Projects</a>
                    <button class="btn btn-sm btn-primary" onclick="this.closest('.success-notification').remove()">Create Another</button>
                </div>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 100);
    }, 500);
@endif
</script>
@endpush

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

/* Kbd styling */
kbd {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Modern Form Card */
.modern-form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s;
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

/* Modern Input Group with Validation */
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
    transition: all 0.3s;
}

.modern-input {
    width: 100%;
    padding: 0.875rem 3rem 0.875rem 2.75rem;
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

.modern-input:focus + .validation-icon,
.modern-input:focus ~ .input-icon {
    color: #667eea;
}

.modern-input.is-valid {
    border-color: #28a745;
    padding-right: 3rem;
}

.modern-input.is-valid ~ .input-icon {
    color: #28a745;
}

.modern-input.is-invalid {
    border-color: #dc3545;
    animation: shake 0.3s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Validation Icon */
.validation-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #28a745;
    opacity: 0;
    transition: all 0.3s;
    pointer-events: none;
}

.validation-icon.show {
    opacity: 1;
    animation: checkmark 0.3s ease-in-out;
}

@keyframes checkmark {
    0% { transform: translateY(-50%) scale(0); }
    50% { transform: translateY(-50%) scale(1.2); }
    100% { transform: translateY(-50%) scale(1); }
}

/* Auto-resize Textarea */
.modern-textarea {
    resize: none;
    min-height: 80px;
    max-height: 300px;
    overflow-y: auto;
    transition: height 0.2s;
}

.auto-resize {
    transition: height 0.2s ease;
}

/* Date Input Enhancements */
.date-input {
    cursor: text;
}

.date-quick-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-quick-date {
    padding: 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #495057;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-quick-date:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

/* Status Select with Color Indicators */
.status-select option {
    padding: 0.5rem;
}

/* Tags System */
.tags-pills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    min-height: 40px;
}

.tag-pill-removable {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1.5px solid rgba(102, 126, 234, 0.3);
    border-radius: 12px;
    color: #667eea;
    font-size: 0.8125rem;
    font-weight: 500;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.tag-remove {
    background: none;
    border: none;
    color: #667eea;
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
}

.tag-remove:hover {
    background: #667eea;
    color: white;
}

.tags-autocomplete {
    position: relative;
    margin-top: 0.5rem;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.tag-suggestion {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.3s;
    border-bottom: 1px solid #f8f9fa;
}

.tag-suggestion:last-child {
    border-bottom: none;
}

.tag-suggestion:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    color: #667eea;
}

/* Optional Field Styling */
.optional-field {
    transition: all 0.3s;
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

/* Tip Items */
.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    background: #f8f9fa;
    transition: all 0.3s;
}

.tip-item:last-child {
    margin-bottom: 0;
}

.tip-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateX(5px);
}

.tip-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.tip-icon-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.tip-icon-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.tip-icon-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); }
.tip-icon-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

.tip-content {
    flex-grow: 1;
}

.tip-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.tip-description {
    font-size: 0.8125rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
}

/* Recent Projects */
.recent-project-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    transition: all 0.3s;
}

.recent-project-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateX(5px);
}

.btn-duplicate {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: none;
    color: #667eea;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-duplicate:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: scale(1.1);
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

.btn-outline-primary-modern {
    background: white;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-outline-primary-modern:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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

.btn-light {
    background: white;
    border: none;
    color: #667eea;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-light:hover {
    background: #f8f9fa;
    color: #667eea;
    transform: translateY(-2px);
}

/* Success Notification */
.success-notification {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    opacity: 0;
    transition: all 0.3s;
    max-width: 500px;
    text-align: center;
}

.success-notification.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.success-notification::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: -1;
}

.success-content i {
    color: #28a745;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-form-header {
        padding: 1.5rem;
    }

    .form-header-icon {
        width: 50px;
        height: 50px;
    }

    .form-header-icon i {
        font-size: 24px;
    }

    .modern-form-card-body {
        padding: 1.5rem;
    }

    .date-quick-actions {
        gap: 0.25rem;
    }

    .btn-quick-date {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Focus visible for accessibility */
*:focus-visible {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Loading state */
.btn-gradient-primary:disabled,
.btn-secondary-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush
