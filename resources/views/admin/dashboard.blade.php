@extends('layouts.app')

@section('title', 'Admin Dashboard - MyTime')

@push('styles')
<link rel="stylesheet" href="/css/admin-green.css">
<style>
    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #047857;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.body.classList.add('admin-theme');
    });
</script>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome back, {{ Auth::user()->name }}! Manage your MyTime system.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="card-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="card-title">User Management</h5>
                <p class="card-text">Create, edit, and manage user accounts</p>
                <a href="#" class="btn btn-primary">Manage Users</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="card-icon text-success">
                    <i class="fas fa-clock"></i>
                </div>
                <h5 class="card-title">Time Tracking</h5>
                <p class="card-text">Monitor and manage time entries</p>
                <a href="#" class="btn btn-success">View Time Logs</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="card-icon text-warning">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5 class="card-title">Reports</h5>
                <p class="card-text">Generate and view detailed reports</p>
                <a href="{{ route('analytics') }}" class="btn btn-warning">View Reports</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="card-icon text-info">
                    <i class="fas fa-cogs"></i>
                </div>
                <h5 class="card-title">System Settings</h5>
                <p class="card-text">Configure system preferences</p>
                <a href="#" class="btn btn-info">Settings</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-activity me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-plus text-success me-2"></i>
                            New user registered: John Doe
                        </div>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-clock text-primary me-2"></i>
                            Time entry submitted by Jane Smith
                        </div>
                        <small class="text-muted">4 hours ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-alt text-info me-2"></i>
                            Monthly report generated
                        </div>
                        <small class="text-muted">1 day ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="text-primary">25</h3>
                        <small class="text-muted">Total Users</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-success">142</h3>
                        <small class="text-muted">Time Entries</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-warning">8.5h</h3>
                        <small class="text-muted">Avg Daily</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-info">98%</h3>
                        <small class="text-muted">Uptime</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection