@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Quick Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">My Academic Journey</h2>
                            <p class="mb-0 opacity-75">Track and manage your academic progress</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('courses.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-2"></i>Add New Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 opacity-75">Overall GPA</h6>
                    <h3 class="mb-0">{{ number_format($overallGpa ?? 0, 2) }}</h3>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-chart-line me-1"></i>Based on achieved grades
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 opacity-75">Total Courses</h6>
                    <h3 class="mb-0">{{ $courses->count() }}</h3>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-books me-1"></i>Active courses
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 opacity-75">Upcoming Assessments</h6>
                    <h3 class="mb-0">{{ $upcomingAssessments ?? 0 }}</h3>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-clock me-1"></i>Due this week
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 opacity-75">At Risk</h6>
                    <h3 class="mb-0">{{ $atRiskCourses ?? 0 }}</h3>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-exclamation-triangle me-1"></i>Below target GPA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary">{{ $course->code }}</span>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('courses.show', $course) }}">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('courses.assessments.create', $course) }}">
                                        <i class="fas fa-plus me-2"></i>Add Assessment
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a></li>
                                </ul>
                            </div>
                        </div>

                        <h4 class="card-title mb-3">{{ $course->name }}</h4>
                        <p class="card-text text-muted mb-3">{{ Str::limit($course->description, 100) }}</p>

                        <!-- Progress Section -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Course Progress</span>
                                <span class="small text-muted">{{ $course->progress ?? 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $course->progress ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <div class="small text-muted mb-1">Target GPA</div>
                                    <div class="fw-bold">{{ number_format($course->getAverageTargetGpa(), 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <div class="small text-muted mb-1">Current GPA</div>
                                    <div class="fw-bold">{{ number_format($course->getAverageAchievedGpa(), 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-tasks me-1"></i>
                                {{ $course->assessments_count ?? 0 }} Assessments
                            </span>
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-primary">
                                View Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center p-5">
                        <img src="https://cdn.jsdelivr.net/npm/twemoji@11.3.0/2/svg/1f4da.svg"
                             alt="Empty courses" class="mb-4" style="width: 64px;">
                        <h4>No Courses Yet</h4>
                        <p class="text-muted mb-4">Start your academic journey by adding your first course</p>
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Add Your First Course
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
    .hover-shadow-lg {
        transition: all 0.3s ease;
    }
    .hover-shadow-lg:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endpush
            </div>
        </div>
    </div>
@endsection
