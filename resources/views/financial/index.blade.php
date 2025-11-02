@extends('layouts.app')

@section('title', 'Financial Management - MyTime')

@section('content')
<div class="container-fluid py-4" x-data="financialDashboard()">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="h3 fw-bold mb-1">
                    <i class="fas fa-wallet me-2 text-primary"></i>Financial Management
                </h1>
                <p class="text-muted mb-0">Track your income, expenses, savings, and bank deposits</p>
            </div>
            <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    <!-- Privacy Toggle -->
                    <button @click="togglePrivacy()" class="btn btn-outline-secondary btn-sm">
                        <i class="fas" :class="hideAmounts ? 'fa-eye-slash' : 'fa-eye'" style="margin-right: 0.5rem;"></i>
                        <span x-text="hideAmounts ? 'Show Amounts' : 'Hide Amounts'"></span>
                    </button>

                    <!-- Add Transaction Button -->
                    <button @click="openAddModal()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>

                    <!-- Export Button -->
                    <button @click="exportData()" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Date Range Filter -->
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">Date Range</label>
                    <select x-model="dateRange" @change="fetchData()" class="form-select form-select-sm">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">Type</label>
                    <select x-model="typeFilter" @change="fetchData()" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="savings">Savings</option>
                        <option value="bank_deposit">Bank Deposit</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">Category</label>
                    <select x-model="categoryFilter" @change="fetchData()" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <button @click="clearFilters()" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-redo me-2"></i>Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Income Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Income</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '****' : formatCurrency(summary.income)"></h4>
                            <small :class="summary.income_trend >= 0 ? 'text-success' : 'text-danger'">
                                <i :class="summary.income_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-right: 0.25rem;"></i>
                                <span x-text="Math.abs(summary.income_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Expenses</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '****' : formatCurrency(summary.expense)"></h4>
                            <small :class="summary.expense_trend <= 0 ? 'text-success' : 'text-danger'">
                                <i :class="summary.expense_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-right: 0.25rem;"></i>
                                <span x-text="Math.abs(summary.expense_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">ANZ 10984661</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '****' : formatCurrency(summary.savings)"></h4>
                            <small :class="summary.savings_trend >= 0 ? 'text-success' : 'text-danger'">
                                <i :class="summary.savings_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-right: 0.25rem;"></i>
                                <span x-text="Math.abs(summary.savings_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Deposits Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">ANZ Acc 13674771</p>
                            <h4 class="mb-2" x-text="hideAmounts ? '****' : formatCurrency(summary.bank_deposit)"></h4>
                            <small :class="summary.bank_deposit_trend >= 0 ? 'text-success' : 'text-danger'">
                                <i :class="summary.bank_deposit_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" style="margin-right: 0.25rem;"></i>
                                <span x-text="Math.abs(summary.bank_deposit_trend) + '% vs previous'"></span>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
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
                <div class="col-lg-8">
                    <p class="text-muted small mb-1">Net Balance</p>
                    <small class="text-muted d-block mb-2">(Income - Expenses - ANZ 10984661 - ANZ Acc 13674771)</small>
                    <h2 :class="summary.net_balance >= 0 ? 'text-success' : 'text-danger'" x-text="hideAmounts ? '****' : formatCurrency(summary.net_balance)"></h2>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div style="font-size: 3rem;" x-text="summary.net_balance >= 0 ? '📈' : '📉'"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Transactions Summary -->
    @if($summary['pending_count'] > 0)
    <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #f59e0b;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-hourglass-half text-warning" style="font-size: 1.5rem;"></i>
                        <h5 class="mb-0">Pending Transactions</h5>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Count</small>
                            <h5 class="mb-0" x-text="summary.pending_count"></h5>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Total Amount</small>
                            <h5 class="mb-0" x-text="hideAmounts ? '****' : formatCurrency(summary.pending_total)"></h5>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Income Pending</small>
                            <h5 class="mb-0 text-success" x-text="hideAmounts ? '****' : formatCurrency(summary.pending_income)"></h5>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Expense Pending</small>
                            <h5 class="mb-0 text-danger" x-text="hideAmounts ? '****' : formatCurrency(summary.pending_expense)"></h5>
                        </div>
                    </div>
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
                    <div style="height: 300px; position: relative;">
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
                    <div style="height: 300px; position: relative;">
                        <canvas id="expenseCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Transactions</h6>
                <small class="text-muted">Total: {{ $transactions->total() }} transactions</small>
            </div>
        </div>
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                            <tr>
                                <td>
                                    <div class="small fw-semibold">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $transaction->transaction_date->format('l') }}</small>
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
                                    <span class="badge bg-{{ $typeColors[$transaction->type] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="font-size: 1.25rem;">{{ $transaction->category->icon }}</span>
                                        <span class="small">{{ $transaction->category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold" :class="'{{ $transaction->type }}' === 'income' ? 'text-success' : '{{ $transaction->type }}' === 'expense' ? 'text-danger' : 'text-info'">
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
                                    <span class="badge bg-{{ $statusColors[$transaction->status] ?? 'secondary' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <small class="text-muted text-truncate" style="max-width: 200px;" title="{{ $transaction->description }}">
                                        {{ $transaction->description ?? '-' }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button @click="openEditModal({{ $transaction->id }})" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteTransaction({{ $transaction->id }})" class="btn btn-outline-danger" title="Delete">
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
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                    <h5 class="mt-3 text-muted">No transactions found</h5>
                    <p class="text-muted mb-3">Start by adding your first transaction</p>
                    <button @click="openAddModal()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Transaction Modal -->
<div x-show="showModal" x-cloak class="modal fade" id="transactionModal" tabindex="-1" :class="showModal ? 'show d-block' : ''">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" x-text="editMode ? 'Edit Transaction' : 'Add New Transaction'"></h5>
                <button type="button" class="btn-close" @click="closeModal()"></button>
            </div>

            <form @submit.prevent="submitTransaction()" class="modal-body">
                <div class="row g-3">
                    <!-- Transaction Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" x-model="formData.transaction_date" :max="new Date().toISOString().split('T')[0]" required class="form-control">
                    </div>

                    <!-- Type -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Type <span class="text-danger">*</span></label>
                        <select x-model="formData.type" @change="filterCategoriesByType()" required class="form-select">
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                            <option value="savings">Savings</option>
                            <option value="bank_deposit">Bank Deposit</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                        <select x-model="formData.category_id" required class="form-select">
                            <option value="">Select Category</option>
                            <template x-for="category in filteredCategories" :key="category.id">
                                <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Amount <span class="text-danger">*</span></label>
                        <input type="number" x-model="formData.amount" step="0.01" min="0.01" required class="form-control" placeholder="0.00">
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                        <select x-model="formData.status" required class="form-select">
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Reference Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Reference Number</label>
                        <input type="text" x-model="formData.reference_number" class="form-control" placeholder="Optional">
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea x-model="formData.description" rows="3" class="form-control" placeholder="Add any notes or details..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="modal-footer border-0 mt-4">
                    <button type="button" @click="closeModal()" class="btn btn-outline-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span x-text="editMode ? 'Update Transaction' : 'Add Transaction'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Backdrop -->
<div x-show="showModal" x-cloak class="modal-backdrop fade" :class="showModal ? 'show' : ''"></div>
@endsection

@push('scripts')
<script>
    window.financialCategories = @json($categories);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="{{ asset('js/financial.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Financial dashboard initialized');
    });
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
