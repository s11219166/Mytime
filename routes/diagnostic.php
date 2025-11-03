<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Diagnostic Routes - Admin Only
Route::middleware(['auth'])->group(function () {
    
    // Check database connection and info
    Route::get('/diagnostic/db-info', function() {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");
            
            // Get database info
            $dbInfo = [
                'connection' => $connection,
                'driver' => $driver,
                'host' => config("database.connections.{$connection}.host"),
                'database' => config("database.connections.{$connection}.database"),
                'port' => config("database.connections.{$connection}.port"),
            ];
            
            // Test connection
            DB::connection()->getPdo();
            
            // Get project count
            $projectCount = DB::table('projects')->count();
            $transactionCount = DB::table('financial_transactions')->count();
            
            return response()->json([
                'status' => 'success',
                'database_info' => $dbInfo,
                'project_count' => $projectCount,
                'transaction_count' => $transactionCount,
                'connection_status' => 'Connected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.db-info');
    
    // Check all projects in database
    Route::get('/diagnostic/projects', function() {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $projects = DB::table('projects')
                ->select('id', 'name', 'status', 'created_at', 'updated_at')
                ->orderBy('id', 'desc')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'total_count' => count($projects),
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.projects');
    
    // Check all transactions in database
    Route::get('/diagnostic/transactions', function() {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $transactions = DB::table('financial_transactions')
                ->select('id', 'user_id', 'type', 'amount', 'deleted_at', 'created_at', 'updated_at')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get();
            
            $deletedCount = DB::table('financial_transactions')
                ->whereNotNull('deleted_at')
                ->count();
            
            $activeCount = DB::table('financial_transactions')
                ->whereNull('deleted_at')
                ->count();
            
            return response()->json([
                'status' => 'success',
                'active_count' => $activeCount,
                'deleted_count' => $deletedCount,
                'total_count' => $activeCount + $deletedCount,
                'recent_transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.transactions');
    
    // Test delete a specific project
    Route::post('/diagnostic/test-delete-project/{id}', function($id) {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            // Check if exists
            $exists = DB::table('projects')->where('id', $id)->first();
            if (!$exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project not found'
                ], 404);
            }
            
            // Delete team members
            DB::table('project_user')->where('project_id', $id)->delete();
            
            // Delete project
            $deleted = DB::table('projects')->where('id', $id)->delete();
            
            // Verify
            $stillExists = DB::table('projects')->where('id', $id)->first();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Project deleted',
                'deleted_rows' => $deleted,
                'still_exists' => $stillExists ? true : false,
                'verification' => $stillExists ? 'FAILED - Project still in database!' : 'SUCCESS - Project removed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.test-delete-project');
    
    // Test delete a specific transaction
    Route::post('/diagnostic/test-delete-transaction/{id}', function($id) {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            // Check if exists
            $exists = DB::table('financial_transactions')->where('id', $id)->first();
            if (!$exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }
            
            // Delete transaction (soft delete)
            $deleted = DB::table('financial_transactions')
                ->where('id', $id)
                ->update(['deleted_at' => now()]);
            
            // Verify with deleted_at
            $stillActive = DB::table('financial_transactions')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();
            
            // Check with deleted_at
            $isDeleted = DB::table('financial_transactions')
                ->where('id', $id)
                ->whereNotNull('deleted_at')
                ->first();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction soft deleted',
                'updated_rows' => $deleted,
                'still_active' => $stillActive ? true : false,
                'is_marked_deleted' => $isDeleted ? true : false,
                'verification' => $stillActive ? 'FAILED - Transaction still active!' : 'SUCCESS - Transaction marked as deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.test-delete-transaction');
    
    // Force hard delete all soft-deleted transactions
    Route::post('/diagnostic/purge-deleted-transactions', function() {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $count = DB::table('financial_transactions')
                ->whereNotNull('deleted_at')
                ->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => "Permanently deleted {$count} soft-deleted transactions"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.purge-deleted-transactions');
    
    // Check query execution
    Route::get('/diagnostic/test-query', function() {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            // Test raw query
            $result = DB::select('SELECT COUNT(*) as count FROM projects');
            $projectCount = $result[0]->count ?? 0;
            
            // Test Eloquent
            $eloquentCount = \App\Models\Project::count();
            
            // Test with active scope
            $activeCount = \App\Models\Project::whereNull('deleted_at')->count();
            
            return response()->json([
                'status' => 'success',
                'raw_query_count' => $projectCount,
                'eloquent_count' => $eloquentCount,
                'active_count' => $activeCount,
                'match' => $projectCount === $eloquentCount ? 'YES' : 'NO - Possible caching issue'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('diagnostic.test-query');
    
});
