<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyTime')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 300;
        }

        .sidebar.collapsed .sidebar-header h3 {
            display: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 1rem;
            text-align: center;
        }

        .sidebar.collapsed .sidebar-menu span {
            display: none;
        }

        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.2rem;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        .user-badge {
            background: linear-gradient(135deg, #90EE90 0%, #32CD32 100%);
            color: white;
        }

        .admin-badge {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
        }

        .content-wrapper {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #495057;
            font-weight: 300;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #6c757d;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-item.unread {
            background-color: #f0fff4;
        }

        .notification-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        #notificationDropdownMenu {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            #notificationDropdownMenu {
                width: 300px;
                right: 0;
                left: auto;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-clock me-2"></i>MyTime</h3>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li>
                <a href="{{ route('projects.create') }}" class="{{ request()->routeIs('projects.create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i>
                    <span>Add Project</span>
                </a>
            </li>
            <li>
                <a href="{{ route('analytics') }}" class="{{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li>
                <a href="{{ route('time-logs.index') }}" class="{{ request()->routeIs('time-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Time Logs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('notifications') }}" class="{{ request()->routeIs('notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="{{ route('financial.index') }}" class="{{ request()->routeIs('financial.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Financial</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inspiration') }}" class="{{ request()->routeIs('inspiration') ? 'active' : '' }}">
                    <i class="fas fa-star-half-alt"></i>
                    <span>Inspiration Hub</span>
                </a>
            </li>
            <li>
                <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            @if(Auth::user()->isAdmin())
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i>
                    <span>Admin Panel</span>
                </a>
            </li>
            @endif
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-nav ms-auto">
                    <!-- Notification Dropdown -->
                    <div class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge d-none" id="notificationCount">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="notificationDropdownMenu">
                            <li class="dropdown-header">Notifications</li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notificationList">
                                <!-- Notifications will be loaded here -->
                                <li class="text-center p-3">
                                    <i class="fas fa-bell-slash text-muted"></i>
                                    <p class="mb-0 text-muted">No new notifications</p>
                                </li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center" href="{{ route('notifications') }}">
                                    <i class="fas fa-envelope me-2"></i>View All Notifications
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            {{ Auth::user()->name }}
                            @if(Auth::user()->isAdmin())
                                <span class="badge admin-badge ms-2">Admin</span>
                            @else
                                <span class="badge user-badge ms-2">User</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Real-time notification functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch initial notification count
            fetchNotificationCount();

            // Fetch initial notifications
            fetchLatestNotifications();

            // Set up polling for real-time updates (every 30 seconds)
            setInterval(fetchNotificationCount, 30000);
            setInterval(fetchLatestNotifications, 30000);

            // Also check when dropdown is opened
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function() {
                    fetchLatestNotifications();
                });
            }
        });

        function fetchNotificationCount() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationCount');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching notification count:', error);
                });
        }

        function fetchLatestNotifications() {
            fetch('/notifications/latest')
                .then(response => response.json())
                .then(data => {
                    updateNotificationDropdown(data.notifications, data.unread_count);
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
        }

        function updateNotificationDropdown(notifications, unreadCount) {
            const notificationList = document.getElementById('notificationList');
            const badge = document.getElementById('notificationCount');

            // Update badge
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }

            // Update notification list
            if (notificationList) {
                if (notifications.length > 0) {
                    notificationList.innerHTML = notifications.map(notification => `
                        <li class="notification-item ${notification.is_read ? 'read' : 'unread'}">
                            <a class="dropdown-item" href="${notification.project_id ? '/projects/' + notification.project_id : '#'}" data-notification-id="${notification.id}">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon bg-${notification.color} text-white rounded-circle me-2">
                                        <i class="fas ${notification.icon}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${notification.title}</h6>
                                        <p class="mb-1 small text-muted">${notification.message}</p>
                                        <small class="text-muted">${notification.created_at}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    `).join('');
                } else {
                    notificationList.innerHTML = `
                        <li class="text-center p-3">
                            <i class="fas fa-bell-slash text-muted"></i>
                            <p class="mb-0 text-muted">No new notifications</p>
                        </li>
                    `;
                }
            }
        }

        // Mark notification as read when clicked
        document.addEventListener('click', function(e) {
            if (e.target.closest('.notification-item')) {
                const notificationItem = e.target.closest('.notification-item');
                const notificationLink = notificationItem.querySelector('a');
                const notificationId = notificationLink ? notificationLink.getAttribute('data-notification-id') : null;

                if (notificationId && !notificationItem.classList.contains('read')) {
                    // Send request to mark as read
                    fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            notificationItem.classList.remove('unread');
                            notificationItem.classList.add('read');

                            // Update badge count
                            const badge = document.getElementById('notificationCount');
                            if (badge && !badge.classList.contains('d-none')) {
                                let count = parseInt(badge.textContent);
                                if (count > 1) {
                                    badge.textContent = count - 1;
                                } else {
                                    badge.classList.add('d-none');
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
