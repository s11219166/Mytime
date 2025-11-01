<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FinancialController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Session Routes
    Route::post('/session/note', [DashboardController::class, 'addNote'])->name('session.note');
    Route::post('/session/break/start', [DashboardController::class, 'startBreak'])->name('session.break.start');
    Route::post('/session/break/end', [DashboardController::class, 'endBreak'])->name('session.break.end');
    Route::post('/session/end', [DashboardController::class, 'endSession'])->name('session.end');

    // Admin Dashboard
    Route::get('/admin/dashboard', function () {
        if (!Auth::user()->isAdmin()) {
            return redirect('/dashboard');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // User Management Routes (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserManagementController::class);
    });

    // Projects Routes
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/progress', [ProjectController::class, 'updateProgress'])->name('projects.progress.update');
    Route::post('/projects/{project}/mark-complete', [ProjectController::class, 'markComplete'])->name('projects.mark-complete');

    // Analytics Routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    // Time Logs Routes
    Route::get('/time-logs', [TimeLogController::class, 'index'])->name('time-logs.index');
    Route::post('/time-logs/start', [TimeLogController::class, 'start'])->name('time-logs.start');
    Route::post('/time-logs/{id}/stop', [TimeLogController::class, 'stop'])->name('time-logs.stop');
    Route::post('/time-logs', [TimeLogController::class, 'store'])->name('time-logs.store');
    Route::put('/time-logs/{id}', [TimeLogController::class, 'update'])->name('time-logs.update');
    Route::delete('/time-logs/{id}', [TimeLogController::class, 'destroy'])->name('time-logs.destroy');

    // Inspiration Hub Route
    Route::get('/inspiration', function () {
        return view('inspiration');
    })->name('inspiration');

    // Test route to generate sample notifications (remove in production)
    Route::get('/test-notifications', function() {
        $service = app(\App\Services\NotificationService::class);
        $user = Auth::user();
        $project = \App\Models\Project::first();

        if ($project) {
            // Create sample notifications
            $service->sendProjectDueReminder($project, $user, 3);
            $service->notifyProjectAssignment($project, $user);

            return redirect()->route('notifications')->with('success', 'Sample notifications created!');
        }

        return redirect()->route('notifications')->with('error', 'No projects found. Create a project first.');
    })->name('test.notifications');

    // Test route to send test email (remove in production)
    Route::get('/test-email', function() {
        try {
            $user = Auth::user();

            // Send a simple test email
            \Illuminate\Support\Facades\Mail::raw('This is a test email from MyTime application. If you received this, your email configuration is working correctly!', function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Test Email from MyTime');
            });

            return back()->with('success', 'Test email sent successfully to ' . $user->email . '! Check your inbox.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    })->name('test.email');

    // Test route to send project reminder email (remove in production)
    Route::get('/test-project-email', function() {
        try {
            $user = Auth::user();
            $project = \App\Models\Project::first();

            if (!$project) {
                return back()->with('error', 'No projects found. Create a project first.');
            }

            // Send project due reminder email
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\ProjectDueReminderMail($project, $user, 3));

            return back()->with('success', 'Project reminder email sent successfully to ' . $user->email . '! Check your inbox.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    })->name('test.project.email');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile/personal', [ProfileController::class, 'updatePersonalInfo'])->name('profile.personal.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/profile/report', [ProfileController::class, 'downloadReport'])->name('profile.report.download');

    // Financial Management Routes
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/transaction', [FinancialController::class, 'store'])->name('financial.store');
    Route::put('/financial/transaction/{id}', [FinancialController::class, 'update'])->name('financial.update');
    Route::delete('/financial/transaction/{id}', [FinancialController::class, 'destroy'])->name('financial.destroy');
    Route::get('/financial/chart-data', [FinancialController::class, 'getChartData'])->name('financial.chart-data');
    Route::get('/financial/summary', [FinancialController::class, 'getSummary'])->name('financial.summary');
    Route::get('/financial/export', [FinancialController::class, 'export'])->name('financial.export');
    Route::get('/financial/filter', [FinancialController::class, 'filter'])->name('financial.filter');

    // Notification Routes
    Route::get('/notifications', function () {
        $notifications = Auth::user()->notifications()->latest()->paginate(10);
        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
        return view('notifications', compact('notifications', 'unreadCount'));
    })->name('notifications');

    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/mark-multiple-read', [\App\Http\Controllers\NotificationController::class, 'markMultipleAsRead'])->name('notifications.mark-multiple-read');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy']);
    Route::post('/notifications/clear-read', [\App\Http\Controllers\NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'getLatest']);
});

// Test route to check database connection
Route::get('/test-db', function() {
    try {
        $count = \App\Models\FinancialCategory::count();
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection is working',
            'category_count' => $count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test route to check database connection for transactions
Route::get('/test-transactions', function() {
    try {
        $count = \App\Models\FinancialTransaction::count();
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection is working',
            'transaction_count' => $count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
