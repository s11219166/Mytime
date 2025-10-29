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
            bank_deposit_trend: 0
        },

        // Charts
        incomeExpenseChart: null,
        expenseCategoryChart: null,

        // Initialize
        init() {
            this.loadSummary();
            this.initCharts();

            // Auto-focus on first field when modal opens
            this.$watch('showModal', (value) => {
                if (value) {
                    setTimeout(() => {
                        this.$el.querySelector('input[type="date"]')?.focus();
                    }, 100);
                }
            });
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
                    this.formData = {
                        id: data.transaction.id,
                        transaction_date: data.transaction.transaction_date,
                        type: data.transaction.type,
                        category_id: data.transaction.category_id,
                        amount: data.transaction.amount,
                        description: data.transaction.description || '',
                        status: data.transaction.status,
                        reference_number: data.transaction.reference_number || ''
                    };
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
            // Category filtering happens in the template with x-show
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

                    // Reload page to show updated data
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

        // Fetch data with filters
        async fetchData() {
            await this.loadSummary();
            this.updateCharts();

            // Build URL with filters
            const params = new URLSearchParams({
                date_range: this.dateRange
            });

            if (this.typeFilter) {
                params.append('type', this.typeFilter);
            }

            if (this.categoryFilter) {
                params.append('category_id', this.categoryFilter);
            }

            // Reload page with filters
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
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: [],
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
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
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
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

            // Group data by date
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
            // You can integrate with your existing notification system
            // For now, using browser alert
            if (type === 'success') {
                alert('✓ ' + message);
            } else {
                alert('✗ ' + message);
            }
        }
    };
}
