<template>
  <div class="analytics-page">
    <div class="dashboard-header">
      <h1  style="margin-top: -58px;
    " class="dashboard-title text-3xl font-bold">Analytics Dashboard</h1>
      <p class="dashboard-subtitle">View and analyze your business performance metrics</p>
    </div>

    <div class="analytics-tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id" 
        @click="activeTab = tab.id" 
        :class="{ active: activeTab === tab.id }" 
        class="tab-button">
        {{ tab.name }}
      </button>
    </div>
    
    <div class="analytics-content">
      <!-- Performance Overview -->
      <div v-if="activeTab === 'overview'" class="tab-content">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #4299e1;">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Total Revenue</div>
              <div class="stat-value">${{ formatNumber(revenueData.total) }}</div>
              <div class="stat-trend" :class="{ 'up': revenueData.trend > 0, 'down': revenueData.trend < 0 }">
                <i :class="revenueData.trend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                {{ Math.abs(revenueData.trend) }}% from last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #48bb78;">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Active Rentals</div>
              <div class="stat-value">{{ formatNumber(rentalData.active) }}</div>
              <div class="stat-trend" :class="{ 'up': rentalData.trend > 0, 'down': rentalData.trend < 0 }">
                <i :class="rentalData.trend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                {{ Math.abs(rentalData.trend) }}% from last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #ed8936;">
              <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Inventory Utilization</div>
              <div class="stat-value">{{ inventoryData.utilization }}%</div>
              <div class="stat-trend" :class="{ 'up': inventoryData.trend > 0, 'down': inventoryData.trend < 0 }">
                <i :class="inventoryData.trend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                {{ Math.abs(inventoryData.trend) }}% from last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #9f7aea;">
              <i class="fas fa-file-contract"></i>
            </div>
            <div class="stat-content">
              <div class="stat-title">Completed Agreements</div>
              <div class="stat-value">{{ formatNumber(agreementsData.completed) }}</div>
              <div class="stat-trend" :class="{ 'up': agreementsData.trend > 0, 'down': agreementsData.trend < 0 }">
                <i :class="agreementsData.trend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                {{ Math.abs(agreementsData.trend) }}% from last month
              </div>
            </div>
          </div>
        </div>

        <div class="charts-grid">
          <div class="chart-card large">
            <div class="chart-header">
              <h3>Monthly Revenue</h3>
              <select class="period-selector" v-model="revenuePeriod">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
              </select>
            </div>
            <div class="chart-body">
              <LineChart :chartData="revenueChartData" />
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3>Rental Type Distribution</h3>
            </div>
            <div class="chart-body">
              <DoughnutChart :chartData="rentalTypeChartData" />
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3>Top Rental Items</h3>
            </div>
            <div class="chart-body">
              <BarChart :chartData="topItemsChartData" />
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Analysis -->
      <div v-if="activeTab === 'revenue'" class="tab-content">
        <div class="charts-grid">
          <div class="chart-card large">
            <div class="chart-header">
              <h3>Revenue Breakdown</h3>
              <select class="period-selector" v-model="revenueBreakdownPeriod">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
              </select>
            </div>
            <div class="chart-body">
              <LineChart :chartData="revenueBreakdownChartData" />
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3>Revenue by Category</h3>
            </div>
            <div class="chart-body">
              <DoughnutChart :chartData="revenueByCategoryChartData" />
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3>Revenue by Location</h3>
            </div>
            <div class="chart-body">
              <BarChart :chartData="revenueByLocationChartData" />
            </div>
          </div>

          <div class="chart-card large">
            <div class="chart-header">
              <h3>Average Rental Value</h3>
            </div>
            <div class="chart-body">
              <LineChart :chartData="avgRentalValueChartData" />
            </div>
          </div>
        </div>
      </div>

      <!-- Inventory Analysis -->
      <div v-if="activeTab === 'inventory'" class="tab-content">
        <div class="charts-grid">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Inventory Status</h3>
            </div>
            <div class="chart-body">
              <DoughnutChart :chartData="inventoryStatusChartData" />
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3>Low Stock Items</h3>
            </div>
            <div class="chart-body">
              <BarChart :chartData="lowStockItemsChartData" />
            </div>
          </div>

          <div class="chart-card large">
            <div class="chart-header">
              <h3>Monthly Utilization Rate</h3>
            </div>
            <div class="chart-body">
              <LineChart :chartData="utilizationRateChartData" />
            </div>
          </div>
          
          <div class="chart-card large">
            <div class="table-header">
              <h3>Inventory Performance</h3>
            </div>
            <div class="table-body">
              <table class="analytics-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Total Rentals</th>
                    <th>Utilization %</th>
                    <th>Revenue Generated</th>
                    <th>ROI</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in topPerformingItems" :key="index">
                    <td>{{ item.name }}</td>
                    <td>{{ item.type }}</td>
                    <td>{{ item.rentals }}</td>
                    <td>{{ item.utilization }}%</td>
                    <td>${{ formatNumber(item.revenue) }}</td>
                    <td>{{ item.roi }}%</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Customer Analysis -->
      <div v-if="activeTab === 'customers'" class="tab-content">
        <div class="charts-grid">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Customer Acquisition</h3>
            </div>
            <div class="chart-body">
              <LineChart :chartData="customerAcquisitionChartData" />
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3>Customer Segment</h3>
            </div>
            <div class="chart-body">
              <DoughnutChart :chartData="customerSegmentChartData" />
            </div>
          </div>

          <div class="chart-card large">
            <div class="table-header">
              <h3>Top Customers</h3>
            </div>
            <div class="table-body">
              <table class="analytics-table">
                <thead>
                  <tr>
                    <th>Customer</th>
                    <th>Total Rentals</th>
                    <th>Lifetime Value</th>
                    <th>First Rental</th>
                    <th>Last Rental</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(customer, index) in topCustomers" :key="index">
                    <td>{{ customer.name }}</td>
                    <td>{{ customer.totalRentals }}</td>
                    <td>${{ formatNumber(customer.lifetimeValue) }}</td>
                    <td>{{ customer.firstRental }}</td>
                    <td>{{ customer.lastRental }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="chart-card large">
            <div class="chart-header">
              <h3>Customer Retention Rate</h3>
            </div>
            <div class="chart-body">
              <LineChart :chartData="customerRetentionChartData" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineChart from '@/components/charts/LineChart.vue';
import BarChart from '@/components/charts/BarChart.vue';
import DoughnutChart from '@/components/charts/DoughnutChart.vue';

export default {
  name: 'Analytics',
  components: {
    LineChart,
    BarChart,
    DoughnutChart
  },
  data() {
    return {
      tabs: [
        { id: 'overview', name: 'Overview' },
        { id: 'revenue', name: 'Revenue Analysis' },
        { id: 'inventory', name: 'Inventory Analysis' },
        { id: 'customers', name: 'Customer Analysis' }
      ],
      activeTab: 'overview',
      revenuePeriod: '6',
      revenueBreakdownPeriod: '6',
      
      // Overview data
      revenueData: {
        total: 156482.50,
        trend: 8.2
      },
      rentalData: {
        active: 324,
        trend: 12.5
      },
      inventoryData: {
        utilization: 78,
        trend: -2.3
      },
      agreementsData: {
        completed: 1842,
        trend: 5.7
      },
      
      // Top performing inventory items
      topPerformingItems: [
        { name: 'Heavy Excavator XL200', type: 'Construction', rentals: 58, utilization: 92, revenue: 25420, roi: 185 },
        { name: 'Portable Generator 5kW', type: 'Power', rentals: 124, utilization: 87, revenue: 18750, roi: 210 },
        { name: 'Professional Camera Kit', type: 'Photography', rentals: 76, utilization: 84, revenue: 15640, roi: 175 },
        { name: 'Industrial Pressure Washer', type: 'Cleaning', rentals: 95, utilization: 81, revenue: 12840, roi: 160 },
        { name: 'Concrete Mixer 500L', type: 'Construction', rentals: 42, utilization: 79, revenue: 10950, roi: 145 }
      ],
      
      // Top customers data
      topCustomers: [
        { name: 'Acme Construction Ltd.', totalRentals: 48, lifetimeValue: 42500, firstRental: '2024-06-15', lastRental: '2025-04-28' },
        { name: 'GreenScape Landscaping', totalRentals: 36, lifetimeValue: 28750, firstRental: '2024-08-03', lastRental: '2025-04-22' },
        { name: 'Elite Media Productions', totalRentals: 29, lifetimeValue: 22400, firstRental: '2024-09-12', lastRental: '2025-04-15' },
        { name: 'Horizon Events Co.', totalRentals: 27, lifetimeValue: 19600, firstRental: '2024-07-25', lastRental: '2025-04-06' },
        { name: 'Metro Property Services', totalRentals: 23, lifetimeValue: 17850, firstRental: '2024-10-08', lastRental: '2025-03-30' }
      ]
    };
  },
  computed: {
    // Overview charts
    revenueChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'Revenue',
            data: [21500, 23200, 20800, 26400, 24500, 29600],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    rentalTypeChartData() {
      return {
        labels: ['Construction', 'Photography', 'Power', 'Events', 'Other'],
        datasets: [
          {
            data: [42, 18, 15, 20, 5],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)',
              'rgba(160, 174, 192, 0.8)'
            ]
          }
        ]
      };
    },
    topItemsChartData() {
      return {
        labels: ['Excavator', 'Generator', 'Camera', 'Washer', 'Mixer'],
        datasets: [
          {
            label: 'Rental Count',
            data: [58, 124, 76, 95, 42],
            backgroundColor: 'rgba(72, 187, 120, 0.7)',
            borderColor: 'rgba(72, 187, 120, 1)',
            borderWidth: 1
          }
        ]
      };
    },
    
    // Revenue charts
    revenueBreakdownChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'Short-term Rentals',
            data: [12500, 13200, 11800, 14400, 13500, 15600],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          },
          {
            label: 'Long-term Rentals',
            data: [9000, 10000, 9000, 12000, 11000, 14000],
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    revenueByCategoryChartData() {
      return {
        labels: ['Construction', 'Photography', 'Power', 'Events', 'Other'],
        datasets: [
          {
            data: [68500, 32400, 27800, 21500, 6200],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)',
              'rgba(160, 174, 192, 0.8)'
            ]
          }
        ]
      };
    },
    revenueByLocationChartData() {
      return {
        labels: ['North', 'South', 'East', 'West', 'Central'],
        datasets: [
          {
            label: 'Revenue',
            data: [45200, 38600, 27400, 31500, 13800],
            backgroundColor: 'rgba(159, 122, 234, 0.7)',
            borderColor: 'rgba(159, 122, 234, 1)',
            borderWidth: 1
          }
        ]
      };
    },
    avgRentalValueChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'Average Rental Value',
            data: [425, 438, 452, 468, 482, 495],
            borderColor: '#ed8936',
            backgroundColor: 'rgba(237, 137, 54, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    
    // Inventory charts
    inventoryStatusChartData() {
      return {
        labels: ['Available', 'Rented', 'Maintenance', 'Reserved'],
        datasets: [
          {
            data: [35, 42, 15, 8],
            backgroundColor: [
              'rgba(72, 187, 120, 0.8)',
              'rgba(66, 153, 225, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ]
          }
        ]
      };
    },
    lowStockItemsChartData() {
      return {
        labels: ['Generators', 'Cameras', 'Speakers', 'Lights', 'Projectors'],
        datasets: [
          {
            label: 'Available Stock',
            data: [3, 2, 4, 5, 3],
            backgroundColor: 'rgba(237, 137, 54, 0.7)',
            borderColor: 'rgba(237, 137, 54, 1)',
            borderWidth: 1
          }
        ]
      };
    },
    utilizationRateChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'Utilization Rate %',
            data: [72, 68, 74, 76, 80, 78],
            borderColor: '#9f7aea',
            backgroundColor: 'rgba(159, 122, 234, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    
    // Customer charts
    customerAcquisitionChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'New Customers',
            data: [24, 18, 32, 28, 34, 30],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    customerSegmentChartData() {
      return {
        labels: ['Business', 'Individual', 'Government', 'Non-profit'],
        datasets: [
          {
            data: [45, 32, 18, 5],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ]
          }
        ]
      };
    },
    customerRetentionChartData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'Retention Rate %',
            data: [68, 72, 70, 74, 76, 78],
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    }
  },
  methods: {
    formatNumber(value) {
      return new Intl.NumberFormat('en-US').format(value);
    }
  }
};
</script>

<style scoped>
.analytics-page {
  padding: 18px;
}
.analytics-tabs {
  display: flex;
  margin-bottom: 20px;
  border-bottom: 1px solid #e2e8f0;
  overflow-x: auto;
}

.tab-button {
  padding: 8px 16px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  font-weight: 500;
  color: #4a5568;
  cursor: pointer;
  white-space: nowrap;
}

.tab-button:hover {
  color: #2d3748;
  background-color: #f7fafc;
}

.tab-button.active {
  color: #3182ce;
  border-bottom: 2px solid #3182ce;
}

.tab-content {
  animation: fadeIn 0.3s ease-in-out;
}

.analytics-table {
  width: 100%;
  border-collapse: collapse;
}

.analytics-table th,
.analytics-table td {
  padding: 10px 14px;
}

.table-body {
  overflow-x: auto;
  padding: 0 10px 10px 10px;
}

.stat-trend.up {
  color: #48bb78;
}

.stat-trend.down {
  color: #f56565;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>