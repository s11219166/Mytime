// Alpine.js Component for Financial Dashboard
function financialDashboard() {
    return {
        // State
        showModal: false,
        editMode: false,
        hideAmounts: false,
        dateRange: '30',
        typeFilter: '',
        categoryFilter: '',

        // Categories data
        allCategories: [],

        // Form data
        formData: {
            transaction_date: new Date().toISOString().split('T')[0],
            type: '',
            category_id: '',
            amount: '',
            description: '',
            status: 'completed',
            reference_number: ''
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
            if (!this.formData.type) {
                return this.allCategories;
            }
            return this.allCategories.filter(cat => cat.type === this.formData.type);
        },

        // Initialize
        init() {
            this.loadCategories();
            this.loadSummary();
            this.initCharts();
            this.restorePrivacyPreference();

            // Auto-focus on first field when modal opens
            this.$watch('showModal', (value) => {
                if (value) {
                    setTimeout(() => {
                        this.$el.querySelector('input[type="date"]')?.focus();
                    }, 100);
                }
            });
        },

        // Load categories from page data
        loadCategories() {
            if (window.financialCategories) {
                this.allCategories = window.financialCategories;
            }
        },

        // Restore privacy preference
        restorePrivacyPreference() {
            const saved = localStorage.getItem('hideAmounts');
            if (saved !== null) {
                this.hideAmounts = saved === 'true';
            }
        },

        // Toggle privacy
        togglePrivacy() {
            this.hideAmounts = !this.hideAmounts;
            localStorage.setItem('hideAmounts', this.hideAmounts);
        },

        // Open add modal
        openAddModal() {
            this.editMode = false;
            this.resetForm();
            this.showModal = true;
        },

        // Open edit modal
        async openEditModal(id) {
            this.editMode = true;
            this.showModal = true;

            try {
                const response = await fetch(`/financial/transaction/${id}`);
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
            this.showModal = false;
            setTimeout(() => this.resetForm(), 300);
        },

        // Reset form
        resetForm() {
            this.formData = {
                transaction_date: new Date().toISOString().split('T')[0],
                type: '',
                category_id: '',
                amount: '',
                description: '',
                status: 'completed',
                reference_number: ''
            };
        },

        // Filter categories by type
        filterCategoriesByType() {
            this.formData.category_id = '';
        },

        // Submit transaction
        async submitTransaction() {
            const url = this.editMode
                ? `/financial/transaction/${this.formData.id}`
                : '/financial/transaction';

            const method = this.editMode ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(
                        this.editMode ? 'Transaction updated successfully' : 'Transaction created successfully',
                        'success'
                    );
                    this.closeModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    this.showNotification(data.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error submitting transaction:', error);
                this.showNotification('Failed to save transaction', 'error');
            }
        },

        // Delete transaction
        async deleteTransaction(id) {
            if (!confirm('Are you sure you want to delete this transaction?')) {
                return;
            }

            try {
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
            this.dateRange = '30';
            this.typeFilter = '';
            this.categoryFilter = '';
            this.fetchData();
        },

        // Fetch data with filters
        async fetchData() {
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
            this.initIncomeExpenseChart();
            this.initExpenseCategoryChart();
            this.loadChartData();
        },

        // Initialize income vs expense chart
        initIncomeExpenseChart() {
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
        },

        // Initialize expense category chart
        initExpenseCategoryChart() {
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
        },

        // Update expense category chart
        updateExpenseCategoryChart(expenseByCategory) {
            if (!this.expenseCategoryChart) return;

            const labels = expenseByCategory.map(item => item.category.name);
            const data = expenseByCategory.map(item => parseFloat(item.total));

            this.expenseCategoryChart.data.labels = labels;
            this.expenseCategoryChart.data.datasets[0].data = data;
            this.expenseCategoryChart.update();
        },

        // Update charts
        updateCharts() {
            this.loadChartData();
        },

        // Export data
        exportData() {
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
        },

        // Format currency
        formatCurrency(amount) {
            return '$' + parseFloat(amount || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        // Show notification
        showNotification(message, type = 'success') {
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
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    };
}
