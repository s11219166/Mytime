<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use App\Http\Requests\StoreFinancialTransactionRequest;
use App\Http\Requests\UpdateFinancialTransactionRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialController extends Controller
{
    /**
     * Display the financial dashboard
     */
    public function index(Request $request)
    {
        // Clear cache to ensure fresh data
        \Illuminate\Support\Facades\Cache::flush();
        
        $user = Auth::user();

        // Get filter parameters
        $dateRange = $request->input('date_range', '30');
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        // Calculate date range
        $endDate = Carbon::now();
        $startDate = match($dateRange) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(30)
        };

        // Use raw query to bypass any caching issues
        $baseQuery = DB::table('financial_transactions')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type) {
            $baseQuery->where('type', $type);
        }

        if ($categoryId) {
            $baseQuery->where('category_id', $categoryId);
        }

        // Get transaction IDs from raw query
        $transactionIds = $baseQuery->pluck('id')->toArray();

        // Now use Eloquent to load with relationships
        $transactions = FinancialTransaction::with('category')
            ->whereIn('id', $transactionIds)
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        // Get categories for user
        $categories = FinancialCategory::forUser($user->id)->get();

        // Get summary statistics
        $summary = $this->calculateSummary($user->id, $startDate, $endDate);

        return view('financial.index', compact(
            'transactions',
            'categories',
            'summary',
            'dateRange'
        ));
    }

    /**
     * Get a single transaction for editing
     */
    public function show($id)
    {
        $transaction = FinancialTransaction::with('category')
            ->forUser(Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    /**
     * Store a new transaction
     */
    public function store(StoreFinancialTransactionRequest $request)
    {
        $transaction = FinancialTransaction::create([
            'user_id' => Auth::id(),
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => $request->status ?? 'completed',
            'reference_number' => $request->reference_number,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'transaction' => $transaction->load('category')
            ]);
        }

        return redirect()->route('financial.index')
            ->with('success', 'Transaction created successfully');
    }

    /**
     * Update a transaction
     */
    public function update(UpdateFinancialTransactionRequest $request, $id)
    {
        $transaction = FinancialTransaction::forUser(Auth::id())->findOrFail($id);

        $transaction->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'transaction' => $transaction->load('category')
            ]);
        }

        return redirect()->route('financial.index')
            ->with('success', 'Transaction updated successfully');
    }

    /**
     * Delete a transaction
     */
    public function destroy(Request $request, $id)
    {
        $transaction = FinancialTransaction::forUser(Auth::id())->findOrFail($id);
        $transaction->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        }

        return redirect()->route('financial.index')
            ->with('success', 'Transaction deleted successfully');
    }

    /**
     * Get chart data for visualizations
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->input('date_range', '30');

        $endDate = Carbon::now();
        $startDate = match($dateRange) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(30)
        };

        // Income vs Expense over time
        $dailyData = FinancialTransaction::forUser($user->id)
            ->dateRange($startDate, $endDate)
            ->completed()
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->whereIn('type', ['income', 'expense'])
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        // Expense by category
        $expenseByCategory = FinancialTransaction::with('category')
            ->forUser($user->id)
            ->ofType('expense')
            ->completed()
            ->dateRange($startDate, $endDate)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        // Savings distribution
        $savingsDistribution = FinancialTransaction::with('category')
            ->forUser($user->id)
            ->whereIn('type', ['savings', 'bank_deposit'])
            ->completed()
            ->dateRange($startDate, $endDate)
            ->select('category_id', 'type', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id', 'type')
            ->get();

        return response()->json([
            'dailyData' => $dailyData,
            'expenseByCategory' => $expenseByCategory,
            'savingsDistribution' => $savingsDistribution
        ]);
    }

    /**
     * Get summary statistics
     */
    public function getSummary(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->input('date_range', '30');

        $endDate = Carbon::now();
        $startDate = match($dateRange) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(30)
        };

        $summary = $this->calculateSummary($user->id, $startDate, $endDate);

        return response()->json($summary);
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $dateRange = $request->input('date_range', '30');
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        $endDate = Carbon::now();
        $startDate = match($dateRange) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(30)
        };

        $query = FinancialTransaction::with('category')
            ->forUser($user->id)
            ->dateRange($startDate, $endDate)
            ->orderBy('transaction_date', 'desc');

        if ($type) {
            $query->ofType($type);
        }

        if ($categoryId) {
            $query->byCategory($categoryId);
        }

        $transactions = $query->get();

        $filename = 'financial_transactions_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['Date', 'Type', 'Category', 'Amount', 'Status', 'Description', 'Reference']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d'),
                    ucfirst($transaction->type),
                    $transaction->category->name,
                    number_format($transaction->amount, 2),
                    ucfirst($transaction->status),
                    $transaction->description,
                    $transaction->reference_number
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($userId, $startDate, $endDate)
    {
        $income = FinancialTransaction::getTotalByType($userId, 'income', $startDate, $endDate);
        $expense = FinancialTransaction::getTotalByType($userId, 'expense', $startDate, $endDate);
        $savings = FinancialTransaction::getTotalByType($userId, 'savings', $startDate, $endDate);
        $bankDeposit = FinancialTransaction::getTotalByType($userId, 'bank_deposit', $startDate, $endDate);

        $incomeTrend = FinancialTransaction::getTrendPercentage($userId, 'income', $startDate, $endDate);
        $expenseTrend = FinancialTransaction::getTrendPercentage($userId, 'expense', $startDate, $endDate);
        $savingsTrend = FinancialTransaction::getTrendPercentage($userId, 'savings', $startDate, $endDate);
        $bankDepositTrend = FinancialTransaction::getTrendPercentage($userId, 'bank_deposit', $startDate, $endDate);

        // Calculate pending transactions (exclude soft deleted)
        $pendingTransactions = FinancialTransaction::forUser($userId)
            ->active()
            ->where('status', 'pending')
            ->dateRange($startDate, $endDate)
            ->get();

        $pendingCount = $pendingTransactions->count();
        $pendingIncome = $pendingTransactions->where('type', 'income')->sum('amount');
        $pendingExpense = $pendingTransactions->where('type', 'expense')->sum('amount');
        // Total amount = Income - Expenses for pending transactions
        $pendingTotal = $pendingIncome - $pendingExpense;

        // Net balance = Income - Expenses - ANZ Expense Bank 10984661 - ANZ Saving Account 13674771
        $netBalance = $income - $expense - $savings - $bankDeposit;

        return [
            'income' => $income,
            'expense' => $expense,
            'savings' => $savings,
            'bank_deposit' => $bankDeposit,
            'net_balance' => $netBalance,
            'income_trend' => $incomeTrend,
            'expense_trend' => $expenseTrend,
            'savings_trend' => $savingsTrend,
            'bank_deposit_trend' => $bankDepositTrend,
            'pending_count' => $pendingCount,
            'pending_total' => $pendingTotal,
            'pending_income' => $pendingIncome,
            'pending_expense' => $pendingExpense
        ];
    }
}
