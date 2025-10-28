<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function create(Course $course)
    {
        return view('marksheet.assessments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'actual_percentage' => 'required|numeric|min:0|max:100',
            'target_percentage' => 'required|numeric|min:0|max:100',
            'achieved_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        $course->assessments()->create($validated);

        return redirect()->route('courses.show', $course)->with('success', 'Assessment added successfully');
    }
}
