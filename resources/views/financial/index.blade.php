@extends('layouts.app')

@section('title', 'Financial Management - MyTime')

@section('content')
<div class="container-fluid py-4">
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
                    <!-- Add Transaction Button -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>

                    <!-- Export Button -->
                    <button type="button" class="btn btn-info btn-sm" onclick="exportTransactions()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Income Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Income</p>
                            <h4 class="mb-0">${{ number_format($summary['income'], 2) }}</h4>
                        </div>
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem;">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Expenses</p>
                            <h4 class="mb-0">${{ number_format($summary['expense'], 2) }}</h4>
                        </div>
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem;">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Savings</p>
                            <h4 class="mb-0">${{ number_format($summary['savings'], 2) }}</h4>
                        </div>
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem;">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Deposits Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Bank Deposits</p>
                            <h4 class="mb-0">${{ number_format($summary['bank_deposit'], 2) }}</h4>
                        </div>
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem;">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Balance Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid {{ $summary['net_balance'] >= 0 ? '#22c55e' : '#ef4444' }};">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <p class="text-muted small mb-1">Net Balance</p>
                    <h2 class="{{ $summary['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($summary['net_balance'], 2) }}
                    </h2>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div style="font-size: 3rem;">{{ $summary['net_balance'] >= 0 ? '📈' : '📉' }}</div>
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
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small fw-bold">Date</th>
                                <th class="small fw-bold">Type</th>
                                <th class="small fw-bold d-none d-md-table-cell">Category</th>
                                <th class="small fw-bold">Amount</th>
                                <th class="small fw-bold d-none d-lg-table-cell">Status</th>
                                <th class="small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $transaction->transaction_date->format('M d') }}</div>
                                    <small class="text-muted">{{ $transaction->transaction_date->format('l') }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: 
                                        @if($transaction->type === 'income') #dcfce7; color: #166534;
                                        @elseif($transaction->type === 'expense') #fee2e2; color: #991b1b;
                                        @elseif($transaction->type === 'savings') #cffafe; color: #164e63;
                                        @else #fce7f3; color: #831843;
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span style="font-size: 1.25rem;">{{ $transaction->category->icon }}</span>
                                    <span class="small d-none d-lg-inline">{{ $transaction->category->name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold" style="color: 
                                        @if($transaction->type === 'income') #22c55e;
                                        @elseif($transaction->type === 'expense') #ef4444;
                                        @elseif($transaction->type === 'savings') #06b6d4;
                                        @else #f59e0b;
                                        @endif
                                    ">
                                        ${{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge" style="background-color: 
                                        @if($transaction->status === 'completed') #dcfce7; color: #166534;
                                        @elseif($transaction->status === 'pending') #fef3c7; color: #92400e;
                                        @else #fee2e2; color: #991b1b;
                                        @endif
                                    ">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editTransaction({{ $transaction->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTransaction({{ $transaction->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                <div class="text-center p-5">
                    <i class="fas fa-inbox" style="font-size: 3.5rem; color: #d1d5db;"></i>
                    <h5 class="text-muted mt-3">No transactions found</h5>
                    <p class="text-muted">Start by adding your first transaction</p>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">Add New Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="transactionForm" method="POST" action="{{ route('financial.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Transaction Date -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="transaction_date" class="form-control" required max="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Type -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-tag me-2 text-primary"></i>Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-select" required onchange="filterCategories()">
                                <option value="">Select Type</option>
                                <option value="income">💰 Income</option>
                                <option value="expense">💸 Expense</option>
                                <option value="savings">🏦 Savings</option>
                                <option value="bank_deposit">🏧 Bank Deposit</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-list me-2 text-primary"></i>Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="categorySelect" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign me-2 text-primary"></i>Amount <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-check-circle me-2 text-primary"></i>Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="completed">✓ Completed</option>
                                <option value="pending">⏳ Pending</option>
                                <option value="cancelled">✕ Cancelled</option>
                            </select>
                        </div>

                        <!-- Reference Number -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-hashtag me-2 text-primary"></i>Reference Number
                            </label>
                            <input type="text" name="reference_number" class="form-control" placeholder="e.g., TXN-001">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>Description
                            </label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Add any notes or details about this transaction..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Set today's date as default
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="transaction_date"]');
        if (dateInput) {
            dateInput.valueAsDate = new Date();
        }
    });

    // Filter categories by type
    function filterCategories() {
        const typeSelect = document.querySelector('select[name="type"]');
        const categorySelect = document.getElementById('categorySelect');
        const selectedType = typeSelect.value;

        // Show/hide categories based on type
        Array.from(categorySelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const optionType = option.getAttribute('data-type');
                option.style.display = (optionType === selectedType) ? 'block' : 'none';
            }
        });

        // Reset category selection
        categorySelect.value = '';
    }

    // Edit transaction
    function editTransaction(id) {
        alert('Edit functionality coming soon!');
    }

    // Delete transaction
    function deleteTransaction(id) {
        if (confirm('Are you sure you want to delete this transaction?')) {
            fetch(`/financial/transaction/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Transaction deleted successfully');
                    location.reload();
                } else {
                    alert('Error deleting transaction: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting transaction');
            });
        }
    }

    // Export transactions
    function exportTransactions() {
        window.location.href = '/financial/export';
    }

    // Handle form submission
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('{{ route("financial.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction added successfully');
                location.reload();
            } else {
                const errors = data.errors || {};
                const errorMessages = Object.values(errors).flat().join('\n');
                alert('Error: ' + (errorMessages || data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding transaction: ' + error.message);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .modal-content {
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        color: #374151;
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(59, 175, 218, 0.1);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(59, 175, 218, 0.2);
    }

    @media (max-width: 640px) {
        .form-control, .form-select {
            font-size: 16px;
            padding: 0.65rem 0.75rem;
        }

        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endpush
