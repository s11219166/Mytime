// Alpine.js Component for Financial Dashboard
function financialDashboard() {
    return {
        // State
        showModal: false,
        showQuickAddModal: false,
        editMode: false,
        hideAmounts: false,
        dateRange: '30',
        typeFilter: '',
        categoryFilter: '',
        isSubmitting: false,
        searchQuery: '',
        sortBy: 'date_desc',

        // Categories data
        allCategories: [],

        // Form data
        formData: {
            id: null,
            transaction_date: new Date().toISOString().split('T')[0],
            type: '',
            category_id: '',
            amount: '',
            description: '',
            status: 'completed',
            reference_number: ''
        },

        // Quick add form
        quickAddForm: {
            amount: '',
            type: 'expense',
            category_id: '',
            description: ''
        },

        // Summary data
        summary: {
            income: 0,
            expense: 0,
            savings: 0,
            bank_deposit: 0,
            net_balance: 0,
            income_trend: 0,
            expense_trend: 0,
            savings_trend: 0,
            bank_deposit_trend: 0,
            pending_count: 0,
            pending_total: 0,
            pending_income: 0,
            pending_expense: 0
        },

        // Charts
        incomeExpenseChart: null,
        expenseCategoryChart: null,

        // Computed: Filtered categories based on selected type
        get filteredCategories() {
            try {
                if (!this.formData.type) {
                    return this.allCategories;
                }
                return this.allCategories.filter(cat => cat.type === this.formData.type);
            } catch (error) {
                console.error('Error filtering categories:', error);
                return [];
            }
        },

        get quickAddFilteredCategories() {
            try {
                if (!this.quickAddForm.type) {
                    return this.allCategories;
                }
                return this.allCategories.filter(cat => cat.type === this.quickAddForm.type);
            } catch (error) {
                console.error('Error filtering quick add categories:', error);
                return [];
            }
        },

        // Initialize
        init() {
            try {
                console.log('Initializing financial dashboard...');
                this.loadCategories();
                this.loadSummary();
                this.restorePrivacyPreference();

                // Initialize charts after a small delay to ensure DOM is ready
                setTimeout(() => {
                    try {
                        this.initCharts();
                    } catch (chartError) {
                        console.error('Error initializing charts:', chartError);
                    }
                }, 100);

                // Auto-focus on first field when modal opens
                this.$watch('showModal', (value) => {
                    try {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                            setTimeout(() => {
                                const dateInput = document.querySelector('input[type="date"]');
                                if (dateInput) {
                                    dateInput.focus();
                                }
                            }, 100);
                        } else {
                            document.body.style.overflow = 'auto';
                        }
                    } catch (error) {
                        console.error('Error in modal watch:', error);
                    }
                });

                // Quick add modal
                this.$watch('showQuickAddModal', (value) => {
                    try {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                            setTimeout(() => {
                                const amountInput = document.querySelector('[data-quick-add-amount]');
                                if (amountInput) {
                                    amountInput.focus();
                                }
                            }, 100);
                        } else {
                            document.body.style.overflow = 'auto';
                        }
                    } catch (error) {
                        console.error('Error in quick add modal watch:', error);
                    }
                });

                console.log('Financial dashboard initialized successfully');
            } catch (error) {
                console.error('Error initializing financial dashboard:', error);
                this.showNotification('Error initializing dashboard. Please refresh the page.', 'error');
            }
        },

        // Load categories from page data
        loadCategories() {
            try {
                if (window.financialCategories) {
                    this.allCategories = window.financialCategories;
                    console.log('Categories loaded:', this.allCategories.length);
                } else {
                    console.warn('No financial categories found in window object');
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        // Restore privacy preference
        restorePrivacyPreference() {
            try {
                const saved = localStorage.getItem('hideAmounts');
                if (saved !== null) {
                    this.hideAmounts = saved === 'true';
                }
            } catch (error) {
                console.error('Error restoring privacy preference:', error);
            }
        },

        // Toggle privacy
        togglePrivacy() {
            try {
                this.hideAmounts = !this.hideAmounts;
                localStorage.setItem('hideAmounts', this.hideAmounts);
            } catch (error) {
                console.error('Error toggling privacy:', error);
            }
        },

        // Open add modal
        openAddModal() {
            try {
                console.log('Opening add modal...');
                this.editMode = false;
                this.resetForm();
                this.showModal = true;
            } catch (error) {
                console.error('Error opening add modal:', error);
                this.showNotification('Error opening form. Please try again.', 'error');
            }
        },

        // Open quick add modal
        openQuickAddModal() {
            try {
                console.log('Opening quick add modal...');
                this.quickAddForm = {
                    amount: '',
                    type: 'expense',
                    category_id: '',
                    description: ''
                };
                this.showQuickAddModal = true;
            } catch (error) {
                console.error('Error opening quick add modal:', error);
                this.showNotification('Error opening quick add form. Please try again.', 'error');
            }
        },

        // Close quick add modal
        closeQuickAddModal() {
            try {
                this.showQuickAddModal = false;
            } catch (error) {
                console.error('Error closing quick add modal:', error);
            }
        },

        // Submit quick add
        async submitQuickAdd() {
            try {
                console.log('Submitting quick add...');

                if (!this.quickAddForm.amount || !this.quickAddForm.type || !this.quickAddForm.category_id) {
                    this.showNotification('Please fill in all required fields', 'error');
                    return;
                }

                this.isSubmitting = true;

                const response = await fetch('/financial/transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_date: new Date().toISOString().split('T')[0],
                        type: this.quickAddForm.type,
                        category_id: this.quickAddForm.category_id,
                        amount: this.quickAddForm.amount,
                        description: this.quickAddForm.description,
                        status: 'completed',
                        reference_number: ''
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification('Transaction added successfully', 'success');
                    this.closeQuickAddModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    this.showNotification(data.message || 'An error occurred', 'error');
                    this.isSubmitting = false;
                }
            } catch (error) {
                console.error('Error submitting quick add:', error);
                this.showNotification('Failed to save transaction: ' + error.message, 'error');
                this.isSubmitting = false;
            }
        },

        // Open edit modal
        async openEditModal(id) {
            try {
                console.log('Opening edit modal for transaction:', id);
                this.editMode = true;
                this.showModal = true;

                const response = await fetch(`/financial/transaction/${id}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    let formattedDate = data.transaction.transaction_date;
                    if (formattedDate && formattedDate.includes('T')) {
                        formattedDate = formattedDate.split('T')[0];
                    } else if (formattedDate && formattedDate.includes(' ')) {
                        formattedDate = formattedDate.split(' ')[0];
                    }

                    this.formData = {
                        id: data.transaction.id,
                        transaction_date: formattedDate,
                        type: data.transaction.type,
                        category_id: data.transaction.category_id,
                        amount: data.transaction.amount,
                        description: data.transaction.description || '',
                        status: data.transaction.status,
                        reference_number: data.transaction.reference_number || ''
                    };

                    this.filterCategoriesByType();
                }
            } catch (error) {
                console.error('Error loading transaction:', error);
                this.showNotification('Failed to load transaction', 'error');
            }
        },

        // Close modal
        closeModal() {
            try {
                this.showModal = false;
                setTimeout(() => this.resetForm(), 300);
            } catch (error) {
                console.error('Error closing modal:', error);
            }
        },

        // Reset form
        resetForm() {
            try {
                this.formData = {
                    id: null,
                    transaction_date: new Date().toISOString().split('T')[0],
                    type: '',
                    category_id: '',
                    amount: '',
                    description: '',
                    status: 'completed',
                    reference_number: ''
                };
            } catch (error) {
                console.error('Error resetting form:', error);
            }
        },

        // Filter categories by type
        filterCategoriesByType() {
            try {
                this.formData.category_id = '';
            } catch (error) {
                console.error('Error filtering categories by type:', error);
            }
        },

        // Submit transaction
        async submitTransaction() {
            try {
                console.log('Submitting transaction...');

                // Validate required fields
                if (!this.formData.transaction_date || !this.formData.type || !this.formData.category_id || !this.formData.amount) {
                    this.showNotification('Please fill in all required fields', 'error');
                    return;
                }

                this.isSubmitting = true;

                const url = this.editMode
                    ? `/financial/transaction/${this.formData.id}`
                    : '/financial/transaction';

                const method = this.editMode ? 'PUT' : 'POST';

                // Prepare data for submission
                const submitData = {
                    transaction_date: this.formData.transaction_date,
                    type: this.formData.type,
                    category_id: parseInt(this.formData.category_id),
                    amount: parseFloat(this.formData.amount),
                    description: this.formData.description || '',
                    status: this.formData.status,
                    reference_number: this.formData.reference_number || ''
                };

                // Add id for edit mode
                if (this.editMode) {
                    submitData.id = this.formData.id;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(submitData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification(
                        this.editMode ? 'Transaction updated successfully' : 'Transaction created successfully',
                        'success'
                    );
                    this.closeModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        this.showNotification(errorMessages, 'error');
                    } else {
                        this.showNotification(data.message || 'An error occurred', 'error');
                    }
                    this.isSubmitting = false;
                }
            } catch (error) {
                console.error('Error submitting transaction:', error);
                this.showNotification('Failed to save transaction: ' + error.message, 'error');
                this.isSubmitting = false;
            }
        },

        // Delete transaction
        async deleteTransaction(id) {
            try {
                if (!confirm('Are you sure you want to delete this transaction?')) {
                    return;
                }

                const response = await fetch(`/financial/transaction/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Transaction deleted successfully', 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    this.showNotification(data.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error deleting transaction:', error);
                this.showNotification('Failed to delete transaction', 'error');
            }
        },

        // Clear filters
        clearFilters() {
            try {
                this.dateRange = '30';
                this.typeFilter = '';
                this.categoryFilter = '';
                this.searchQuery = '';
                this.fetchData();
            } catch (error) {
                console.error('Error clearing filters:', error);
            }
        },

        // Fetch data with filters
        async fetchData() {
            try {
                await this.loadSummary();
                this.updateCharts();

                const params = new URLSearchParams({
                    date_range: this.dateRange
                });

                if (this.typeFilter) {
                    params.append('type', this.typeFilter);
                }

                if (this.categoryFilter) {
                    params.append('category_id', this.categoryFilter);
                }

                window.location.href = `/financial?${params.toString()}`;
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        },

        // Load summary data
        async loadSummary() {
            try {
                const params = new URLSearchParams({
                    date_range: this.dateRange
                });

                const response = await fetch(`/financial/summary?${params.toString()}`);
                const data = await response.json();

                this.summary = data;
            } catch (error) {
                console.error('Error loading summary:', error);
            }
        },

        // Initialize charts
        initCharts() {
            try {
                this.initIncomeExpenseChart();
                this.initExpenseCategoryChart();
                this.loadChartData();
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        },

        // Initialize income vs expense chart
        initIncomeExpenseChart() {
            try {
                const ctx = document.getElementById('incomeExpenseChart');
                if (!ctx) return;

                this.incomeExpenseChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'Income',
                                data: [],
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2
                            },
                            {
                                label: 'Expenses',
                                data: [],
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing income expense chart:', error);
            }
        },

        // Initialize expense category chart
        initExpenseCategoryChart() {
            try {
                const ctx = document.getElementById('expenseCategoryChart');
                if (!ctx) return;

                this.expenseCategoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [
                                '#ef4444', '#f97316', '#f59e0b', '#eab308',
                                '#84cc16', '#22c55e', '#10b981', '#14b8a6',
                                '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1'
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = '$' + context.parsed.toLocaleString();
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing expense category chart:', error);
            }
        },

        // Load chart data
        async loadChartData() {
            try {
                const params = new URLSearchParams({
                    date_range: this.dateRange
                });

                const response = await fetch(`/financial/chart-data?${params.toString()}`);
                const data = await response.json();

                this.updateIncomeExpenseChart(data.dailyData);
                this.updateExpenseCategoryChart(data.expenseByCategory);
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        },

        // Update income expense chart
        updateIncomeExpenseChart(dailyData) {
            try {
                if (!this.incomeExpenseChart) return;

                const dateMap = new Map();

                dailyData.forEach(item => {
                    if (!dateMap.has(item.date)) {
                        dateMap.set(item.date, { income: 0, expense: 0 });
                    }

                    if (item.type === 'income') {
                        dateMap.get(item.date).income = parseFloat(item.total);
                    } else if (item.type === 'expense') {
                        dateMap.get(item.date).expense = parseFloat(item.total);
                    }
                });

                const sortedDates = Array.from(dateMap.keys()).sort();
                const incomeData = sortedDates.map(date => dateMap.get(date).income);
                const expenseData = sortedDates.map(date => dateMap.get(date).expense);

                this.incomeExpenseChart.data.labels = sortedDates;
                this.incomeExpenseChart.data.datasets[0].data = incomeData;
                this.incomeExpenseChart.data.datasets[1].data = expenseData;
                this.incomeExpenseChart.update();
            } catch (error) {
                console.error('Error updating income expense chart:', error);
            }
        },

        // Update expense category chart
        updateExpenseCategoryChart(expenseByCategory) {
            try {
                if (!this.expenseCategoryChart) return;

                const labels = expenseByCategory.map(item => item.category.name);
                const data = expenseByCategory.map(item => parseFloat(item.total));

                this.expenseCategoryChart.data.labels = labels;
                this.expenseCategoryChart.data.datasets[0].data = data;
                this.expenseCategoryChart.update();
            } catch (error) {
                console.error('Error updating expense category chart:', error);
            }
        },

        // Update charts
        updateCharts() {
            try {
                this.loadChartData();
            } catch (error) {
                console.error('Error updating charts:', error);
            }
        },

        // Export data
        exportData() {
            try {
                const params = new URLSearchParams({
                    date_range: this.dateRange
                });

                if (this.typeFilter) {
                    params.append('type', this.typeFilter);
                }

                if (this.categoryFilter) {
                    params.append('category_id', this.categoryFilter);
                }

                window.location.href = `/financial/export?${params.toString()}`;
            } catch (error) {
                console.error('Error exporting data:', error);
                this.showNotification('Error exporting data', 'error');
            }
        },

        // Format currency
        formatCurrency(amount) {
            try {
                return '$' + parseFloat(amount || 0).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } catch (error) {
                console.error('Error formatting currency:', error);
                return '$0.00';
            }
        },

        // Show notification with improved error handling
        showNotification(message, type = 'success') {
            try {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
                    <i class="fas ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(alertDiv);

                // Auto-remove notification after 5 seconds
                setTimeout(() => {
                    try {
                        if (alertDiv && alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    } catch (removeError) {
                        console.error('Error removing notification:', removeError);
                    }
                }, 5000);

                // Also set up click handler for manual dismissal
                const closeBtn = alertDiv.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        try {
                            if (alertDiv && alertDiv.parentNode) {
                                alertDiv.remove();
                            }
                        } catch (closeError) {
                            console.error('Error closing notification:', closeError);
                        }
                    });
                }

            } catch (error) {
                console.error('Error showing notification:', error);
                // Fallback to alert if notification system fails
                alert(message);
            }
        }
    };
}

// Global error handler for any unhandled errors
window.addEventListener('error', function(event) {
    console.error('Global error:', event.error);
});

// Ensure DOM is loaded before Alpine.js runs
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, Alpine.js should initialize...');
});
