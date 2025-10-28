<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile page
     */
    public function show()
    {
        $user = Auth::user();

        $assignedProjectsCount = $user->assignedProjects()->count();
        $createdProjectsCount = $user->createdProjects()->count();
        $totalProjects = $assignedProjectsCount + $createdProjectsCount;

        $totalTrackedHours = 0;
        $weeklyTrackedHours = 0;

        $efficiency = $user->working_hours
            ? sprintf('%d%%', min(100, round(($weeklyTrackedHours / max(1, $user->working_hours * 5)) * 100)))
            : 'N/A';

        $profileStats = [
            'projects' => $totalProjects,
            'total_time' => sprintf('%sh', number_format($totalTrackedHours, 1)),
            'weekly_time' => sprintf('%sh', number_format($weeklyTrackedHours, 1)),
            'efficiency' => $efficiency,
        ];

        $availableTimezones = config('profile.timezones');
        $availableDateFormats = config('profile.date_formats');
        $availableTimeFormats = config('profile.time_formats');

        return view('profile', compact(
            'user',
            'profileStats',
            'availableTimezones',
            'availableDateFormats',
            'availableTimeFormats'
        ));
    }

    /**
     * Update personal information
     */
    public function updatePersonalInfo(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'first_name', 'last_name', 'phone', 'department', 'position', 'bio'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Personal information updated successfully!'
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['Current password is incorrect.']]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'working_hours' => 'required|integer|min:1|max:24',
            'email_notifications' => 'nullable|boolean',
            'project_updates' => 'nullable|boolean',
            'time_reminders' => 'nullable|boolean',
            'weekly_reports' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $request->only([
            'timezone', 'date_format', 'time_format', 'working_hours'
        ]);

        foreach (['email_notifications', 'project_updates', 'time_reminders', 'weekly_reports'] as $toggle) {
            $payload[$toggle] = $request->boolean($toggle);
        }

        $user->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved successfully!'
        ]);
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedFile = $request->file('avatar');
        
        // Store in public disk under avatars folder
        $storedPath = $uploadedFile->store('avatars', 'public');

        if (!$storedPath) {
            return back()->with('error', 'Unable to upload profile photo. Please try again.');
        }

        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Only update the profile_photo_path field
        $user->profile_photo_path = $storedPath;
        $user->save();

        return back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Download time report placeholder
     */
    public function downloadReport()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="time-report.csv"',
        ];

        $csvContent = implode("\n", [
            'Date,Project,Hours,Notes',
            now()->format('Y-m-d') . ',Sample Project,0,Report export placeholder',
        ]);

        return response($csvContent, 200, $headers);
    }
}
