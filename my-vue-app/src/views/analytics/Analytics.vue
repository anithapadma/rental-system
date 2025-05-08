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
        <div v-if="isLoading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading analytics data...</p>
        </div>
        <div v-else-if="error" class="error-container">
          <p>{{ error }}</p>
          <button @click="loadOverviewData" class="retry-button">Retry</button>
        </div>
        <div v-else>
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
                <select class="period-selector" v-model="revenuePeriod" @change="loadRevenueChartData">
                  <option value="3">Last 3 months</option>
                  <option value="6">Last 6 months</option>
                  <option value="12">Last 12 months</option>
                </select>
              </div>
              <div class="chart-body">
                <div v-if="loadingCharts.revenue" class="chart-loading">
                  <div class="loading-spinner small"></div>
                </div>
                <LineChart v-else :chartData="revenueChartData" />
              </div>
            </div>

            <div class="chart-card">
              <div class="chart-header">
                <h3>Rental Type Distribution</h3>
              </div>
              <div class="chart-body">
                <div v-if="loadingCharts.rentalType" class="chart-loading">
                  <div class="loading-spinner small"></div>
                </div>
                <DoughnutChart v-else :chartData="rentalTypeChartData" />
              </div>
            </div>
            
            <div class="chart-card">
              <div class="chart-header">
                <h3>Top Rental Items</h3>
              </div>
              <div class="chart-body">
                <div v-if="loadingCharts.topItems" class="chart-loading">
                  <div class="loading-spinner small"></div>
                </div>
                <BarChart v-else :chartData="topItemsChartData" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Analysis -->
      <div v-if="activeTab === 'revenue'" class="tab-content">
        <div v-if="isLoadingRevenue" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading revenue analysis...</p>
        </div>
        <div v-else-if="revenueError" class="error-container">
          <p>{{ revenueError }}</p>
          <button @click="loadRevenueData" class="retry-button">Retry</button>
        </div>
        <div v-else class="charts-grid">
          <div class="chart-card large">
            <div class="chart-header">
              <h3>Revenue Breakdown</h3>
              <select class="period-selector" v-model="revenueBreakdownPeriod" @change="loadRevenueBreakdownData">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
              </select>
            </div>
            <div class="chart-body">
              <div v-if="loadingCharts.revenueBreakdown" class="chart-loading">
                <div class="loading-spinner small"></div>
              </div>
              <LineChart v-else :chartData="revenueBreakdownChartData" />
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3>Revenue by Category</h3>
            </div>
            <div class="chart-body">
              <div v-if="loadingCharts.revenueByCategory" class="chart-loading">
                <div class="loading-spinner small"></div>
              </div>
              <DoughnutChart v-else :chartData="revenueByCategoryChartData" />
            </div>
          </div>
          
          <div class="chart-card">
            <div class="chart-header">
              <h3>Revenue by Location</h3>
            </div>
            <div class="chart-body">
              <div v-if="loadingCharts.revenueByLocation" class="chart-loading">
                <div class="loading-spinner small"></div>
              </div>
              <BarChart v-else :chartData="revenueByLocationChartData" />
            </div>
          </div>

          <div class="chart-card large">
            <div class="chart-header">
              <h3>Average Rental Value</h3>
            </div>
            <div class="chart-body">
              <div v-if="loadingCharts.avgRentalValue" class="chart-loading">
                <div class="loading-spinner small"></div>
              </div>
              <LineChart v-else :chartData="avgRentalValueChartData" />
            </div>
          </div>
        </div>
      </div>

      <!-- Inventory Analysis -->
      <div v-if="activeTab === 'inventory'" class="tab-content">
        <div v-if="isLoadingInventory" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading inventory analysis...</p>
        </div>
        <div v-else-if="inventoryError" class="error-container">
          <p>{{ inventoryError }}</p>
          <button @click="loadInventoryData" class="retry-button">Retry</button>
        </div>
        <div v-else class="charts-grid">
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
        <div v-if="isLoadingCustomer" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading customer analysis...</p>
        </div>
        <div v-else-if="customerError" class="error-container">
          <p>{{ customerError }}</p>
          <button @click="loadCustomerData" class="retry-button">Retry</button>
        </div>
        <div v-else class="charts-grid">
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
import analyticsService from '@/services/analyticsService';

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
      
      // Loading state
      isLoading: false,
      isLoadingRevenue: false,
      isLoadingInventory: false,
      isLoadingCustomer: false,
      loadingCharts: {
        revenue: false,
        rentalType: false,
        topItems: false,
        revenueBreakdown: false,
        revenueByCategory: false,
        revenueByLocation: false,
        avgRentalValue: false,
        inventoryStatus: false,
        lowStockItems: false,
        utilizationRate: false,
        topPerformingItems: false,
        customerAcquisition: false,
        customerSegment: false,
        topCustomers: false,
        customerRetention: false
      },
      
      // Error state
      error: null,
      revenueError: null,
      inventoryError: null,
      customerError: null,
      
      // Overview data
      revenueData: {
        total: 0,
        trend: 0
      },
      rentalData: {
        active: 0,
        trend: 0
      },
      inventoryData: {
        utilization: 0,
        trend: 0
      },
      agreementsData: {
        completed: 0,
        trend: 0
      },
      
      // Chart data
      revenueChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      rentalTypeChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      topItemsChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      
      // Revenue analysis data
      revenueBreakdownChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      revenueByCategoryChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      revenueByLocationChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      avgRentalValueChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      
      // Inventory analysis data
      inventoryStatusChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      lowStockItemsChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      utilizationRateChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      
      // Top performing inventory items
      topPerformingItems: [],
      
      // Customer analysis data
      customerAcquisitionChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      customerSegmentChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      customerRetentionChartData: {
        labels: [],
        datasets: [{ data: [] }]
      },
      
      // Top customers
      topCustomers: []
    };
  },
  created() {
    // Load initial data when component is created
    this.loadOverviewData();
  },
  watch: {
    // Watch for tab changes to load appropriate data
    activeTab(newTab) {
      if (newTab === 'overview') {
        this.loadOverviewData();
      } else if (newTab === 'revenue') {
        this.loadRevenueData();
      } else if (newTab === 'inventory') {
        this.loadInventoryData();
      } else if (newTab === 'customers') {
        this.loadCustomerData();
      }
    }
  },
  methods: {
    formatNumber(value) {
      return new Intl.NumberFormat('en-US').format(value);
    },
    
    // Overview data loading
    async loadOverviewData() {
      this.isLoading = true;
      this.error = null;
      
      try {
        const response = await analyticsService.getOverviewData();
        const data = response.data;
        
        // Update overview metrics
        this.revenueData = data.revenue;
        this.rentalData = data.rental;
        this.inventoryData = data.inventory;
        this.agreementsData = data.agreements;
        
        // Load chart data for overview tab
        this.loadRevenueChartData();
        this.loadRentalTypeData();
        this.loadTopItemsData();
      } catch (err) {
        console.error('Error loading overview data:', err);
        this.error = 'Failed to load analytics data. Please try again.';
      } finally {
        this.isLoading = false;
      }
    },
    
    // Chart data loading for overview tab
    async loadRevenueChartData() {
      this.loadingCharts.revenue = true;
      
      try {
        const response = await analyticsService.getRevenueChartData(parseInt(this.revenuePeriod));
        const data = response.data;
        
        this.revenueChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Revenue',
              data: data.values,
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading revenue chart data:', err);
      } finally {
        this.loadingCharts.revenue = false;
      }
    },
    
    async loadRentalTypeData() {
      this.loadingCharts.rentalType = true;
      
      try {
        const response = await analyticsService.getRentalTypeDistributionData();
        const data = response.data;
        
        this.rentalTypeChartData = {
          labels: data.labels,
          datasets: [
            {
              data: data.values,
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
      } catch (err) {
        console.error('Error loading rental type data:', err);
      } finally {
        this.loadingCharts.rentalType = false;
      }
    },
    
    async loadTopItemsData() {
      this.loadingCharts.topItems = true;
      
      try {
        const response = await analyticsService.getTopRentalItemsData();
        const data = response.data;
        
        this.topItemsChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Rental Count',
              data: data.values,
              backgroundColor: 'rgba(72, 187, 120, 0.7)',
              borderColor: 'rgba(72, 187, 120, 1)',
              borderWidth: 1
            }
          ]
        };
      } catch (err) {
        console.error('Error loading top items data:', err);
      } finally {
        this.loadingCharts.topItems = false;
      }
    },
    
    // Revenue analysis data loading
    async loadRevenueData() {
      this.isLoadingRevenue = true;
      this.revenueError = null;
      
      try {
        // Load all revenue analysis charts
        await Promise.all([
          this.loadRevenueBreakdownData(),
          this.loadRevenueByCategoryData(),
          this.loadRevenueByLocationData(),
          this.loadAverageRentalValueData()
        ]);
      } catch (err) {
        console.error('Error loading revenue analysis data:', err);
        this.revenueError = 'Failed to load revenue analysis data. Please try again.';
      } finally {
        this.isLoadingRevenue = false;
      }
    },
    
    // Revenue analysis chart data loading
    async loadRevenueBreakdownData() {
      this.loadingCharts.revenueBreakdown = true;
      
      try {
        const response = await analyticsService.getRevenueAnalysisData(parseInt(this.revenueBreakdownPeriod));
        const data = response.data;
        
        this.revenueBreakdownChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Short-term Rentals',
              data: data.shortTerm,
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              borderWidth: 2,
              fill: true
            },
            {
              label: 'Long-term Rentals',
              data: data.longTerm,
              borderColor: '#48bb78',
              backgroundColor: 'rgba(72, 187, 120, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading revenue breakdown data:', err);
      } finally {
        this.loadingCharts.revenueBreakdown = false;
      }
    },
    
    async loadRevenueByCategoryData() {
      this.loadingCharts.revenueByCategory = true;
      
      try {
        const response = await analyticsService.getRevenueByCategoryData();
        const data = response.data;
        
        this.revenueByCategoryChartData = {
          labels: data.labels,
          datasets: [
            {
              data: data.values,
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
      } catch (err) {
        console.error('Error loading revenue by category data:', err);
      } finally {
        this.loadingCharts.revenueByCategory = false;
      }
    },
    
    async loadRevenueByLocationData() {
      this.loadingCharts.revenueByLocation = true;
      
      try {
        const response = await analyticsService.getRevenueByLocationData();
        const data = response.data;
        
        this.revenueByLocationChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Revenue',
              data: data.values,
              backgroundColor: 'rgba(159, 122, 234, 0.7)',
              borderColor: 'rgba(159, 122, 234, 1)',
              borderWidth: 1
            }
          ]
        };
      } catch (err) {
        console.error('Error loading revenue by location data:', err);
      } finally {
        this.loadingCharts.revenueByLocation = false;
      }
    },
    
    async loadAverageRentalValueData() {
      this.loadingCharts.avgRentalValue = true;
      
      try {
        const response = await analyticsService.getAverageRentalValueData();
        const data = response.data;
        
        this.avgRentalValueChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Average Rental Value',
              data: data.values,
              borderColor: '#ed8936',
              backgroundColor: 'rgba(237, 137, 54, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading average rental value data:', err);
      } finally {
        this.loadingCharts.avgRentalValue = false;
      }
    },
    
    // Inventory analysis data loading
    async loadInventoryData() {
      this.isLoadingInventory = true;
      this.inventoryError = null;
      
      try {
        // Load all inventory analysis charts
        await Promise.all([
          this.loadInventoryStatusData(),
          this.loadLowStockItemsData(),
          this.loadUtilizationRateData(),
          this.loadTopPerformingItemsData()
        ]);
      } catch (err) {
        console.error('Error loading inventory analysis data:', err);
        this.inventoryError = 'Failed to load inventory analysis data. Please try again.';
      } finally {
        this.isLoadingInventory = false;
      }
    },
    
    // Inventory analysis chart data loading
    async loadInventoryStatusData() {
      this.loadingCharts.inventoryStatus = true;
      
      try {
        const response = await analyticsService.getInventoryStatusData();
        const data = response.data;
        
        this.inventoryStatusChartData = {
          labels: data.labels,
          datasets: [
            {
              data: data.values,
              backgroundColor: [
                'rgba(72, 187, 120, 0.8)',
                'rgba(66, 153, 225, 0.8)',
                'rgba(237, 137, 54, 0.8)',
                'rgba(159, 122, 234, 0.8)'
              ]
            }
          ]
        };
      } catch (err) {
        console.error('Error loading inventory status data:', err);
      } finally {
        this.loadingCharts.inventoryStatus = false;
      }
    },
    
    async loadLowStockItemsData() {
      this.loadingCharts.lowStockItems = true;
      
      try {
        const response = await analyticsService.getLowStockItemsData();
        const data = response.data;
        
        this.lowStockItemsChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Available Stock',
              data: data.values,
              backgroundColor: 'rgba(237, 137, 54, 0.7)',
              borderColor: 'rgba(237, 137, 54, 1)',
              borderWidth: 1
            }
          ]
        };
      } catch (err) {
        console.error('Error loading low stock items data:', err);
      } finally {
        this.loadingCharts.lowStockItems = false;
      }
    },
    
    async loadUtilizationRateData() {
      this.loadingCharts.utilizationRate = true;
      
      try {
        const response = await analyticsService.getUtilizationRateData();
        const data = response.data;
        
        this.utilizationRateChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Utilization Rate %',
              data: data.values,
              borderColor: '#9f7aea',
              backgroundColor: 'rgba(159, 122, 234, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading utilization rate data:', err);
      } finally {
        this.loadingCharts.utilizationRate = false;
      }
    },
    
    async loadTopPerformingItemsData() {
      this.loadingCharts.topPerformingItems = true;
      
      try {
        const response = await analyticsService.getTopPerformingItemsData();
        this.topPerformingItems = response.data.items;
      } catch (err) {
        console.error('Error loading top performing items data:', err);
      } finally {
        this.loadingCharts.topPerformingItems = false;
      }
    },
    
    // Customer analysis data loading
    async loadCustomerData() {
      this.isLoadingCustomer = true;
      this.customerError = null;
      
      try {
        // Load all customer analysis charts
        await Promise.all([
          this.loadCustomerAcquisitionData(),
          this.loadCustomerSegmentData(),
          this.loadTopCustomersData(),
          this.loadCustomerRetentionData()
        ]);
      } catch (err) {
        console.error('Error loading customer analysis data:', err);
        this.customerError = 'Failed to load customer analysis data. Please try again.';
      } finally {
        this.isLoadingCustomer = false;
      }
    },
    
    // Customer analysis chart data loading
    async loadCustomerAcquisitionData() {
      this.loadingCharts.customerAcquisition = true;
      
      try {
        const response = await analyticsService.getCustomerAcquisitionData();
        const data = response.data;
        
        this.customerAcquisitionChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'New Customers',
              data: data.values,
              borderColor: '#4299e1',
              backgroundColor: 'rgba(66, 153, 225, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading customer acquisition data:', err);
      } finally {
        this.loadingCharts.customerAcquisition = false;
      }
    },
    
    async loadCustomerSegmentData() {
      this.loadingCharts.customerSegment = true;
      
      try {
        const response = await analyticsService.getCustomerSegmentData();
        const data = response.data;
        
        this.customerSegmentChartData = {
          labels: data.labels,
          datasets: [
            {
              data: data.values,
              backgroundColor: [
                'rgba(66, 153, 225, 0.8)',
                'rgba(72, 187, 120, 0.8)',
                'rgba(237, 137, 54, 0.8)',
                'rgba(159, 122, 234, 0.8)'
              ]
            }
          ]
        };
      } catch (err) {
        console.error('Error loading customer segment data:', err);
      } finally {
        this.loadingCharts.customerSegment = false;
      }
    },
    
    async loadTopCustomersData() {
      this.loadingCharts.topCustomers = true;
      
      try {
        const response = await analyticsService.getTopCustomersData();
        this.topCustomers = response.data.customers;
      } catch (err) {
        console.error('Error loading top customers data:', err);
      } finally {
        this.loadingCharts.topCustomers = false;
      }
    },
    
    async loadCustomerRetentionData() {
      this.loadingCharts.customerRetention = true;
      
      try {
        const response = await analyticsService.getCustomerRetentionData();
        const data = response.data;
        
        this.customerRetentionChartData = {
          labels: data.labels,
          datasets: [
            {
              label: 'Retention Rate %',
              data: data.values,
              borderColor: '#48bb78',
              backgroundColor: 'rgba(72, 187, 120, 0.1)',
              borderWidth: 2,
              fill: true
            }
          ]
        };
      } catch (err) {
        console.error('Error loading customer retention data:', err);
      } finally {
        this.loadingCharts.customerRetention = false;
      }
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

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px;
  color: #e53e3e;
}

.retry-button {
  margin-top: 12px;
  padding: 6px 12px;
  background-color: #3182ce;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(66, 153, 225, 0.3);
  border-top-color: #3182ce;
  border-radius: 50%;
  animation: spin 1s ease-in-out infinite;
}

.loading-spinner.small {
  width: 24px;
  height: 24px;
  border-width: 2px;
}

.chart-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 200px;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>