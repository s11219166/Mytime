<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = auth()->user()->courses;
        return view('marksheet.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('marksheet.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string'
        ]);

        $validated['user_id'] = auth()->id();
        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course created successfully');
    }

    public function show(Course $course)
    {
        return view('marksheet.courses.show', compact('course'));
    }

    public function analytics()
    {
        $courses = auth()->user()->courses()->with('assessments')->get();
        $courseNames = $courses->pluck('name');
        $targetGPAs = $courses->map->getAverageTargetGpa();
        $achievedGPAs = $courses->map->getAverageAchievedGpa();

        return view('marksheet.analytics', compact('courses', 'courseNames', 'targetGPAs', 'achievedGPAs'));
    }
}
