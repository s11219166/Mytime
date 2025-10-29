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
                <canvas id="incomeExpenseChart"></canvas>
            </div>

            <!-- Expense by Category Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Expense Breakdown by Category</h3>
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->transaction_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($transaction->type === 'income') bg-green-100 text-green-800
                                    @elseif($transaction->type === 'expense') bg-red-100 text-red-800
                                    @elseif($transaction->type === 'savings') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span>{{ $transaction->category->icon }}</span>
                                <span class="text-gray-900">{{ $transaction->category->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" x-data
                                :class="'{{ $transaction->type }}' === 'income' ? 'text-green-600' : 'text-red-600'">
                                <span x-show="!$root.hideAmounts">${{ number_format($transaction->amount, 2) }}</span>
                                <span x-show="$root.hideAmounts">****</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                {{ $transaction->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal({{ $transaction->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="deleteTransaction({{ $transaction->id }})"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-6xl mb-4">📊</div>
                                <p class="text-lg">No transactions found</p>
                                <p class="text-sm mt-2">Start by adding your first transaction</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
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
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    x-show="!formData.type || formData.type === '{{ $category->type }}'">
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                            @endforeach
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/financial.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/financial.css') }}">
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
