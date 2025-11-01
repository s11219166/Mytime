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

<!-- Session Panel and Quick Actions -->
<div class="row mt-4 g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-stopwatch me-2"></i>Current Session</h5>
                <span class="badge bg-success" id="heartbeatStatus">Live</span>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="text-muted">Started</div>
                        <div class="fs-5">{{ optional($lastSession?->started_at)->format('M d, Y h:i A') ?? '—' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-muted">Duration</div>
                        <div class="display-6" id="sessionTimer">00:00:00</div>
                    </div>
                    <div>
                        <div class="text-muted">Last Activity</div>
                        <div class="fs-5" id="lastActivity">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Recent Sessions</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(($recentSessions ?? []) as $s)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-sign-in-alt text-success me-2"></i>
                                <strong>{{ $s->started_at?->format('M d, Y h:i A') ?? $s->created_at->format('M d, Y h:i A') }}</strong>
                                <small class="text-muted ms-2">IP: {{ $s->ip_address ?? 'N/A' }}</small>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">Last: {{ $s->ended_at?->diffForHumans() ?? 'now' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No session history yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Admin Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-success"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-success"><i class="fas fa-project-diagram me-2"></i>View Projects</a>
                    <a href="{{ route('analytics') }}" class="btn btn-outline-success"><i class="fas fa-chart-line me-2"></i>Analytics</a>
                </div>
                <hr>
                <div>
                    <div class="d-flex justify-content-between text-muted">
                        <span>Avg Daily Sessions</span>
                        <span>{{ $avgDailySessions ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span>Total Sessions (All)</span>
                        <span>{{ $globalTotalSessions ?? 0 }}</span>
                    </div>
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