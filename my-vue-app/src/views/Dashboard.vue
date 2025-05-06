<template>
  <div class="dashboard-container">
    <Sidebar />
    
    <div style="margin-top: -70px;" class="dashboard-content">
      <!-- Dashboard Header -->
      <div class="dashboard-header">
        <div>
          <h1 style="    margin-left: -67%;" class="dashboard-title">Dashboard</h1>
          <p class="dashboard-subtitle">
            Overview of your rental management system as of {{ currentDate }}
          </p>
        </div>
        
        <div class="dashboard-actions">
          <button @click="refreshData" class="refresh-btn">
            <i class="fas fa-sync-alt" :class="{ 'fa-spin': isLoading }"></i>
            <span>Refresh</span>
          </button>
        </div>
      </div>
      
      <!-- Error Alert -->
      <div v-if="error" class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ error.message || 'An error occurred while loading data' }}</p>
      </div>
      
      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card rental-card">
          <div class="stat-icon">
            <i class="fas fa-truck-loading"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">Active Rentals</h3>
            <div class="stat-value">24</div>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 8% from last week
            </div>
          </div>
        </div>
        
        <div class="stat-card inventory-card">
          <div class="stat-icon">
            <i class="fas fa-boxes"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">Total Inventory</h3>
            <div class="stat-value">156</div>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 3% from last month
            </div>
          </div>
        </div>
        
        <div class="stat-card pending-card">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">Pending Returns</h3>
            <div class="stat-value">8</div>
            <div class="stat-trend negative">
              <i class="fas fa-arrow-down"></i> 5% from last week
            </div>
          </div>
        </div>
        
        <div class="stat-card revenue-card">
          <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-title">Monthly Revenue</h3>
            <div class="stat-value">$4,250</div>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 12% from last month
            </div>
          </div>
        </div>
      </div>
      
      <!-- Charts Section -->
      <div class="charts-grid">
        <!-- Rental Trends Chart -->
        <div class="chart-card large">
          <div class="chart-header">
            <h3>Rental Trends</h3>
            <div class="chart-actions">
              <select v-model="rentalTrendPeriod" class="period-selector">
                <option value="week">Last Week</option>
                <option value="month">Last Month</option>
                <option value="quarter">Last Quarter</option>
              </select>
            </div>
          </div>
          <div class="chart-body">
            <LineChart :chartData="rentalTrendsData" />
          </div>
        </div>
        
        <!-- Inventory Status Chart -->
        <div class="chart-card">
          <div class="chart-header">
            <h3>Inventory Status</h3>
          </div>
          <div class="chart-body">
            <DoughnutChart :chartData="inventoryStatusData" />
          </div>
        </div>
        
        <!-- Category Performance Chart -->
        <div class="chart-card">
          <div class="chart-header">
            <h3>Category Performance</h3>
          </div>
          <div class="chart-body">
            <BarChart :chartData="categoryPerformanceData" />
          </div>
        </div>
      </div>
      
      <!-- Recent Activity & Deadlines Section -->
      <div class="tables-grid">
        <!-- Recent Activity Table -->
        <div class="table-card">
          <div class="table-header">
            <h3>Recent Activity</h3>
            <router-link to="/rental-list" class="view-all-btn">
              View All <i class="fas fa-arrow-right"></i>
            </router-link>
          </div>
          <div class="table-body">
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Item</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(activity, index) in recentActivities" :key="index">
                  <td>{{ formatDate(activity.date) }}</td>
                  <td class="customer-cell">{{ activity.customer }}</td>
                  <td>{{ activity.item }}</td>
                  <td>
                    <span :class="['status-badge', getStatusClass(activity.status)]">
                      {{ activity.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Upcoming Deadlines -->
        <div class="table-card">
          <div class="table-header">
            <h3>Upcoming Deadlines</h3>
            <button class="action-btn">
              <i class="fas fa-bell"></i>
            </button>
          </div>
          <div class="table-body">
            <div v-for="(deadline, index) in upcomingDeadlines" :key="index" class="deadline-item">
              <div class="deadline-content">
                <h4>{{ deadline.customer }}</h4>
                <p>{{ deadline.item }} - {{ deadline.duration }}</p>
              </div>
              <div class="deadline-date">
                <div class="days-remaining" :class="getDaysRemainingClass(deadline.dueDate)">
                  {{ getDaysRemaining(deadline.dueDate) }}
                </div>
                <div class="due-date">{{ formatDate(deadline.dueDate) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" :message="loadingMessage" />
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import LineChart from '../components/charts/LineChart.vue';
import BarChart from '../components/charts/BarChart.vue';
import DoughnutChart from '../components/charts/DoughnutChart.vue';
import pageFunctionality from '../mixins/pageFunctionality';

export default {
  name: 'Dashboard',
  components: {
    Sidebar,
    LoadingSpinner,
    LineChart,
    BarChart,
    DoughnutChart
  },
  mixins: [pageFunctionality],
  data() {
    return {
      rentalTrendPeriod: 'month',
      recentActivities: [
        { date: '2025-05-01', customer: 'John Smith', item: 'Power Generator', status: 'Rented' },
        { date: '2025-04-30', customer: 'Sarah Johnson', item: 'Floor Sander', status: 'Returned' },
        { date: '2025-04-29', customer: 'Mike Williams', item: 'Concrete Mixer', status: 'Overdue' },
        { date: '2025-04-28', customer: 'Emily Brown', item: 'Pressure Washer', status: 'Rented' },
        { date: '2025-04-27', customer: 'David Miller', item: 'Scissor Lift', status: 'Maintenance' }
      ],
      upcomingDeadlines: [
        { customer: 'John Smith', item: 'Power Generator', duration: '3 days', dueDate: '2025-05-04' },
        { customer: 'Emily Brown', item: 'Pressure Washer', duration: '1 week', dueDate: '2025-05-05' },
        { customer: 'Robert Jones', item: 'Jackhammer', duration: '2 days', dueDate: '2025-05-03' },
        { customer: 'Lisa Davis', item: 'Paint Sprayer', duration: '4 days', dueDate: '2025-05-05' }
      ],
      rentalTrendsData: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
          {
            label: 'New Rentals',
            data: [18, 25, 30, 24],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            tension: 0.4,
            pointBackgroundColor: '#4299e1',
            pointBorderColor: '#fff',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true
          },
          {
            label: 'Returns',
            data: [12, 19, 27, 20],
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            tension: 0.4,
            pointBackgroundColor: '#48bb78',
            pointBorderColor: '#fff',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true
          }
        ]
      },
      inventoryStatusData: {
        labels: ['Available', 'Rented', 'Maintenance', 'Reserved'],
        datasets: [{
          data: [65, 24, 8, 13],
          backgroundColor: [
            'rgba(66, 153, 225, 0.8)',
            'rgba(72, 187, 120, 0.8)',
            'rgba(237, 137, 54, 0.8)',
            'rgba(159, 122, 234, 0.8)'
          ],
          hoverBackgroundColor: [
            'rgba(66, 153, 225, 1)',
            'rgba(72, 187, 120, 1)',
            'rgba(237, 137, 54, 1)',
            'rgba(159, 122, 234, 1)'
          ]
        }]
      },
      categoryPerformanceData: {
        labels: ['Power Tools', 'Landscaping', 'Construction', 'Cleaning', 'Moving'],
        datasets: [{
          label: 'Rental Frequency',
          data: [42, 35, 28, 22, 15],
          backgroundColor: 'rgba(66, 153, 225, 0.8)',
          borderColor: 'rgba(66, 153, 225, 1)',
          borderWidth: 1,
          borderRadius: 5
        }]
      }
    };
  },
  computed: {
    currentDate() {
      return this.formatDate(new Date().toISOString());
    }
  },
  mounted() {
    this.fetchPageData();
  },
  watch: {
    rentalTrendPeriod() {
      this.updateRentalTrendsChart();
    }
  },
  methods: {
    getStatusClass(status) {
      switch (status) {
        case 'Rented':
          return 'status-rented';
        case 'Returned':
          return 'status-returned';
        case 'Overdue':
          return 'status-overdue';
        case 'Maintenance':
          return 'status-maintenance';
        default:
          return 'status-default';
      }
    },
    getDaysRemaining(date) {
      const today = new Date();
      const dueDate = new Date(date);
      const diffTime = Math.abs(dueDate - today);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return `${diffDays} day${diffDays !== 1 ? 's' : ''}`;
    },
    getDaysRemainingClass(date) {
      const today = new Date();
      const dueDate = new Date(date);
      const diffTime = dueDate - today;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays <= 0) return 'urgent';
      if (diffDays <= 2) return 'warning';
      return 'normal';
    },
    updateRentalTrendsChart() {
      // In a real app, this would fetch data based on the selected period
      // For now, we'll just simulate different data
      if (this.rentalTrendPeriod === 'week') {
        this.rentalTrendsData = {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [
            {
              label: 'New Rentals',
              data: [5, 7, 4, 6, 8, 3, 5],
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#4299e1',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            },
            {
              label: 'Returns',
              data: [3, 4, 6, 4, 7, 2, 4],
              borderColor: '#48bb78',
              backgroundColor: 'rgba(72, 187, 120, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#48bb78',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            }
          ]
        };
      } else if (this.rentalTrendPeriod === 'month') {
        this.rentalTrendsData = {
          labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
          datasets: [
            {
              label: 'New Rentals',
              data: [18, 25, 30, 24],
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#4299e1',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            },
            {
              label: 'Returns',
              data: [12, 19, 27, 20],
              borderColor: '#48bb78',
              backgroundColor: 'rgba(72, 187, 120, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#48bb78',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            }
          ]
        };
      } else if (this.rentalTrendPeriod === 'quarter') {
        this.rentalTrendsData = {
          labels: ['January', 'February', 'March', 'April'],
          datasets: [
            {
              label: 'New Rentals',
              data: [65, 72, 95, 97],
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#4299e1',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            },
            {
              label: 'Returns',
              data: [58, 69, 86, 78],
              borderColor: '#48bb78',
              backgroundColor: 'rgba(72, 187, 120, 0.1)',
              tension: 0.4,
              pointBackgroundColor: '#48bb78',
              pointBorderColor: '#fff',
              pointRadius: 4,
              pointHoverRadius: 6,
              fill: true
            }
          ]
        };
      }
    },
    async fetchPageData() {
      // In a real application, this would call an API
      this.startLoading("Loading dashboard data...");
      
      try {
        await new Promise(resolve => setTimeout(resolve, 1000));
        // Here you would typically fetch data from an API
        
        // For demonstration, we'll just update the timestamp
        this.lastUpdated = new Date();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
  background-color: #f7fafc;
  min-height: 100vh;
}

.dashboard-content {
  flex: 1;
  margin-left: var(--sidebar-width, 260px);
  padding: 18px;
  transition: margin 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body.sidebar-collapsed .dashboard-content {
  margin-left: 70px;
}

/* Header Styling */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
}

.dashboard-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 3px;
}

.dashboard-subtitle {
  color: #718096;
  font-size: 0.85rem;
}

.dashboard-actions {
  display: flex;
  gap: 10px;
}

.refresh-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  color: #2d3748;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.refresh-btn:hover {
  border-color: #cbd5e0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
}

.fa-spin {
  animation: fa-spin 1s linear infinite;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Alert Styling */
.alert-error {
  display: flex;
  align-items: center;
  gap: 10px;
  background-color: #fff5f5;
  color: #c53030;
  border-left: 4px solid #e53e3e;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.stat-card {
  background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
  border-radius: 10px;
  padding: 16px;
  display: flex;
  align-items: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
  border: 1px solid #edf2f7;
  overflow: hidden;
  position: relative;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin-right: 12px;
  color: white;
  flex-shrink: 0;
}

.rental-card .stat-icon {
  background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.inventory-card .stat-icon {
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.pending-card .stat-icon {
  background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.revenue-card .stat-icon {
  background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%);
}

.stat-content {
  flex: 1;
}

.stat-title {
  font-size: 0.8rem;
  color: #718096;
  margin-bottom: 3px;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 3px;
}

.stat-trend {
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

.stat-trend.positive {
  color: #38a169;
}

.stat-trend.negative {
  color: #e53e3e;
}

/* Charts Grid */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.chart-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  border: 1px solid #edf2f7;
  display: flex;
  flex-direction: column;
}

.chart-card.large {
  grid-column: span 2;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #edf2f7;
}

.chart-header h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
}

.period-selector {
  background-color: #f7fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 4px 6px;
  font-size: 0.8rem;
  color: #4a5568;
  outline: none;
  cursor: pointer;
}

.chart-body {
  flex: 1;
  padding: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 250px;
}

/* Tables Grid */
.tables-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.table-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  border: 1px solid #edf2f7;
  display: flex;
  flex-direction: column;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #edf2f7;
}

.table-header h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
}

.view-all-btn {
  display: flex;
  align-items: center;
  gap: 5px;
  color: #3182ce;
  font-size: 0.85rem;
  font-weight: 500;
  text-decoration: none;
}

.view-all-btn:hover {
  text-decoration: underline;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4a5568;
  background-color: #f7fafc;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background-color: #edf2f7;
  color: #2d3748;
}

.table-body {
  padding: 0;
  overflow: hidden;
  flex: 1;
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  background-color: #f7fafc;
  color: #4a5568;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  text-align: left;
  padding: 10px 14px;
  border-bottom: 1px solid #edf2f7;
}

tbody td {
  padding: 10px 14px;
  color: #4a5568;
  font-size: 0.85rem;
  border-bottom: 1px solid #edf2f7;
}

tbody tr:last-child td {
  border-bottom: none;
}

.customer-cell {
  font-weight: 500;
  color: #2d3748;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-block;
}

.status-rented {
  background-color: #ebf8ff;
  color: #2b6cb0;
}

.status-returned {
  background-color: #f0fff4;
  color: #2f855a;
}

.status-overdue {
  background-color: #fff5f5;
  color: #c53030;
}

.status-maintenance {
  background-color: #fffaf0;
  color: #c05621;
}

.status-default {
  background-color: #f7fafc;
  color: #4a5568;
}

/* Deadline Items */
.deadline-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #edf2f7;
}

.deadline-item:last-child {
  border-bottom: none;
}

.deadline-content h4 {
  font-size: 0.95rem;
  font-weight: 500;
  color: #2d3748;
  margin: 0 0 4px 0;
}

.deadline-content p {
  font-size: 0.85rem;
  color: #718096;
  margin: 0;
}

.deadline-date {
  text-align: right;
}

.days-remaining {
  font-weight: 600;
  font-size: 0.85rem;
  border-radius: 20px;
  padding: 4px 10px;
  display: inline-block;
  margin-bottom: 4px;
}

.days-remaining.normal {
  background-color: #ebf8ff;
  color: #2b6cb0;
}

.days-remaining.warning {
  background-color: #fffaf0;
  color: #c05621;
}

.days-remaining.urgent {
  background-color: #fff5f5;
  color: #c53030;
}

.due-date {
  font-size: 0.75rem;
  color: #a0aec0;
}

/* Responsive adjustments */
@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .chart-card.large {
    grid-column: span 1;
  }
  
  .tables-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>