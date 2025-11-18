@extends('layouts.app')

@section('title', 'Dashboard - MyTime')

@push('styles')
<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #06b6d4;
        --purple: #8b5cf6;
        --dark: #1f2937;
        --light: #f3f4f6;
    }

    body {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdfa 100%);
        min-height: 100vh;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
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

    .dashboard-header-content {
        position: relative;
        z-index: 1;
    }

    .dashboard-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .dashboard-header p {
        font-size: 1rem;
        opacity: 0.95;
        margin: 0;
    }

    .stat-card {
        background: white;
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-left: 4px solid var(--card-color, var(--primary));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-card.primary { --card-color: var(--primary); }
    .stat-card.success { --card-color: var(--success); }
    .stat-card.danger { --card-color: var(--danger); }
    .stat-card.warning { --card-color: var(--warning); }
    .stat-card.info { --card-color: var(--info); }
    .stat-card.purple { --card-color: var(--purple); }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(var(--card-color-rgb, 59, 130, 246), 0.1);
        color: var(--card-color, var(--primary));
    }

    .stat-title {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .stat-description {
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: var(--primary);
        font-size: 1.5rem;
    }

    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
        text-align: center;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .quick-action-btn.primary {
        background: linear-gradient(135deg, var(--primary), #1e40af);
    }

    .quick-action-btn.success {
        background: linear-gradient(135deg, var(--success), #047857);
    }

    .quick-action-btn.warning {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .quick-action-btn.info {
        background: linear-gradient(135deg, var(--info), #0e7490);
    }

    .admin-banner {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .admin-banner-content {
        flex: 1;
    }

    .admin-banner-content h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .admin-banner-content p {
        margin: 0;
        opacity: 0.95;
        font-size: 0.95rem;
    }

    .admin-banner i {
        font-size: 2rem;
    }

    .admin-banner-btn {
        background: white;
        color: #f59e0b;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }

    .admin-banner-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .list-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
    }

    .list-card-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .list-card-header i {
        color: var(--primary);
    }

    .list-item {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: var(--light);
        padding-left: 1.5rem;
    }

    .list-item-title {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .list-item-meta {
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .badge-primary { background: rgba(59, 130, 246, 0.2); color: #1e40af; }
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #065f46; }
    .badge-danger { background: rgba(239, 68, 68, 0.2); color: #7f1d1d; }
    .badge-warning { background: rgba(245, 158, 11, 0.2); color: #92400e; }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .admin-action-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .admin-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .admin-action-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .admin-action-title {
        font-weight: 700;
        color: var(--dark);
    }

    .admin-action-desc {
        font-size: 0.85rem;
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem 1rem;
        }

        .dashboard-header h1 {
            font-size: 1.5rem;
        }

        .card-grid {
            grid-template-columns: 1fr;
        }

        .admin-banner {
            flex-direction: column;
            text-align: center;
        }

        .admin-banner-btn {
            width: 100%;
        }

        .admin-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-header-content">
            <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p><i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, F j, Y') }} • {{ now()->format('g:i A') }}</p>
        </div>
    </div>

    @if(Auth::user()->isAdmin())
        <!-- ADMIN DASHBOARD -->
        
        <!-- Admin Banner -->
        <div class="admin-banner">
            <div>
                <i class="fas fa-crown"></i>
            </div>
            <div class="admin-banner-content">
                <h3>Admin Access</h3>
                <p>You have full system access and can manage all users and projects.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="admin-banner-btn">
                <i class="fas fa-arrow-right me-2"></i>Admin Panel
            </a>
        </div>

        <!-- Admin Stats -->
        <div class="section-title">
            <i class="fas fa-chart-bar"></i>System Overview
        </div>

        <div class="admin-stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Users</div>
                        <div class="stat-value">{{ $adminStats['total_users'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-description">Active users in the system</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Projects</div>
                        <div class="stat-value">{{ $adminStats['total_projects'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
                <div class="stat-description">All projects in the system</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Active Projects</div>
                        <div class="stat-value">{{ $adminStats['active_projects'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
                <div class="stat-description">Currently in progress</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Overdue Projects</div>
                        <div class="stat-value">{{ $adminStats['overdue_projects'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
                <div class="stat-description">Past their due date</div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="section-title">
            <i class="fas fa-bolt"></i>Quick Actions
        </div>

        <div class="card-grid">
            <a href="{{ route('users.index') }}" class="admin-action-card">
                <div class="admin-action-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="admin-action-title">Manage Users</div>
                <div class="admin-action-desc">View and manage all users</div>
            </a>

            <a href="{{ route('projects.index') }}" class="admin-action-card">
                <div class="admin-action-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="admin-action-title">View Projects</div>
                <div class="admin-action-desc">Monitor all projects</div>
            </a>

            <a href="{{ route('analytics') }}" class="admin-action-card">
                <div class="admin-action-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="admin-action-title">Analytics</div>
                <div class="admin-action-desc">View system analytics</div>
            </a>

            <a href="{{ route('financial.index') }}" class="admin-action-card">
                <div class="admin-action-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--purple);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="admin-action-title">Financial</div>
                <div class="admin-action-desc">Manage finances</div>
            </a>
        </div>

    @else
        <!-- USER DASHBOARD -->

        <!-- User Stats -->
        <div class="section-title">
            <i class="fas fa-chart-line"></i>Your Statistics
        </div>

        <div class="card-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Active Projects</div>
                        <div class="stat-value">{{ $stats['active_projects'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="stat-description">Projects in progress</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Completed</div>
                        <div class="stat-value">{{ $stats['completed_projects'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-description">Finished projects</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Performance</div>
                        <div class="stat-value">{{ $stats['completion_rate'] ?? 0 }}%</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div class="stat-description">Overall completion rate</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Time Tracked</div>
                        <div class="stat-value">{{ $stats['total_hours'] ?? 0 }}h</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-description">Total hours this month</div>
            </div>
        </div>

        <!-- Today's Stats -->
        <div class="section-title">
            <i class="fas fa-calendar-day"></i>Today's Activity
        </div>

        <div class="card-grid">
            <div class="stat-card purple">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Sessions</div>
                        <div class="stat-value">{{ $todayStats['sessions'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                <div class="stat-description">Work sessions today</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Total Time</div>
                        <div class="stat-value">{{ $todayStats['total_time'] ?? '0h' }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                </div>
                <div class="stat-description">Hours worked today</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Avg Session</div>
                        <div class="stat-value">{{ $todayStats['avg_session'] ?? '0m' }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                </div>
                <div class="stat-description">Average session duration</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div>
                        <div class="stat-title">Notifications</div>
                        <div class="stat-value">{{ $stats['unread_notifications'] ?? 0 }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
                <div class="stat-description">Unread messages</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-title">
            <i class="fas fa-bolt"></i>Quick Actions
        </div>

        <div class="card-grid">
            <a href="{{ route('projects.create') }}" class="quick-action-btn primary">
                <i class="fas fa-plus"></i>New Project
            </a>
            <a href="{{ route('projects.index') }}" class="quick-action-btn success">
                <i class="fas fa-tasks"></i>View Projects
            </a>
            <a href="{{ route('financial.index') }}" class="quick-action-btn warning">
                <i class="fas fa-wallet"></i>Financial
            </a>
            <a href="{{ route('time-logs.index') }}" class="quick-action-btn info">
                <i class="fas fa-clock"></i>Time Logs
            </a>
        </div>

        <!-- Upcoming Due Projects -->
        <div class="section-title">
            <i class="fas fa-calendar-check"></i>Upcoming Due
        </div>

        <div class="list-card">
            <div id="upcomingProjects">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="section-title">
            <i class="fas fa-bell"></i>Recent Notifications
        </div>

        <div class="list-card">
            <div id="recentNotifications">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>

    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!Auth::user()->isAdmin())
    // Load upcoming projects
    function loadUpcomingProjects() {
        fetch('/api/upcoming-projects')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('upcomingProjects');
                if (data.projects && data.projects.length > 0) {
                    container.innerHTML = data.projects.map(project => `
                        <a href="/projects/${project.id}" class="list-item">
                            <div class="list-item-title">
                                ${project.name}
                                <span class="badge ${project.days_remaining <= 1 ? 'badge-danger' : project.days_remaining <= 3 ? 'badge-warning' : 'badge-primary'}">
                                    ${project.days_remaining <= 0 ? 'OVERDUE' : project.days_remaining + 'd'}
                                </span>
                            </div>
                            <div class="list-item-meta">
                                <i class="fas fa-calendar me-1"></i>${project.days_remaining} days remaining
                            </div>
                        </a>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-check-circle"></i><p>No upcoming due projects</p></div>';
                }
            })
            .catch(error => {
                console.error('Error loading upcoming projects:', error);
                document.getElementById('upcomingProjects').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Error loading projects</p></div>';
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
                        <a href="${notification.project_id ? '/projects/' + notification.project_id : '/notifications'}" class="list-item">
                            <div class="list-item-title">${notification.title}</div>
                            <div class="list-item-meta">
                                <i class="fas fa-clock me-1"></i>${notification.created_at}
                            </div>
                        </a>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('recentNotifications').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Error loading notifications</p></div>';
            });
    }

    // Load data on page load
    loadUpcomingProjects();
    loadRecentNotifications();

    // Refresh every 60 seconds
    setInterval(loadUpcomingProjects, 60000);
    setInterval(loadRecentNotifications, 60000);
    @endif
});
</script>
@endpush
