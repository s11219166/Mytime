@extends('layouts.app')

@section('title', 'Analytics Dashboard - MyTime')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.analytics-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.stat-trend {
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

.chart-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.period-selector {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 0.5rem;
    border: none;
    font-weight: 500;
}

.productivity-score {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 2rem auto;
}

.score-circle {
    transform: rotate(-90deg);
}

.score-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.score-number {
    font-size: 3rem;
    font-weight: bold;
    line-height: 1;
}

.score-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    margin-right: 0.5rem;
}

.progress-item {
    margin-bottom: 1.5rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.modern-progress {
    height: 12px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.modern-progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}
</style>
@endpush

@section('content')
<!-- Header -->
<div class="analytics-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h1>
            <p class="mb-0 opacity-90">Comprehensive insights into your productivity and performance</p>
        </div>
        <div>
            <form method="GET" action="{{ route('analytics') }}" class="d-inline">
                <select name="period" class="period-selector" onchange="this.form.submit()">
                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                </select>
            </form>
        </div>
    </div>
</div>

<!-- Key Performance Metrics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value" style="color: #667eea;">{{ number_format($totalHours, 1) }}h</div>
            <div class="stat-label">Total Hours Tracked</div>
            <div class="stat-trend text-success">
                <i class="fas fa-arrow-up"></i> {{ number_format($avgDailyHours, 1) }}h daily average
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-value" style="color: #f5576c;">{{ $activeProjects }}</div>
            <div class="stat-label">Active Projects</div>
            <div class="stat-trend text-muted">
                <i class="fas fa-folder"></i> {{ $totalProjects }} total projects
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value" style="color: #00f2fe;">{{ number_format($productivityScore, 0) }}%</div>
            <div class="stat-label">Productivity Score</div>
            <div class="stat-trend {{ $productivityScore >= 70 ? 'text-success' : 'text-warning' }}">
                <i class="fas fa-{{ $productivityScore >= 70 ? 'smile' : 'meh' }}"></i>
                {{ $productivityScore >= 80 ? 'Excellent' : ($productivityScore >= 60 ? 'Good' : 'Needs Improvement') }}
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-value" style="color: #fa709a;">{{ $currentStreak }}</div>
            <div class="stat-label">Day Streak</div>
            <div class="stat-trend text-primary">
                <i class="fas fa-trophy"></i> Keep it up!
            </div>
        </div>
    </div>
</div>

<!-- Time Tracking Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-chart-area me-2 text-primary"></i>Daily Time Tracking</h5>
            </div>
            <div class="chart-container" style="height: 350px;">
                <canvas id="dailyTimeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-chart-pie me-2 text-success"></i>Project Distribution</h5>
            </div>
            <div class="chart-container">
                <canvas id="projectPriorityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Project Completion Card (Enhanced) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border: 2px solid #10b981; box-shadow: 0 8px 30px rgba(16, 185, 129, 0.15);">
            <div class="chart-header" style="border-bottom: 2px solid #10b981; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h5 class="chart-title" style="color: #10b981; font-size: 1.35rem;">
                    <i class="fas fa-chart-line me-2"></i>Daily Project Completions
                    <span style="font-size: 0.75rem; font-weight: normal; color: #6c757d; margin-left: 0.5rem;">(Last 30 Days Trend)</span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); font-size: 0.875rem; padding: 0.5rem 1rem;">
                        <i class="fas fa-check-double me-1"></i>{{ $completedProjects }} Total Completed
                    </span>
                    <span class="badge bg-info" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                        <i class="fas fa-calculator me-1"></i>{{ number_format($completedProjects / max($period, 1), 1) }} per day avg
                    </span>
                </div>
            </div>
            <div class="chart-container" style="height: 350px;">
                <canvas id="projectCompletionChart"></canvas>
            </div>
            <div class="mt-3 pt-3 border-top" style="border-color: rgba(16, 185, 129, 0.2) !important;">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="stat-mini">
                            <i class="fas fa-trophy text-warning" style="font-size: 1.5rem;"></i>
                            <h5 class="mb-0 mt-2" style="color: #10b981;">{{ max($dailyCompletions->pluck('count')->toArray()) }}</h5>
                            <small class="text-muted">Peak Day</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini">
                            <i class="fas fa-calendar-check text-success" style="font-size: 1.5rem;"></i>
                            <h5 class="mb-0 mt-2" style="color: #10b981;">{{ $dailyCompletions->where('count', '>', 0)->count() }}</h5>
                            <small class="text-muted">Active Days</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini">
                            <i class="fas fa-chart-bar text-info" style="font-size: 1.5rem;"></i>
                            <h5 class="mb-0 mt-2" style="color: #10b981;">{{ number_format(array_sum($dailyCompletions->pluck('count')->toArray()) / max($dailyCompletions->count(), 1), 1) }}</h5>
                            <small class="text-muted">Daily Average</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Analytics -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-tasks me-2 text-info"></i>Project Status Overview</h5>
            </div>
            <div class="chart-container">
                <canvas id="projectStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-clock me-2 text-warning"></i>Time by Project</h5>
            </div>
            <div class="chart-container">
                <canvas id="timeByProjectChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Breakdown -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-calendar-week me-2 text-purple"></i>Weekly Activity Heatmap</h5>
            </div>
            <div class="chart-container">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-bell me-2 text-danger"></i>Notifications</h5>
            </div>
            <div class="chart-container">
                <canvas id="notificationsChart"></canvas>
            </div>
        </div>
    </div>
<!-- Hourly Distribution & Top Projects -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-business-time me-2 text-primary"></i>Work Hours Distribution</h5>
            </div>
            <div class="chart-container">
                <canvas id="hourlyDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-trophy me-2 text-warning"></i>Top Performing Projects</h5>
            </div>
            <div class="p-3">
                @forelse($topProjects as $index => $project)
                <div class="progress-item">
                    <div class="progress-label">
                        <div>
                            <i class="fas fa-medal text-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'brown') }}"></i>
                            <strong>{{ $project->name }}</strong>
                        </div>
                        <span class="text-primary">{{ $project->progress }}%</span>
                    </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar"
                             style="width: {{ $project->progress }}%; background: linear-gradient(90deg,
                             {{ $project->progress >= 80 ? '#10b981' : ($project->progress >= 50 ? '#3b82f6' : '#f59e0b') }} 0%,
                             {{ $project->progress >= 80 ? '#34d399' : ($project->progress >= 50 ? '#60a5fa' : '#fbbf24') }} 100%);"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-4">No active projects found</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Budget & Completion Metrics -->
<div class="row mb-4">
    <div class="col-lg-4 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-dollar-sign me-2 text-success"></i>Budget Overview</h5>
            </div>
            <div class="text-center py-4">
                <div class="mb-4">
                    <h2 class="text-success mb-1">${{ number_format($totalBudget, 2) }}</h2>
                    <p class="text-muted">Total Project Budget</p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h4 class="text-primary">${{ number_format($activeBudget, 2) }}</h4>
                        <small class="text-muted">Active</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-muted">${{ number_format($totalBudget - $activeBudget, 2) }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-percentage me-2 text-info"></i>Completion Rate</h5>
            </div>
            <div class="productivity-score">
                <svg class="score-circle" width="200" height="200">
                    <circle cx="100" cy="100" r="90" fill="none" stroke="#e9ecef" stroke-width="12"/>
                    <circle cx="100" cy="100" r="90" fill="none"
                            stroke="url(#gradient)"
                            stroke-width="12"
                            stroke-dasharray="{{ 2 * pi() * 90 }}"
                            stroke-dashoffset="{{ 2 * pi() * 90 * (1 - $avgProgress / 100) }}"
                            stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                </svg>
                <div class="score-text">
                    <div class="score-number" style="color: #667eea;">{{ number_format($avgProgress, 0) }}%</div>
                    <div class="score-label">Average Progress</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="chart-card">
            <div class="chart-header">
                <h5 class="chart-title"><i class="fas fa-chart-bar me-2 text-danger"></i>Project Stats</h5>
            </div>
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>Completed</span>
                    </div>
                    <h4 class="mb-0 text-success">{{ $completedProjects }}</h4>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        <span>Overdue</span>
                    </div>
                    <h4 class="mb-0 text-danger">{{ $overdueProjects }}</h4>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <i class="fas fa-bell text-warning me-2"></i>
                        <span>Unread Notifications</span>
                    </div>
                    <h4 class="mb-0 text-warning">{{ $unreadNotifications }}</h4>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-envelope-open text-info me-2"></i>
                        <span>Read Rate</span>
                    </div>
                    <h4 class="mb-0 text-info">{{ $readRate }}%</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chart.js default configuration
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6c757d';

// Color schemes
const colors = {
    primary: '#667eea',
    secondary: '#764ba2',
    success: '#10b981',
    danger: '#ef4444',
    warning: '#f59e0b',
    info: '#3b82f6',
    purple: '#8b5cf6',
    pink: '#ec4899',
    gradient1: ['#667eea', '#764ba2'],
    gradient2: ['#f093fb', '#f5576c'],
    gradient3: ['#4facfe', '#00f2fe'],
    gradient4: ['#43e97b', '#38f9d7'],
    gradient5: ['#fa709a', '#fee140']
};

// ===== 1. DAILY TIME TRACKING CHART =====
const dailyTimeCtx = document.getElementById('dailyTimeChart').getContext('2d');
const dailyTimeData = @json($dailyTime);
const dailyLabels = dailyTimeData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const dailyHours = dailyTimeData.map(d => (d.total_minutes / 60).toFixed(1));

const gradient1 = dailyTimeCtx.createLinearGradient(0, 0, 0, 400);
gradient1.addColorStop(0, 'rgba(102, 126, 234, 0.3)');
gradient1.addColorStop(1, 'rgba(102, 126, 234, 0.01)');

new Chart(dailyTimeCtx, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Hours Worked',
            data: dailyHours,
            borderColor: colors.primary,
            backgroundColor: gradient1,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#fff',
            pointBorderColor: colors.primary,
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return 'Hours: ' + context.parsed.y + 'h';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: { callback: value => value + 'h' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// ===== 2. PROJECT PRIORITY DISTRIBUTION =====
const priorityCtx = document.getElementById('projectPriorityChart').getContext('2d');
const priorityData = @json($projectsByPriority);
const priorityLabels = Object.keys(priorityData).map(p => p.charAt(0).toUpperCase() + p.slice(1));
const priorityValues = Object.values(priorityData);

new Chart(priorityCtx, {
    type: 'doughnut',
    data: {
        labels: priorityLabels,
        datasets: [{
            data: priorityValues,
            backgroundColor: [
                colors.success,
                colors.warning,
                colors.danger,
                colors.purple
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, usePointStyle: true }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});

// ===== 2B. PROJECT COMPLETION DAILY CHART (ENHANCED LINE GRAPH) =====
const completionCtx = document.getElementById('projectCompletionChart').getContext('2d');
const completionData = @json($dailyCompletions);
const completionLabels = completionData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const completionCounts = completionData.map(d => d.count);

// Create beautiful gradient for area fill
const gradientCompletion = completionCtx.createLinearGradient(0, 0, 0, 300);
gradientCompletion.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
gradientCompletion.addColorStop(0.5, 'rgba(16, 185, 129, 0.2)');
gradientCompletion.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

new Chart(completionCtx, {
    type: 'line',
    data: {
        labels: completionLabels,
        datasets: [{
            label: 'Projects Completed',
            data: completionCounts,
            borderColor: '#10b981',
            backgroundColor: gradientCompletion,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#10b981',
            pointBorderWidth: 3,
            pointHoverBorderWidth: 4,
            pointHoverBackgroundColor: '#10b981',
            pointStyle: 'circle',
            // Add shadow effect
            shadowOffsetX: 0,
            shadowOffsetY: 4,
            shadowBlur: 10,
            shadowColor: 'rgba(16, 185, 129, 0.3)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 12,
                        weight: '600'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                padding: 16,
                cornerRadius: 12,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                displayColors: true,
                borderColor: '#10b981',
                borderWidth: 2,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        const count = context.parsed.y;
                        return ' ' + count + ' project' + (count !== 1 ? 's' : '') + ' completed';
                    },
                    afterLabel: function(context) {
                        // Calculate cumulative total up to this date
                        let cumulative = 0;
                        for (let i = 0; i <= context.dataIndex; i++) {
                            cumulative += completionCounts[i];
                        }
                        return 'Total so far: ' + cumulative + ' projects';
                    }
                }
            },
            annotation: {
                annotations: {
                    line1: {
                        type: 'line',
                        yMin: Math.max(...completionCounts) > 0 ? (completionCounts.reduce((a, b) => a + b, 0) / completionCounts.length) : 0,
                        yMax: Math.max(...completionCounts) > 0 ? (completionCounts.reduce((a, b) => a + b, 0) / completionCounts.length) : 0,
                        borderColor: 'rgba(102, 126, 234, 0.5)',
                        borderWidth: 2,
                        borderDash: [10, 5],
                        label: {
                            display: true,
                            content: 'Average',
                            position: 'end',
                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                            color: '#fff',
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 6,
                            borderRadius: 6
                        }
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    stepSize: 1,
                    callback: value => Math.floor(value),
                    font: {
                        size: 11,
                        weight: '500'
                    },
                    color: '#6c757d'
                },
                title: {
                    display: true,
                    text: 'Number of Projects',
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    color: '#495057'
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11,
                        weight: '500'
                    },
                    color: '#6c757d',
                    maxRotation: 45,
                    minRotation: 0
                },
                title: {
                    display: true,
                    text: 'Date',
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    color: '#495057'
                }
            }
        },
        animation: {
            duration: 1500,
            easing: 'easeInOutQuart',
            onComplete: function() {
                // Animation complete callback
            }
        }
    }
});

// ===== 3. PROJECT STATUS CHART =====
const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
const statusData = @json($projectsByStatus);
const statusLabels = Object.keys(statusData).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
const statusValues = Object.values(statusData);

new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: statusLabels,
        datasets: [{
            label: 'Projects',
            data: statusValues,
            backgroundColor: colors.gradient1.map((c, i) => {
                const ctx = statusCtx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, c);
                gradient.addColorStop(1, colors.gradient1[1]);
                return gradient;
            })[0],
            borderRadius: 8,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: { stepSize: 1 }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// ===== 4. TIME BY PROJECT CHART =====
const timeProjectCtx = document.getElementById('timeByProjectChart').getContext('2d');
const timeByProject = @json($timeByProject);
const projectNames = timeByProject.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name);
const projectHours = timeByProject.map(p => (p.total_minutes / 60).toFixed(1));

new Chart(timeProjectCtx, {
    type: 'horizontalBar',
    data: {
        labels: projectNames,
        datasets: [{
            label: 'Hours',
            data: projectHours,
            backgroundColor: [
                colors.primary,
                colors.success,
                colors.warning,
                colors.info,
                colors.purple,
                colors.pink,
                colors.danger
            ],
            borderRadius: 8
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: context => context.parsed.x + ' hours'
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: { callback: value => value + 'h' }
            },
            y: {
                grid: { display: false }
            }
        }
    }
});

// ===== 5. WEEKLY ACTIVITY CHART =====
const weeklyCtx = document.getElementById('weeklyActivityChart').getContext('2d');
const weeklyData = @json($weeklyTime);
const weeklyLabels = weeklyData.map(d => d.day.substring(0, 3));
const weeklyHours = weeklyData.map(d => d.hours);

const gradient2 = weeklyCtx.createLinearGradient(0, 0, 0, 300);
gradient2.addColorStop(0, colors.gradient2[0]);
gradient2.addColorStop(1, colors.gradient2[1]);

new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: weeklyLabels,
        datasets: [{
            label: 'Daily Hours',
            data: weeklyHours,
            backgroundColor: weeklyHours.map(h => {
                if (h >= 8) return colors.success;
                if (h >= 6) return colors.info;
                if (h >= 4) return colors.warning;
                return colors.danger;
            }),
            borderRadius: 8,
            barThickness: 50
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: context => context.parsed.y + ' hours'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 12,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: { callback: value => value + 'h' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// ===== 6. NOTIFICATIONS CHART =====
const notifCtx = document.getElementById('notificationsChart').getContext('2d');
const notifData = @json($notificationsByType);
const notifLabels = Object.keys(notifData).map(type => {
    return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
});
const notifValues = Object.values(notifData);

new Chart(notifCtx, {
    type: 'pie',
    data: {
        labels: notifLabels,
        datasets: [{
            data: notifValues,
            backgroundColor: [
                colors.primary,
                colors.danger,
                colors.info,
                colors.warning,
                colors.success,
                colors.purple
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: { size: 10 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});

// ===== 7. HOURLY DISTRIBUTION CHART =====
const hourlyCtx = document.getElementById('hourlyDistributionChart').getContext('2d');
const hourlyData = @json($hourlyDistribution);
const hours = Array.from({length: 24}, (_, i) => i);
const hourlyMinutes = hours.map(hour => {
    const entry = hourlyData.find(d => d.hour == hour);
    return entry ? (entry.total_minutes / 60).toFixed(1) : 0;
});
const hourLabels = hours.map(h => {
    if (h === 0) return '12 AM';
    if (h < 12) return h + ' AM';
    if (h === 12) return '12 PM';
    return (h - 12) + ' PM';
});

const gradient3 = hourlyCtx.createLinearGradient(0, 0, 0, 300);
gradient3.addColorStop(0, 'rgba(67, 233, 123, 0.3)');
gradient3.addColorStop(1, 'rgba(56, 249, 215, 0.01)');

new Chart(hourlyCtx, {
    type: 'line',
    data: {
        labels: hourLabels,
        datasets: [{
            label: 'Hours Worked',
            data: hourlyMinutes,
            borderColor: colors.gradient4[0],
            backgroundColor: gradient3,
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: context => context.parsed.y + ' hours'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: { callback: value => value + 'h' }
            },
            x: {
                grid: { display: false },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    autoSkip: true,
                    maxTicksLimit: 12
                }
            }
        }
    }
});
</script>
@endpush
