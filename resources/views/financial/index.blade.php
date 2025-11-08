@extends('layouts.app')

@section('title', 'Financial Management - MyTime')

@section('content')
<div class="container-fluid py-4" x-data="financialDashboard()" x-init="console.log('Financial dashboard Alpine component initialized')">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <h1 class="h3 fw-bold mb-1">
                    <i class="fas fa-wallet me-2 text-primary"></i>Financial Management
                </h1>
                <p class="text-muted mb-0 small">Track your income, expenses, savings, and bank deposits</p>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    <!-- Quick Add Button (Mobile Friendly) -->
                    <button @click="openQuickAddModal()" class="btn btn-success btn-sm d-lg-none" title="Quick Add">
                        <i class="fas fa-bolt me-2"></i>Quick Add
                    </button>

                    <!-- Privacy Toggle -->
                    <button @click="togglePrivacy()" class="btn btn-outline-secondary btn-sm" title="Toggle Privacy">
                        <i class="fas" :class="hideAmounts ? 'fa-eye-slash' : 'fa-eye'"></i>
                        <span class="d-none d-sm-inline ms-1" x-text="hideAmounts ? 'Show' : 'Hide'"></span>
                    </button>

                    <!-- Add Transaction Button -->
                    <button @click="openAddModal()" class="btn btn-primary btn-sm d-none d-lg-inline-flex">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>

                    <!-- Export Button -->
                    <button @click="exportData()" class="btn btn-info btn-sm" title="Export CSV">
                        <i class="fas fa-download"></i>
                        <span class="d-none d-sm-inline ms-1">Export</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4 filter-section">
        <div class="card-body">
            <div class="row g-2 g-md-3">
                <!-- Date Range Filter -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="filter-label">Date Range</label>
                    <select x-model="dateRange" @change="fetchData()" class="form-select form-select-sm">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="filter-label">Type</label>
                    <select x-model="typeFilter" @change="fetchData()" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="savings">Savings</option>
                        <option value="bank_deposit">Bank Deposit</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="filter-label">Category</label>
                    <select x-model="categoryFilter" @change="fetchData()" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="filter-label d-none d-md-block">&nbsp;</label>
                    <button @click="clearFilters()" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-redo me-2"></i><span class="d-none d-sm-inline">Reset</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Income Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm summary-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Income</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '••••' : formatCurrency(summary.income)"></h4>
                            <small :class="summary.income_trend >= 0 ? 'text-success' : 'text-danger'" class="trend">
                                <i :class="summary.income_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(summary.income_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div class="summary-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm summary-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Expenses</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '••••' : formatCurrency(summary.expense)"></h4>
                            <small :class="summary.expense_trend <= 0 ? 'text-success' : 'text-danger'" class="trend">
                                <i :class="summary.expense_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(summary.expense_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div class="summary-card-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm summary-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">ANZ 10984661</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '••••' : formatCurrency(summary.savings)"></h4>
                            <small :class="summary.savings_trend >= 0 ? 'text-success' : 'text-danger'" class="trend">
                                <i :class="summary.savings_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(summary.savings_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div class="summary-card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Deposits Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm summary-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">ANZ Acc 13674771</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '••••' : formatCurrency(summary.bank_deposit)"></h4>
                            <small :class="summary.bank_deposit_trend >= 0 ? 'text-success' : 'text-danger'" class="trend">
                                <i :class="summary.bank_deposit_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(summary.bank_deposit_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div class="summary-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Balance Card -->
    <div class="card border-0 shadow-sm mb-4" :class="summary.net_balance >= 0 ? 'border-start border-success' : 'border-start border-danger'" style="border-width: 4px;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <p class="text-muted small mb-1">Net Balance</p>
                    <small class="text-muted d-block mb-2">(Income - Expenses - ANZ 10984661 - ANZ Acc 13674771)</small>
                    <h2 :class="summary.net_balance >= 0 ? 'text-success' : 'text-danger'" x-text="hideAmounts ? '••••' : formatCurrency(summary.net_balance)"></h2>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div style="font-size: 3rem;" x-text="summary.net_balance >= 0 ? '📈' : '📉'"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Transactions Summary -->
    @if($summary['pending_count'] > 0)
    <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #f59e0b;">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-hourglass-half text-warning" style="font-size: 1.5rem;"></i>
                <h5 class="mb-0">Pending Transactions</h5>
            </div>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Count</small>
                    <h5 class="mb-0" x-text="summary.pending_count"></h5>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Total Amount</small>
                    <h5 class="mb-0" x-text="hideAmounts ? '••••' : formatCurrency(summary.pending_total)"></h5>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Income Pending</small>
                    <h5 class="mb-0 text-success" x-text="hideAmounts ? '••••' : formatCurrency(summary.pending_income)"></h5>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Expense Pending</small>
                    <h5 class="mb-0 text-danger" x-text="hideAmounts ? '••••' : formatCurrency(summary.pending_expense)"></h5>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Income vs Expense Chart -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">Income vs Expenses Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense by Category Chart -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">Expense Breakdown by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="expenseCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0">Recent Transactions</h6>
                <small class="text-muted">Total: {{ $transactions->total() }} transactions</small>
            </div>
        </div>
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 transaction-table">
                        <thead class="table-light">
                            <tr>
                                <th class="small fw-bold">Date</th>
                                <th class="small fw-bold">Type</th>
                                <th class="small fw-bold d-none d-md-table-cell">Category</th>
                                <th class="small fw-bold">Amount</th>
                                <th class="small fw-bold d-none d-lg-table-cell">Status</th>
                                <th class="small fw-bold d-none d-xl-table-cell">Description</th>
                                <th class="small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr class="transaction-row">
                                <td>
                                    <div class="transaction-date">{{ $transaction->transaction_date->format('M d') }}</div>
                                    <small class="transaction-date-day">{{ $transaction->transaction_date->format('l') }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'income' => 'success',
                                            'expense' => 'danger',
                                            'savings' => 'info',
                                            'bank_deposit' => 'warning'
                                        ];
                                    @endphp
                                    <span class="type-badge {{ $transaction->type }}">
                                        <i class="fas fa-{{ $transaction->type === 'income' ? 'arrow-up' : ($transaction->type === 'expense' ? 'arrow-down' : 'exchange-alt') }}"></i>
                                        <span class="d-none d-sm-inline">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="font-size: 1.25rem;">{{ $transaction->category->icon }}</span>
                                        <span class="small d-none d-lg-inline">{{ $transaction->category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="transaction-amount {{ $transaction->type }}">
                                        <span x-show="!$root.hideAmounts">${{ number_format($transaction->amount, 2) }}</span>
                                        <span x-show="$root.hideAmounts" class="text-muted">••••</span>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @php
                                        $statusColors = [
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="status-badge {{ $transaction->status }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <small class="text-muted text-truncate" style="max-width: 200px;" title="{{ $transaction->description }}">
                                        {{ $transaction->description ?? '-' }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div class="transaction-actions">
                                        <button @click="openEditModal({{ $transaction->id }})" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteTransaction({{ $transaction->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Showing <strong>{{ $transactions->firstItem() }}</strong> to
                                <strong>{{ $transactions->lastItem() }}</strong> of
                                <strong>{{ $transactions->total() }}</strong> results
                            </small>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Transactions pagination">
                                {{ $transactions->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5 class="empty-state-heading">No transactions found</h5>
                    <p class="empty-state-text">Start by adding your first transaction</p>
                    <button @click="openAddModal()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Transaction Modal -->
<div x-show="showModal" x-cloak x-transition class="modal" id="transactionModal" tabindex="-1" style="display: none;" :style="showModal && 'display: block;'">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" x-text="editMode ? 'Edit Transaction' : 'Add New Transaction'"></h5>
                <button type="button" class="btn-close" @click="closeModal()"></button>
            </div>

            <form @submit.prevent="submitTransaction()" class="modal-body">
                <div class="row g-4">
                    <!-- Transaction Date -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt label-icon"></i>
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" x-model="formData.transaction_date" :max="new Date().toISOString().split('T')[0]" required class="form-control">
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag label-icon"></i>
                                Type <span class="text-danger">*</span>
                            </label>
                            <select x-model="formData.type" @change="filterCategoriesByType()" required class="form-select">
                                <option value="">Select Type</option>
                                <option value="income">💰 Income</option>
                                <option value="expense">💸 Expense</option>
                                <option value="savings">🏦 Savings</option>
                                <option value="bank_deposit">🏧 Bank Deposit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-list label-icon"></i>
                                Category <span class="text-danger">*</span>
                            </label>
                            <select x-model.number="formData.category_id" required class="form-select">
                                <option value="">Select Category</option>
                                <template x-for="category in filteredCategories" :key="category.id">
                                    <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign label-icon"></i>
                                Amount <span class="text-danger">*</span>
                            </label>
                            <input type="number" x-model.number="formData.amount" step="0.01" min="0.01" required class="form-control" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-check-circle label-icon"></i>
                                Status <span class="text-danger">*</span>
                            </label>
                            <select x-model="formData.status" required class="form-select">
                                <option value="completed">✓ Completed</option>
                                <option value="pending">⏳ Pending</option>
                                <option value="cancelled">✕ Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reference Number -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-hashtag label-icon"></i>
                                Reference Number
                            </label>
                            <input type="text" x-model="formData.reference_number" class="form-control" placeholder="e.g., TXN-001">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-sticky-note label-icon"></i>
                                Description
                            </label>
                            <textarea x-model="formData.description" rows="3" class="form-control" placeholder="Add any notes or details about this transaction..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="modal-footer border-0 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                    <button type="button" @click="closeModal()" class="btn btn-outline-secondary" :disabled="isSubmitting">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">
                            <i :class="editMode ? 'fas fa-sync-alt' : 'fas fa-plus'" class="me-2"></i>
                            <span x-text="editMode ? 'Update Transaction' : 'Add Transaction'"></span>
                        </span>
                        <span x-show="isSubmitting">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Transaction Modal (Mobile) -->
<div x-show="showQuickAddModal" x-cloak x-transition class="modal" id="quickAddModal" tabindex="-1" style="display: none;" :style="showQuickAddModal && 'display: block;'">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">Quick Add Transaction</h5>
                <button type="button" class="btn-close" @click="closeQuickAddModal()"></button>
            </div>

            <form @submit.prevent="submitQuickAdd()" class="modal-body">
                <div class="row g-4">
                    <!-- Amount -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign label-icon"></i>
                                Amount <span class="text-danger">*</span>
                            </label>
                            <input type="number" x-model.number="quickAddForm.amount" data-quick-add-amount step="0.01" min="0.01" required class="form-control form-control-lg" placeholder="0.00" autofocus>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag label-icon"></i>
                                Type <span class="text-danger">*</span>
                            </label>
                            <select x-model="quickAddForm.type" required class="form-select form-select-lg">
                                <option value="expense">💸 Expense</option>
                                <option value="income">💰 Income</option>
                                <option value="savings">🏦 Savings</option>
                                <option value="bank_deposit">🏧 Bank Deposit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-list label-icon"></i>
                                Category <span class="text-danger">*</span>
                            </label>
                            <select x-model.number="quickAddForm.category_id" required class="form-select form-select-lg">
                                <option value="">Select Category</option>
                                <template x-for="category in quickAddFilteredCategories" :key="category.id">
                                    <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-sticky-note label-icon"></i>
                                Description (Optional)
                            </label>
                            <input type="text" x-model="quickAddForm.description" class="form-control" placeholder="Add notes about this transaction...">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="modal-footer border-0 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                    <button type="button" @click="closeQuickAddModal()" class="btn btn-outline-secondary flex-grow-1" :disabled="isSubmitting">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success flex-grow-1" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">
                            <i class="fas fa-plus me-2"></i>Add
                        </span>
                        <span x-show="isSubmitting">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Adding...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Backdrop -->
<div x-show="showModal || showQuickAddModal" x-cloak class="modal-backdrop fade" :class="(showModal || showQuickAddModal) && 'show'" style="display: none;" :style="(showModal || showQuickAddModal) && 'display: block;'"></div>
@endsection

@push('scripts')
<script>
    // Initialize financial categories data
    window.financialCategories = @json($categories);
    console.log('Financial categories loaded:', window.financialCategories);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="{{ asset('js/financial.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Financial dashboard page loaded');
        console.log('Alpine.js version:', window.Alpine ? 'Loaded' : 'Not loaded');
    });

    // Debug Alpine.js initialization
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized successfully');
    });

    // Error handling for any JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        console.error('Error message:', e.message);
        console.error('Error file:', e.filename);
        console.error('Error line:', e.lineno);
    });

    // Fallback for manual modal opening if Alpine.js fails
    window.openModalFallback = function() {
        console.log('Fallback modal function called');
        const modal = document.getElementById('transactionModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
        }
    };

    // Check if Alpine.js loaded properly after 3 seconds
    setTimeout(() => {
        if (!window.Alpine) {
            console.error('Alpine.js failed to load! Financial page functionality may be limited.');
            // Show a user-friendly message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Page functionality is limited. Please refresh the page.
                <button type="button" class="btn-close" onclick="this.parentNode.remove()"></button>
            `;
            document.body.appendChild(alertDiv);
        }
    }, 3000);
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/financial.css') }}">
<style>
    [x-cloak] { display: none !important; }
    .modal.show { display: block; }
    .modal-backdrop.show { opacity: 0.5; }
</style>
@endpush
