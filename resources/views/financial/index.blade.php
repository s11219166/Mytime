@extends('layouts.app')

@section('title', 'Financial Management')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="financialDashboard()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Financial Management</h1>
                <p class="text-gray-600 mt-1">Track your income, expenses, savings, and bank deposits</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <!-- Privacy Toggle -->
                <button @click="togglePrivacy()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition flex items-center gap-2">
                    <span x-show="!hideAmounts">👁️</span>
                    <span x-show="hideAmounts">🙈</span>
                    <span x-text="hideAmounts ? 'Show Amounts' : 'Hide Amounts'"></span>
                </button>

                <!-- Add Transaction Button -->
                <button @click="openAddModal()"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition shadow-lg flex items-center gap-2">
                    <span class="text-xl">+</span>
                    <span>Add Transaction</span>
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select x-model="dateRange" @change="fetchData()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select x-model="typeFilter" @change="fetchData()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="savings">Savings</option>
                        <option value="bank_deposit">Bank Deposit</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select x-model="categoryFilter" @change="fetchData()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Export Button -->
                <div class="flex items-end">
                    <button @click="exportData()"
                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <span>📥</span>
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Income Card -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Income</p>
                        <h3 class="text-3xl font-bold mt-2" x-text="hideAmounts ? '****' : formatCurrency(summary.income)"></h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <span class="text-2xl">💰</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span :class="summary.income_trend >= 0 ? 'text-green-200' : 'text-red-200'"
                          x-text="(summary.income_trend >= 0 ? '↑' : '↓') + ' ' + Math.abs(summary.income_trend) + '%'"></span>
                    <span class="text-green-100 text-sm">vs previous period</span>
                </div>
            </div>

            <!-- Expenses Card -->
            <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Total Expenses</p>
                        <h3 class="text-3xl font-bold mt-2" x-text="hideAmounts ? '****' : formatCurrency(summary.expense)"></h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <span class="text-2xl">💸</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span :class="summary.expense_trend <= 0 ? 'text-green-200' : 'text-red-200'"
                          x-text="(summary.expense_trend >= 0 ? '↑' : '↓') + ' ' + Math.abs(summary.expense_trend) + '%'"></span>
                    <span class="text-red-100 text-sm">vs previous period</span>
                </div>
            </div>

            <!-- Savings Card -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Savings</p>
                        <h3 class="text-3xl font-bold mt-2" x-text="hideAmounts ? '****' : formatCurrency(summary.savings)"></h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <span class="text-2xl">🏦</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span :class="summary.savings_trend >= 0 ? 'text-green-200' : 'text-red-200'"
                          x-text="(summary.savings_trend >= 0 ? '↑' : '↓') + ' ' + Math.abs(summary.savings_trend) + '%'"></span>
                    <span class="text-blue-100 text-sm">vs previous period</span>
                </div>
            </div>

            <!-- Bank Deposits Card -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">Bank Deposits</p>
                        <h3 class="text-3xl font-bold mt-2" x-text="hideAmounts ? '****' : formatCurrency(summary.bank_deposit)"></h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <span class="text-2xl">🏛️</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span :class="summary.bank_deposit_trend >= 0 ? 'text-green-200' : 'text-red-200'"
                          x-text="(summary.bank_deposit_trend >= 0 ? '↑' : '↓') + ' ' + Math.abs(summary.bank_deposit_trend) + '%'"></span>
                    <span class="text-amber-100 text-sm">vs previous period</span>
                </div>
            </div>
        </div>

        <!-- Net Balance Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border-l-4"
             :class="summary.net_balance >= 0 ? 'border-green-500' : 'border-red-500'">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Net Balance (Income - Expenses)</p>
                    <h3 class="text-4xl font-bold mt-2"
                        :class="summary.net_balance >= 0 ? 'text-green-600' : 'text-red-600'"
                        x-text="hideAmounts ? '****' : formatCurrency(summary.net_balance)"></h3>
                </div>
                <div class="text-6xl" x-text="summary.net_balance >= 0 ? '📈' : '📉'"></div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Income vs Expense Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Income vs Expenses Trend</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="incomeExpenseChart"></canvas>
                </div>
            </div>

            <!-- Expense by Category Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Expense Breakdown by Category</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="expenseCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Recent Transactions</h3>
                    <span class="text-sm text-gray-500">Total: {{ $transactions->total() }} transactions</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $transaction->transaction_date->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $transaction->transaction_date->format('l') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                    @if($transaction->type === 'income') bg-gradient-to-r from-green-400 to-green-500 text-white
                                    @elseif($transaction->type === 'expense') bg-gradient-to-r from-red-400 to-red-500 text-white
                                    @elseif($transaction->type === 'savings') bg-gradient-to-r from-blue-400 to-blue-500 text-white
                                    @else bg-gradient-to-r from-amber-400 to-amber-500 text-white @endif">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-2">{{ $transaction->category->icon }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $transaction->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-data>
                                <div class="text-sm font-bold"
                                     :class="'{{ $transaction->type }}' === 'income' ? 'text-green-600' : '{{ $transaction->type }}' === 'expense' ? 'text-red-600' : 'text-blue-600'">
                                    <span x-show="!$root.hideAmounts">${{ number_format($transaction->amount, 2) }}</span>
                                    <span x-show="$root.hideAmounts" class="text-gray-400">••••</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $transaction->description }}">
                                    {{ $transaction->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal({{ $transaction->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-md mr-2 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button @click="deleteTransaction({{ $transaction->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-xl font-semibold text-gray-500 mb-2">No transactions found</p>
                                    <p class="text-sm text-gray-400 mb-4">Start by adding your first transaction</p>
                                    <button @click="openAddModal()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                        Add Transaction
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-semibold">{{ $transactions->firstItem() }}</span> 
                        to <span class="font-semibold">{{ $transactions->lastItem() }}</span> 
                        of <span class="font-semibold">{{ $transactions->total() }}</span> results
                    </div>
                    <div>
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Transaction Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto"
             @click.stop>
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" x-text="editMode ? 'Edit Transaction' : 'Add New Transaction'"></h3>
            </div>

            <form @submit.prevent="submitTransaction()" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               x-model="formData.transaction_date"
                               :max="new Date().toISOString().split('T')[0]"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.type"
                                @change="filterCategoriesByType()"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                            <option value="savings">Savings</option>
                            <option value="bank_deposit">Bank Deposit</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.category_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Category</option>
                            <template x-for="category in filteredCategories" :key="category.id">
                                <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               x-model="formData.amount"
                               step="0.01"
                               min="0.01"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.status"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text"
                               x-model="formData.reference_number"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Optional">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea x-model="formData.description"
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add any notes or details..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                            @click="closeModal()"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition shadow-lg">
                        <span x-text="editMode ? 'Update Transaction' : 'Add Transaction'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pass categories data to JavaScript
    window.financialCategories = @json($categories);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="{{ asset('js/financial.js') }}"></script>
<script>
    // Initialize charts after page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing financial dashboard charts...');
        // Charts will be initialized by Alpine.js init()
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/financial.css') }}">
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
