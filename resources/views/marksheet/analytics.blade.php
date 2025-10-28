@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ __('Analytics') }}</h1>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">GPA Overview</h3>
                    <canvas id="gpaChart" width="400" height="200"></canvas>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($courses as $course)
                        <div class="border rounded-lg p-4">
                            <h4 class="text-lg font-semibold mb-2">{{ $course->name }}</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Average Target GPA: {{ number_format($course->getAverageTargetGpa(), 2) }}</p>
                                    <p class="text-sm text-gray-600">Average Achieved GPA: {{ number_format($course->getAverageAchievedGpa(), 2) }}</p>
                                </div>
                                <canvas id="courseChart{{ $course->id }}" width="300" height="200"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Overall GPA Chart
        const gpaCtx = document.getElementById('gpaChart').getContext('2d');
        new Chart(gpaCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($courseNames) !!},
                datasets: [
                    {
                        label: 'Target GPA',
                        data: {!! json_encode($targetGPAs) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Achieved GPA',
                        data: {!! json_encode($achievedGPAs) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4.0
                    }
                }
            }
        });

        // Individual Course Charts
        @foreach($courses as $course)
            const courseCtx{{ $course->id }} = document.getElementById('courseChart{{ $course->id }}').getContext('2d');
            new Chart(courseCtx{{ $course->id }}, {
                type: 'line',
                data: {
                    labels: {!! json_encode($course->assessments->pluck('name')) !!},
                    datasets: [
                        {
                            label: 'Target %',
                            data: {!! json_encode($course->assessments->pluck('target_percentage')) !!},
                            borderColor: 'rgba(54, 162, 235, 1)',
                            tension: 0.1
                        },
                        {
                            label: 'Achieved %',
                            data: {!! json_encode($course->assessments->pluck('achieved_percentage')) !!},
                            borderColor: 'rgba(75, 192, 192, 1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        @endforeach
    </script>
    @endpush
@endsection
