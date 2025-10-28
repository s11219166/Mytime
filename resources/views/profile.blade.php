@extends('layouts.app')

@section('title', 'Profile - MyTime')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-user me-2"></i>Profile</h1>
        <p>Manage your account settings and preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="rounded-circle" alt="Profile Picture" id="profileImage" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=90EE90&color=fff" class="rounded-circle" alt="Profile Picture" id="profileImage" style="width: 120px; height: 120px; object-fit: cover;">
                    @endif
                    <button class="btn btn-sm btn-success position-absolute bottom-0 end-0 rounded-circle" style="width: 32px; height: 32px;" onclick="document.getElementById('profileUpload').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                    <form id="profilePhotoForm" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="profileUpload" name="avatar" accept="image/*" style="display: none;">
                    </form>
                </div>
                <h5 class="mb-1">{{ $user->full_name ?? $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                @if($user->isAdmin())
                    <span class="badge admin-badge">Administrator</span>
                @else
                    <span class="badge user-badge">User</span>
                @endif

                <div class="row mt-4">
                    <div class="col-4">
                        <h6 class="mb-0">{{ $profileStats['projects'] }}</h6>
                        <small class="text-muted">Projects</small>
                    </div>
                    <div class="col-4">
                        <h6 class="mb-0">{{ $profileStats['total_time'] }}</h6>
                        <small class="text-muted">Total Time</small>
                    </div>
                    <div class="col-4">
                        <h6 class="mb-0">{{ $profileStats['efficiency'] }}</h6>
                        <small class="text-muted">Efficiency</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('profile.report.download') }}" method="POST" target="_blank">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-2"></i>Export Time Report
                        </button>
                    </form>
                    <a href="{{ route('analytics') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-chart-line me-2"></i>View Analytics
                    </a>
                    <a href="{{ route('profile') }}#preferences" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-cog me-2"></i>Account Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Personal Information</h6>
            </div>
            <div class="card-body">
                <form id="profileForm" action="{{ route('profile.personal.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name ?? '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ $user->phone ?? '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="" disabled {{ empty($user->department) ? 'selected' : '' }}>Select department</option>
                                <option value="Development" {{ ($user->department ?? '') === 'Development' ? 'selected' : '' }}>Development</option>
                                <option value="Design" {{ ($user->department ?? '') === 'Design' ? 'selected' : '' }}>Design</option>
                                <option value="Marketing" {{ ($user->department ?? '') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Sales" {{ ($user->department ?? '') === 'Sales' ? 'selected' : '' }}>Sales</option>
                                <option value="Support" {{ ($user->department ?? '') === 'Support' ? 'selected' : '' }}>Support</option>
                                <option value="Operations" {{ ($user->department ?? '') === 'Operations' ? 'selected' : '' }}>Operations</option>
                                <option value="HR" {{ ($user->department ?? '') === 'HR' ? 'selected' : '' }}>Human Resources</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" value="{{ $user->position ?? '' }}" placeholder="e.g. Senior Developer">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us about yourself...">{{ $user->bio ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.location.reload()">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Security Settings</h6>
            </div>
            <div class="card-body">
                <form id="securityForm" action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and special characters.
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preferences -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Preferences</h6>
            </div>
            <div class="card-body">
                <form id="preferencesForm" action="{{ route('profile.preferences.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="UTC" {{ ($user->timezone ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                                <option value="America/New_York" {{ ($user->timezone ?? '') === 'America/New_York' ? 'selected' : '' }}>UTC-05:00 (Eastern Time)</option>
                                <option value="America/Chicago" {{ ($user->timezone ?? '') === 'America/Chicago' ? 'selected' : '' }}>UTC-06:00 (Central Time)</option>
                                <option value="America/Denver" {{ ($user->timezone ?? '') === 'America/Denver' ? 'selected' : '' }}>UTC-07:00 (Mountain Time)</option>
                                <option value="America/Los_Angeles" {{ ($user->timezone ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>UTC-08:00 (Pacific Time)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_format" class="form-label">Date Format</label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="m/d/Y" {{ ($user->date_format ?? 'm/d/Y') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d/m/Y" {{ ($user->date_format ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="Y-m-d" {{ ($user->date_format ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="time_format" class="form-label">Time Format</label>
                            <select class="form-select" id="time_format" name="time_format">
                                <option value="12" {{ ($user->time_format ?? '12') === '12' ? 'selected' : '' }}>12 Hour (AM/PM)</option>
                                <option value="24" {{ ($user->time_format ?? '') === '24' ? 'selected' : '' }}>24 Hour</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="working_hours" class="form-label">Daily Working Hours</label>
                            <input type="number" class="form-control" id="working_hours" name="working_hours" value="{{ $user->working_hours ?? 8 }}" min="1" max="24">
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Notifications</h6>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" {{ $user->email_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="emailNotifications">
                            Email Notifications
                        </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="projectUpdates" name="project_updates" {{ $user->project_updates ? 'checked' : '' }}>
                        <label class="form-check-label" for="projectUpdates">
                            Project Updates
                        </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="timeReminders" name="time_reminders" {{ $user->time_reminders ? 'checked' : '' }}>
                        <label class="form-check-label" for="timeReminders">
                            Time Tracking Reminders
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="weeklyReports" name="weekly_reports" {{ $user->weekly_reports ? 'checked' : '' }}>
                        <label class="form-check-label" for="weeklyReports">
                            Weekly Reports
                        </label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Save Preferences</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Profile updated</h6>
                            <p class="text-muted mb-1">Updated personal information and preferences</p>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Logged 8 hours</h6>
                            <p class="text-muted mb-1">Completed time tracking for E-commerce Platform project</p>
                            <small class="text-muted">1 day ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Password changed</h6>
                            <p class="text-muted mb-1">Successfully updated account password</p>
                            <small class="text-muted">3 days ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Joined new project</h6>
                            <p class="text-muted mb-1">Added to Mobile App Development team</p>
                            <small class="text-muted">1 week ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    color: #495057;
}

.card-header {
    background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
    color: white;
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.card-header h6 {
    margin: 0;
    font-weight: 500;
}

.btn-success {
    background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #7FDD7F 0%, #28B828 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(50, 205, 50, 0.3);
}

.btn-outline-success {
    color: #32CD32;
    border-color: #32CD32;
}

.btn-outline-success:hover {
    background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
    border-color: #32CD32;
    color: white;
}

.form-check-input:checked {
    background-color: #32CD32;
    border-color: #32CD32;
}

.admin-badge {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
}

.user-badge {
    background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
}

#profileImage {
    border: 4px solid #90EE90;
    box-shadow: 0 4px 12px rgba(50, 205, 50, 0.2);
}

.btn-sm.btn-success {
    box-shadow: 0 2px 6px rgba(50, 205, 50, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
// Profile photo upload
document.getElementById('profileUpload').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        
        // Validate file size (max 2MB)
        if (file.size > 2048 * 1024) {
            showToast('File size must be less than 2MB', 'danger');
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            showToast('Please select an image file', 'danger');
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Submit form
        document.getElementById('profilePhotoForm').submit();
    }
});

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Error updating profile', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating profile', 'danger');
    });
});

// Security form submission
document.getElementById('securityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            this.reset();
        } else {
            const errors = data.errors;
            if (errors) {
                Object.values(errors).forEach(errorArray => {
                    errorArray.forEach(error => showToast(error, 'danger'));
                });
            } else {
                showToast('Error updating password', 'danger');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating password', 'danger');
    });
});

// Preferences form submission
document.getElementById('preferencesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast('Error saving preferences', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error saving preferences', 'danger');
    });
});

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
