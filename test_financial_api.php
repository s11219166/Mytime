<?php

/**
 * Financial API Test Script
 * Run this to test if the financial API is working correctly
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== Financial API Test ===\n\n";

// Test 1: Check if categories exist
echo "Test 1: Checking Financial Categories\n";
echo "--------------------------------------\n";
$categoryCount = FinancialCategory::count();
echo "Total categories: $categoryCount\n";

if ($categoryCount > 0) {
    echo "Categories found:\n";
    $categories = FinancialCategory::all();
    foreach ($categories as $cat) {
        echo "  - ID: {$cat->id}, Name: {$cat->name}, Type: {$cat->type}, Icon: {$cat->icon}\n";
    }
} else {
    echo "❌ NO CATEGORIES FOUND! You need to create categories first.\n";
}

echo "\n";

// Test 2: Check if transactions table exists
echo "Test 2: Checking Financial Transactions Table\n";
echo "---------------------------------------------\n";
try {
    $transactionCount = FinancialTransaction::count();
    echo "Total transactions: $transactionCount\n";
    
    if ($transactionCount > 0) {
        echo "Recent transactions:\n";
        $transactions = FinancialTransaction::latest()->take(5)->get();
        foreach ($transactions as $txn) {
            echo "  - ID: {$txn->id}, Type: {$txn->type}, Amount: {$txn->amount}, Date: {$txn->transaction_date}\n";
        }
    } else {
        echo "No transactions yet (this is normal for new installations)\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "\n";

// Test 3: Check if users exist
echo "Test 3: Checking Users\n";
echo "---------------------\n";
$userCount = User::count();
echo "Total users: $userCount\n";

if ($userCount > 0) {
    $users = User::all();
    foreach ($users as $user) {
        echo "  - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    }
} else {
    echo "❌ NO USERS FOUND! Create a user first.\n";
}

echo "\n";

// Test 4: Test creating a transaction
echo "Test 4: Testing Transaction Creation\n";
echo "-----------------------------------\n";

if ($categoryCount > 0 && $userCount > 0) {
    try {
        $user = User::first();
        $category = FinancialCategory::where('type', 'expense')->first();
        
        if (!$category) {
            $category = FinancialCategory::first();
        }
        
        echo "Creating test transaction...\n";
        echo "  User: {$user->name} (ID: {$user->id})\n";
        echo "  Category: {$category->name} (ID: {$category->id})\n";
        
        $transaction = FinancialTransaction::create([
            'user_id' => $user->id,
            'transaction_date' => now()->toDateString(),
            'type' => $category->type,
            'category_id' => $category->id,
            'amount' => 100.00,
            'description' => 'Test transaction from API test script',
            'status' => 'completed',
            'reference_number' => 'TEST-' . time()
        ]);
        
        echo "✅ Transaction created successfully!\n";
        echo "  Transaction ID: {$transaction->id}\n";
        echo "  Amount: {$transaction->amount}\n";
        echo "  Type: {$transaction->type}\n";
        
        // Delete the test transaction
        $transaction->delete();
        echo "✅ Test transaction deleted\n";
        
    } catch (\Exception $e) {
        echo "❌ ERROR: {$e->getMessage()}\n";
        echo "Stack trace: {$e->getTraceAsString()}\n";
    }
} else {
    echo "⚠️  Skipping transaction creation test (need categories and users)\n";
}

echo "\n";

// Test 5: Check database connection
echo "Test 5: Database Connection\n";
echo "----------------------------\n";
try {
    $connection = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ Database connection successful\n";
    
    // Check tables
    $tables = \Illuminate\Support\Facades\DB::select("
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = DATABASE()
    ");
    
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "  - {$table->TABLE_NAME}\n";
    }
} catch (\Exception $e) {
    echo "❌ Database connection failed: {$e->getMessage()}\n";
}

echo "\n";

// Test 6: Check model relationships
echo "Test 6: Model Relationships\n";
echo "---------------------------\n";
try {
    if ($categoryCount > 0 && $userCount > 0) {
        $user = User::first();
        $category = FinancialCategory::first();
        
        echo "Testing relationships...\n";
        echo "  User has " . $user->financialTransactions()->count() . " transactions\n";
        echo "  Category has " . $category->transactions()->count() . " transactions\n";
        echo "✅ Relationships working\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "\n";

// Summary
echo "=== Test Summary ===\n";
echo "✅ Categories: " . ($categoryCount > 0 ? "OK" : "MISSING") . "\n";
echo "✅ Users: " . ($userCount > 0 ? "OK" : "MISSING") . "\n";
echo "✅ Database: Connected\n";
echo "\nIf all tests pass, the API should work correctly.\n";
echo "If any test fails, check the error message above.\n";
