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
            background: linear-gradient(135deg, #f0fdf4 0%, #f0f9ff 100%);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(16, 185, 129, 0.3);
            overflow-y: auto;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #34d399, #6ee7b7, #a7f3d0);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 2rem 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            position: relative;
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #34d399, transparent);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .sidebar-header i {
            color: #a7f3d0;
            margin-right: 0.5rem;
        }

        .sidebar.collapsed .sidebar-header h3 {
            display: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .sidebar-menu li:last-child {
            border-bottom: none;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 1.2rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #34d399, #6ee7b7);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            color: white;
            padding-left: 1.5rem;
        }

        .sidebar-menu a:hover::before {
            transform: scaleY(1);
        }

        .sidebar-menu a.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.1) 100%);
            color: #a7f3d0;
            font-weight: 600;
            border-right: 3px solid #34d399;
        }

        .sidebar-menu a.active::before {
            transform: scaleY(1);
        }

        .sidebar-menu i {
            width: 24px;
            margin-right: 1rem;
            text-align: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            color: #34d399;
            transform: scale(1.15);
        }

        .sidebar.collapsed .sidebar-menu span {
            display: none;
        }

        .main-content {
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .navbar {
            background: linear-gradient(90deg, #ffffff 0%, #f0fdf4 100%) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
            border: none;
            border-bottom: 2px solid #d1fae5;
            position: relative;
        }

        .navbar::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
        }

        .toggle-sidebar {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: none;
            color: #047857;
            font-size: 1.3rem;
            padding: 0.6rem 0.8rem;
            margin-right: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        .toggle-sidebar:hover {
            background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .user-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .admin-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .content-wrapper {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #047857;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .page-header p {
            color: #059669;
            margin: 0;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
            transition: all 0.3s ease;
            border-top: 3px solid #10b981;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        }

        .notification-item.unread {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #047857;
        }

        #notificationDropdownMenu {
            width: 380px;
            max-height: 450px;
            overflow-y: auto;
            border: 1px solid #d1fae5;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
        }

        #notificationDropdownMenu .dropdown-header {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 700;
            border-radius: 12px 12px 0 0;
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
    <script src="/js/push-notifications.js"></script>
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
