@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ __('Add Assessment') }} - {{ $course->name }}</h1>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('courses.assessments.store', $course) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Assessment Name</label>
                        <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="actual_percentage" class="block text-gray-700 text-sm font-bold mb-2">Actual Percentage Weight</label>
                        <input type="number" name="actual_percentage" id="actual_percentage" min="0" max="100" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="target_percentage" class="block text-gray-700 text-sm font-bold mb-2">Target Percentage</label>
                        <input type="number" name="target_percentage" id="target_percentage" min="0" max="100" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="achieved_percentage" class="block text-gray-700 text-sm font-bold mb-2">Achieved Percentage (Optional)</label>
                        <input type="number" name="achieved_percentage" id="achieved_percentage" min="0" max="100" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
