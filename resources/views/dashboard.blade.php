@extends('layouts.app')

@section('title', 'Dashboard - MyTime')

@push('styles')
<style>
    * {
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #06b6d4;
        --purple: #8b5cf6;
        --pink: #ec4899;
        --indigo: #6366f1;
    }

    body {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdfa 100%);
        min-height: 100vh;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 20px;
        margin-bottom: 3rem;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .dashboard-header-content {
        position: relative;
        z-index: 1;
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: none;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), transparent);
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary { --card-color: var(--primary); }
    .stat-card.success { --card-color: var(--success); }
    .stat-card.danger { --card-color: var(--danger); }
    .stat-card.warning { --card-color: var(--warning); }
    .stat-card.info { --card-color: var(--info); }
    .stat-card.purple { --card-color: var(--purple); }
    .stat-card.pink { --card-color: var(--pink); }
    .stat-card.indigo { --card-color: var(--indigo); }

    .stat-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--card-color), rgba(var(--card-color-rgb), 0.1));
        color: var(--card-color);
    }

    .stat-title {
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .stat-description {
        font-size: 0.9rem;
        color: #9ca3af;
        margin-bottom: 1.5rem;
    }

    .stat-button {
        align-self: flex-start;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        background: linear-gradient(135deg, var(--card-color), rgba(var(--card-color-rgb), 0.8));
        color: white;
        text-decoration: none;
        display: inline-block;
    }

    .stat-button:hover {
        transform: translateX(5px);
        box-shadow: 0 10px 20px rgba(var(--card-color-rgb), 0.3);
    }

    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .quick-stat-item {
        background: rgba(var(--card-color-rgb), 0.1);
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
    }

    .quick-stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--card-color);
        margin-bottom: 0.5rem;
    }

    .quick-stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 2rem;
        background: linear-gradient(180deg, var(--primary), var(--info));
        border-radius: 2px;
    }

    .sidebar-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: none;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .sidebar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    .sidebar-card-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .sidebar-card-header i {
        font-size: 1.3rem;
        color: var(--primary);
    }

    .list-item {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: rgba(59, 130, 246, 0.05);
        padding-left: 1.5rem;
    }

    .list-item-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .list-item-meta {
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-primary { background: rgba(59, 130, 246, 0.2); color: #1e40af; }
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .badge-danger { background: rgba(239, 68, 68, 0.2); color: #7f1d1d; }
    .badge-warning { background: rgba(245, 158, 11, 0.2); color: #92400e; }

    .admin-redirect-banner {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .admin-redirect-banner i {
        font-size: 2rem;
    }

    .admin-redirect-content {
        flex: 1;
    }

    .admin-redirect-content h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .admin-redirect-content p {
        margin: 0;
        opacity: 0.95;
    }

    .admin-redirect-button {
        background: white;
        color: #f59e0b;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .admin-redirect-button:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 2rem 1.5rem;
        }

        .dashboard-header h1 {
            font-size: 1.8rem;
        }

        .stat-card {
            padding: 1.5rem;
            min-height: 180px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }

        .stat-value {
            font-size: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
        }

        .admin-redirect-banner {
            flex-direction: column;
            text-align: center;
        }
    }

    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .floating-animation {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Admin Redirect Banner -->
    @if(Auth::user()->isAdmin())
    <div class="admin-redirect-banner floating-animation">
        <div>
            <i class="fas fa-crown"></i>
        </div>
        <div class="admin-redirect-content">
            <h3>Welcome, Admin!</h3>
            <p>You have access to the admin dashboard with advanced features and controls.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="admin-redirect-button">
            <i class="fas fa-arrow-right me-2"></i>Go to Admin Dashboard
        </a>
    </div>
    @endif

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-header-content">
            <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p><i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, F j, Y') }} • {{ now()->format('g:i A') }}</p>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="row">
        <div class="col-lg-8">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i>Your Dashboard
            </h2>

            <div class="row">
                <!-- Projects Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card primary">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, rgba(59, 130, 246, 0.1)); color: #3b82f6;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="stat-title">Active Projects</div>
                            <div class="stat-value">{{ $stats['active_projects'] ?? 0 }}</div>
                            <div class="stat-description">Manage your projects and track progress</div>
                        </div>
                        <a href="{{ route('projects.index') }}" class="stat-button" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">
                            <i class="fas fa-arrow-right me-2"></i>View Projects
                        </a>
                    </div>
                </div>

                <!-- Analytics Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card info">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, rgba(6, 182, 212, 0.1)); color: #06b6d4;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-title">Performance</div>
                            <div class="stat-value">{{ $stats['completion_rate'] ?? 0 }}%</div>
                            <div class="stat-description">Your overall completion rate</div>
                        </div>
                        <a href="{{ route('analytics') }}" class="stat-button" style="background: linear-gradient(135deg, #06b6d4, #0e7490);">
                            <i class="fas fa-arrow-right me-2"></i>View Analytics
                        </a>
                    </div>
                </div>

                <!-- Financial Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card warning">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, rgba(245, 158, 11, 0.1)); color: #f59e0b;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-title">Financial Status</div>
                            <div class="stat-value">${{ number_format($stats['total_balance'] ?? 0, 2) }}</div>
                            <div class="stat-description">Your current financial balance</div>
                        </div>
                        <a href="{{ route('financial.index') }}" class="stat-button" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-arrow-right me-2"></i>View Financial
                        </a>
                    </div>
                </div>

                <!-- Time Logs Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card success">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, rgba(16, 185, 129, 0.1)); color: #10b981;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-title">Time Tracked</div>
                            <div class="stat-value">{{ $stats['total_hours'] ?? 0 }}h</div>
                            <div class="stat-description">Total hours logged this month</div>
                        </div>
                        <a href="{{ route('time-logs.index') }}" class="stat-button" style="background: linear-gradient(135deg, #10b981, #047857);">
                            <i class="fas fa-arrow-right me-2"></i>View Time Logs
                        </a>
                    </div>
                </div>

                <!-- Notifications Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card danger">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, rgba(239, 68, 68, 0.1)); color: #ef4444;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="stat-title">Notifications</div>
                            <div class="stat-value">{{ $stats['unread_notifications'] ?? 0 }}</div>
                            <div class="stat-description">Unread messages and alerts</div>
                        </div>
                        <a href="{{ route('notifications') }}" class="stat-button" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-arrow-right me-2"></i>View Notifications
                        </a>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="col-md-6 col-lg-6">
                    <div class="stat-card purple">
                        <div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, rgba(139, 92, 246, 0.1)); color: #8b5cf6;">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="stat-title">Account Settings</div>
                            <div class="stat-value">{{ Auth::user()->role ?? 'User' }}</div>
                            <div class="stat-description">Manage your profile and preferences</div>
                        </div>
                        <a href="{{ route('profile') }}" class="stat-button" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                            <i class="fas fa-arrow-right me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Today's Stats -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <i class="fas fa-calendar-day"></i>Today's Stats
                </div>
                <div class="quick-stats">
                    <div class="quick-stat-item">
                        <div class="quick-stat-value">{{ $todayStats['sessions'] ?? 0 }}</div>
                        <div class="quick-stat-label">Sessions</div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="quick-stat-value">{{ $todayStats['total_time'] ?? '0h' }}</div>
                        <div class="quick-stat-label">Total Time</div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="quick-stat-value">{{ $todayStats['avg_session'] ?? '0m' }}</div>
                        <div class="quick-stat-label">Avg Session</div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Due Projects -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <i class="fas fa-exclamation-circle"></i>Upcoming Due
                </div>
                <div id="upcomingProjects">
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <i class="fas fa-bell"></i>Recent Notifications
                </div>
                <div id="recentNotifications">
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <i class="fas fa-lightning-bolt"></i>Quick Actions
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('projects.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6, #1e40af); border: none; padding: 0.75rem; border-radius: 10px; color: white; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-plus me-2"></i>New Project
                    </a>
                    <a href="{{ route('financial.index') }}" class="btn btn-warning" style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 0.75rem; border-radius: 10px; color: white; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-wallet me-2"></i>Financial Dashboard
                    </a>
                    <a href="{{ route('inspiration') }}" class="btn btn-success" style="background: linear-gradient(135deg, #10b981, #047857); border: none; padding: 0.75rem; border-radius: 10px; color: white; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-lightbulb me-2"></i>Get Inspired
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Features Grid -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="section-title">
                <i class="fas fa-star"></i>More Features
            </h2>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card indigo">
                <div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1, rgba(99, 102, 241, 0.1)); color: #6366f1;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-title">Calendar</div>
                    <div class="stat-description">View your schedule and deadlines</div>
                </div>
                <a href="#" class="stat-button" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                    <i class="fas fa-arrow-right me-2"></i>Open
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card pink">
                <div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, rgba(236, 72, 153, 0.1)); color: #ec4899;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-title">Reports</div>
                    <div class="stat-description">Generate and download reports</div>
                </div>
                <a href="#" class="stat-button" style="background: linear-gradient(135deg, #ec4899, #be185d);">
                    <i class="fas fa-arrow-right me-2"></i>View
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="--card-color: #14b8a6;">
                <div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #14b8a6, rgba(20, 184, 166, 0.1)); color: #14b8a6;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-title">Team</div>
                    <div class="stat-description">Collaborate with team members</div>
                </div>
                <a href="#" class="stat-button" style="background: linear-gradient(135deg, #14b8a6, #0d9488);">
                    <i class="fas fa-arrow-right me-2"></i>View
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="--card-color: #f97316;">
                <div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f97316, rgba(249, 115, 22, 0.1)); color: #f97316;">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="stat-title">Settings</div>
                    <div class="stat-description">Configure your preferences</div>
                </div>
                <a href="{{ route('profile') }}" class="stat-button" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    <i class="fas fa-arrow-right me-2"></i>Open
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load upcoming projects
    function loadUpcomingProjects() {
        fetch('/api/upcoming-projects')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('upcomingProjects');
                if (data.projects && data.projects.length > 0) {
                    container.innerHTML = data.projects.map(project => `
                        <a href="/projects/${project.id}" class="list-item" style="text-decoration: none; color: inherit;">
                            <div class="list-item-title">${project.name}</div>
                            <div class="list-item-meta">
                                <i class="fas fa-calendar me-1"></i>${project.days_remaining} days remaining
                                <span class="badge-custom ${project.days_remaining <= 1 ? 'badge-danger' : project.days_remaining <= 3 ? 'badge-warning' : 'badge-primary'}" style="float: right;">
                                    ${project.days_remaining <= 0 ? 'OVERDUE' : project.days_remaining + 'd'}
                                </span>
                            </div>
                        </a>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center p-3 text-muted"><i class="fas fa-check-circle"></i> No upcoming due projects</div>';
                }
            })
            .catch(error => {
                console.error('Error loading upcoming projects:', error);
                document.getElementById('upcomingProjects').innerHTML = '<div class="text-center p-3 text-danger">Error loading projects</div>';
            });
    }

    // Load recent notifications
    function loadRecentNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recentNotifications');
                if (data.notifications && data.notifications.length > 0) {
                    container.innerHTML = data.notifications.slice(0, 5).map(notification => `
                        <a href="${notification.project_id ? '/projects/' + notification.project_id : '/notifications'}" class="list-item" style="text-decoration: none; color: inherit;">
                            <div class="list-item-title">${notification.title}</div>
                            <div class="list-item-meta">
                                <i class="fas fa-clock me-1"></i>${notification.created_at}
                            </div>
                        </a>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center p-3 text-muted"><i class="fas fa-bell-slash"></i> No notifications</div>';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('recentNotifications').innerHTML = '<div class="text-center p-3 text-danger">Error loading notifications</div>';
            });
    }

    // Load data on page load
    loadUpcomingProjects();
    loadRecentNotifications();

    // Refresh every 60 seconds
    setInterval(loadUpcomingProjects, 60000);
    setInterval(loadRecentNotifications, 60000);
});
</script>
@endpush
