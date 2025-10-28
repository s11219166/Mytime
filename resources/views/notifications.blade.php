@extends('layouts.app')

@section('title', 'Notifications - MyTime')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-bell me-2"></i>Notifications</h1>
            <p>Stay updated with your project activities and reminders</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-2"></i>Mark All Read
            </button>
            <form action="{{ route('notifications.clear-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Clear all read notifications?')">
                    <i class="fas fa-trash me-2"></i>Clear Read
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Notification Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $notifications->total() }}</h3>
                <p class="text-muted mb-0">Total</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ $unreadCount }}</h3>
                <p class="text-muted mb-0">Unread</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $notifications->total() - $unreadCount }}</h3>
                <p class="text-muted mb-0">Read</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger">{{ $notifications->whereIn('type', ['project_due', 'project_overdue'])->count() }}</h3>
                <p class="text-muted mb-0">Due/Overdue</p>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card">
    <div class="card-body p-0">
        @forelse($notifications as $notification)
        <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}" data-type="{{ $notification->type }}" data-id="{{ $notification->id }}">
            <div class="d-flex align-items-start p-3 border-bottom">
                <div class="notification-icon bg-{{ $notification->color }} text-white rounded-circle me-3">
                    <i class="fas {{ $notification->icon }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $notification->title }}</h6>
                            <p class="text-muted mb-1">{{ $notification->message }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                @if(!$notification->is_read)
                                <li><a class="dropdown-item" href="#" onclick="markAsRead({{ $notification->id }})"><i class="fas fa-check me-2"></i>Mark as Read</a></li>
                                @endif
                                @if($notification->project_id)
                                <li><a class="dropdown-item" href="{{ route('projects.show', $notification->project_id) }}"><i class="fas fa-eye me-2"></i>View Project</a></li>
                                @endif
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteNotification({{ $notification->id }})"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center p-5">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No notifications</h5>
            <p class="text-muted">You're all caught up! New notifications will appear here.</p>
        </div>
        @endforelse
    </div>
    
    @if($notifications->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $notifications->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.notification-item.unread {
    background-color: #f0fff4;
    border-left: 4px solid #32CD32;
}

.notification-item.read {
    background-color: #ffffff;
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread:hover {
    background-color: #e6ffe6;
}
</style>
@endpush

@push('scripts')
<script>
function markAllAsRead() {
    fetch('{{ route('notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error marking notifications as read', 'danger');
    });
}

function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error marking notification as read', 'danger');
    });
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                document.querySelector(`[data-id="${id}"]`).remove();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error deleting notification', 'danger');
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
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endpush
