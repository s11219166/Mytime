@extends('layouts.app')

@section('title', 'Admin Dashboard - MyTime')

@push('styles')
<link rel="stylesheet" href="/css/admin-green.css">
<style>
    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #256EA6;
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
        <p>Welcome back, {{ Auth::user()->name }}! Monitor system and sessions.</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="card-icon text-success"><i class="fas fa-user-shield"></i></div>
                <h6 class="text-muted">My Sessions Today</h6>
                <h2 class="mb-0">{{ $myTodaySessions ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="card-icon text-primary"><i class="fas fa-layer-group"></i></div>
                <h6 class="text-muted">My Total Sessions</h6>
                <h2 class="mb-0">{{ $myTotalSessions ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="card-icon text-warning"><i class="fas fa-signal"></i></div>
                <h6 class="text-muted">Active Admins (30m)</h6>
                <h2 class="mb-0">{{ $activeAdmins ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="card-icon text-info"><i class="fas fa-globe"></i></div>
                <h6 class="text-muted">Sessions Today (All)</h6>
                <h2 class="mb-0">{{ $globalTodaySessions ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Quick Admin Actions -->
<div class="row mt-4 g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Admin Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('users.index') }}" class="card h-100 text-decoration-none text-dark border-success">
                            <div class="card-body text-center">
                                <div style="font-size: 2.5rem; color: #20c997; margin-bottom: 1rem;">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <h6 class="card-title">Manage Users</h6>
                                <p class="card-text small text-muted">View and manage all users</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('projects.index') }}" class="card h-100 text-decoration-none text-dark border-info">
                            <div class="card-body text-center">
                                <div style="font-size: 2.5rem; color: #0dcaf0; margin-bottom: 1rem;">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <h6 class="card-title">View Projects</h6>
                                <p class="card-text small text-muted">Monitor all projects</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('analytics') }}" class="card h-100 text-decoration-none text-dark border-warning">
                            <div class="card-body text-center">
                                <div style="font-size: 2.5rem; color: #ffc107; margin-bottom: 1rem;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h6 class="card-title">Analytics</h6>
                                <p class="card-text small text-muted">View system analytics</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>System Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Avg Daily Sessions</span>
                    <span class="badge bg-primary">{{ $avgDailySessions ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total Sessions (All)</span>
                    <span class="badge bg-success">{{ $globalTotalSessions ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  // Heartbeat every 30s
  const statusEl = document.getElementById('heartbeatStatus');
  const lastActivity = document.getElementById('lastActivity');
  async function beat(){
    try{
      const res = await fetch('{{ route('admin.session.heartbeat') }}', {
        method:'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
      });
      if(res.ok){
        statusEl.textContent = 'Live';
        statusEl.classList.remove('bg-danger');
        statusEl.classList.add('bg-success');
        lastActivity.textContent = new Date().toLocaleString();
      } else {
        statusEl.textContent = 'Offline';
        statusEl.classList.remove('bg-success');
        statusEl.classList.add('bg-danger');
      }
    }catch(e){
      statusEl.textContent = 'Offline';
      statusEl.classList.remove('bg-success');
      statusEl.classList.add('bg-danger');
    }
  }
  setInterval(beat, 30000);
  beat();

  // Session timer from lastSession.started_at
  const startedAtStr = "{{ optional($lastSession?->started_at)->format('Y-m-d H:i:s') }}";
  const timerEl = document.getElementById('sessionTimer');
  function tick(){
    if(!startedAtStr || startedAtStr.trim() === '') { 
      timerEl.textContent = '00:00:00'; 
      return; 
    }
    try {
      const start = new Date(startedAtStr.replace(' ', 'T') + 'Z');
      if(isNaN(start.getTime())) {
        timerEl.textContent = '00:00:00';
        return;
      }
      const now = new Date();
      let diff = Math.max(0, Math.floor((now - start)/1000));
      const h = String(Math.floor(diff/3600)).padStart(2,'0');
      diff %= 3600;
      const m = String(Math.floor(diff/60)).padStart(2,'0');
      const s = String(diff%60).padStart(2,'0');
      timerEl.textContent = `${h}:${m}:${s}`;
    } catch(e) {
      timerEl.textContent = '00:00:00';
    }
  }
  setInterval(tick, 1000);
  tick();
})();
</script>
@endpush