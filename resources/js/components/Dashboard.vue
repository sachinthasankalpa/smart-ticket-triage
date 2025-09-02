<template>
    <div class="dashboard dashboard-container">
        <div class="dashboard__header dashboard-header">
            <h2 class="dashboard__title">Dashboard</h2>
        </div>
        <div v-if="isLoading" class="dashboard__loader loader">Loading stats...</div>
        <div v-else class="dashboard__grid dashboard-grid">
            <div class="card dashboard__card">
                <h3 class="card__title">Total Tickets</h3>
                <p class="card__stat-number stat-number">{{ stats.total_tickets }}</p>
            </div>
            <div class="card dashboard__card">
                <h3 class="card__title">Tickets by Status</h3>
                <ul class="card__stat-list stat-list" v-if="stats.status_counts">
                    <li class="card__stat-list-item" v-for="(count, status) in stats.status_counts" :key="status">
                        <span class="card__stat-label">{{ status.replace('_', ' ') }}:</span>
                        <strong class="card__stat-value">{{ count }}</strong>
                    </li>
                </ul>
                <p v-else>No data.</p>
            </div>
            <div class="card dashboard__card">
                <h3 class="card__title">Tickets by Category</h3>
                <ul class="card__stat-list stat-list" v-if="stats.category_counts">
                    <li class="card__stat-list-item" v-for="(count, category) in stats.category_counts" :key="category">
                        <span class="card__stat-label">{{ (category || 'N/A').replace('_', ' ') }}:</span>
                        <strong class="card__stat-value">{{ count }}</strong>
                    </li>
                </ul>
                <p v-else>No data.</p>
            </div>
            <div class="card dashboard__card card--chart chart-card">
                <h3 class="card__title">Category Distribution</h3>
                <div class="card__chart-wrapper chart-wrapper">
                    <canvas class="card__chart category-chart" id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue';
import {ticketStore} from '../store/tickets';
import {themeStore} from '../store/theme';
import Chart from 'chart.js/auto';

export default defineComponent({
    name: 'Dashboard',
    data() {
        return {
            chartInstance: null
        };
    },
    computed: {
        stats() {
            return ticketStore.state.stats;
        },
        isLoading() {
            return ticketStore.state.isLoading;
        },
        currentTheme() {
            return themeStore.state.currentTheme;
        },
        isDarkMode() {
            return this.currentTheme === 'dark';
        }
    },
    watch: {
        stats(newStats) {
            if (newStats && !this.isLoading) {
                this.$nextTick(() => {
                    this.createChart();
                });
            }
        },
        currentTheme() {
            this.createChart();
        }
    },
    methods: {
        createChart() {
            if (this.chartInstance) {
                this.chartInstance.destroy();
            }
            const ctx = document.getElementById('categoryChart');
            if (!ctx || !this.stats.category_counts) return;

            const labels = Object.keys(this.stats.category_counts).map(c => (c || 'N/A').replace('_', ' '));
            const data = Object.values(this.stats.category_counts);
            const textColor = this.isDarkMode ? '#f1f5f9' : '#1e293b';

            this.chartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tickets',
                        data: data,
                        backgroundColor: [
                            'rgba(37, 99, 235, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                        ],
                        borderColor: this.isDarkMode ? '#1e293b' : '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: textColor
                            }
                        }
                    }
                }
            });
        }
    },
    mounted() {
        ticketStore.fetchStats();
    },
    beforeUnmount() {
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }
    }
});
</script>

<style scoped>
.dashboard-container {
    max-width: 1100px;
    margin: auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.card {
    background: var(--card-background);
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    transition: background-color 0.3s;
}

.card.chart-card {
    grid-column: 1 / -1;
    height: 400px;
    display: flex;
    flex-direction: column;
}

.chart-wrapper {
    position: relative;
    flex-grow: 1;
    min-height: 0;
}

.category-chart {
    width: 100%;
    height: 100%;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.stat-list {
    list-style: none;
    padding: 0;
    margin: 0;
    text-transform: capitalize;
}

.stat-list li {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
    transition: border-color 0.3s;
}

.stat-list li:last-child {
    border-bottom: none;
}
</style>
