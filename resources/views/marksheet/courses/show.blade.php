@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-1">{{ $course->name }}</h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-bookmark me-2"></i>{{ $course->code }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('courses.assessments.create', $course) }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-2"></i>Add Assessment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase mb-1">Average GPA</h6>
                            <h3 class="mb-0">{{ number_format($course->getAverageAchievedGpa(), 2) }}</h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: {{ ($course->getAverageAchievedGpa() / 4) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-bullseye text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase mb-1">Target GPA</h6>
                            <h3 class="mb-0">{{ number_format($course->getAverageTargetGpa(), 2) }}</h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ ($course->getAverageTargetGpa() / 4) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-tasks text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase mb-1">Assessments</h6>
                            <h3 class="mb-0">{{ $course->assessments->count() }}</h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-percentage text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase mb-1">Course Progress</h6>
                            <h3 class="mb-0">{{ $course->progress ?? 0 }}%</h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: {{ $course->progress ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Description -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">About This Course</h5>
                    <p class="card-text">{{ $course->description ?: 'No description available.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessments List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Assessments</h5>
                        <a href="{{ route('courses.assessments.create', $course) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Assessment
                        </a>
                    </div>

                    @if($course->assessments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Assessment</th>
                                    <th>Weight</th>
                                    <th>Target</th>
                                    <th>Achievement</th>
                                    <th>Status</th>
                                    <th>GPA Impact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->assessments as $assessment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="assessment-icon me-3">
                                                <i class="fas fa-file-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $assessment->name }}</h6>
                                                <small class="text-muted">Weight: {{ $assessment->actual_percentage }}%</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $assessment->actual_percentage }}%</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-info"
                                                     style="width: {{ $assessment->target_percentage }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $assessment->target_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar {{ $assessment->achieved_percentage >= $assessment->target_percentage ? 'bg-success' : 'bg-warning' }}"
                                                     style="width: {{ $assessment->achieved_percentage ?? 0 }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $assessment->achieved_percentage ?? 'N/A' }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if(!$assessment->achieved_percentage)
                                            <span class="badge bg-secondary">Pending</span>
                                        @elseif($assessment->achieved_percentage >= $assessment->target_percentage)
                                            <span class="badge bg-success">On Track</span>
                                        @else
                                            <span class="badge bg-warning">Needs Attention</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $gpaChange = $assessment->achieved_gpa - $assessment->target_gpa;
                                                $textClass = $gpaChange >= 0 ? 'text-success' : 'text-danger';
                                                $icon = $gpaChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                            @endphp
                                            <span class="{{ $textClass }}">
                                                <i class="fas {{ $icon }} me-1"></i>
                                                {{ abs($gpaChange) > 0 ? number_format(abs($gpaChange), 2) : '0.00' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light" title="Delete">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-center py-5">
                            <img src="https://cdn.jsdelivr.net/npm/twemoji@11.3.0/2/svg/1f4dd.svg"
                                 alt="No assessments" class="mb-3" style="width: 64px;">
                            <h5>No Assessments Yet</h5>
                            <p class="text-muted mb-4">Start tracking your progress by adding your first assessment</p>
                            <a href="{{ route('courses.assessments.create', $course) }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Add Your First Assessment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .assessment-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: rgba(78, 115, 223, 0.1);
    }
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    .table > :not(caption) > * > * {
        padding: 1rem;
    }
</style>
@endpush
            </div>
        </div>
    </div>
@endsection
