import api from './api';

const analyticsService = {
  /**
   * Get overview analytics data
   * @returns {Promise} Promise with overview analytics data
   */
  getOverviewData() {
    return api.get('/analytics/overview');
  },
  
  /**
   * Get revenue analytics data
   * @param {number} period - Period in months (3, 6, or 12)
   * @returns {Promise} Promise with revenue analytics data
   */
  getRevenueAnalysisData(period = 6) {
    return api.get(`/analytics/revenue?period=${period}`);
  },
  
  /**
   * Get monthly revenue chart data
   * @param {number} period - Period in months (3, 6, or 12)
   * @returns {Promise} Promise with revenue chart data
   */
  getRevenueChartData(period = 6) {
    return api.get(`/analytics/revenue/chart?period=${period}`);
  },
  
  /**
   * Get revenue breakdown by category
   * @returns {Promise} Promise with revenue by category data
   */
  getRevenueByCategoryData() {
    return api.get('/analytics/revenue/categories');
  },
  
  /**
   * Get revenue breakdown by location
   * @returns {Promise} Promise with revenue by location data
   */
  getRevenueByLocationData() {
    return api.get('/analytics/revenue/locations');
  },
  
  /**
   * Get average rental value over time
   * @returns {Promise} Promise with average rental value data
   */
  getAverageRentalValueData() {
    return api.get('/analytics/revenue/average-value');
  },
  
  /**
   * Get rental type distribution
   * @returns {Promise} Promise with rental type distribution data
   */
  getRentalTypeDistributionData() {
    return api.get('/analytics/rentals/types');
  },
  
  /**
   * Get top rental items data
   * @returns {Promise} Promise with top rental items data
   */
  getTopRentalItemsData() {
    return api.get('/analytics/rentals/top-items');
  },
  
  /**
   * Get inventory status data
   * @returns {Promise} Promise with inventory status data
   */
  getInventoryStatusData() {
    return api.get('/analytics/inventory/status');
  },
  
  /**
   * Get low stock items data
   * @returns {Promise} Promise with low stock items data
   */
  getLowStockItemsData() {
    return api.get('/analytics/inventory/low-stock');
  },
  
  /**
   * Get monthly utilization rate data
   * @returns {Promise} Promise with utilization rate data
   */
  getUtilizationRateData() {
    return api.get('/analytics/inventory/utilization');
  },
  
  /**
   * Get top performing inventory items
   * @returns {Promise} Promise with top performing items data
   */
  getTopPerformingItemsData() {
    return api.get('/analytics/inventory/top-performing');
  },
  
  /**
   * Get customer acquisition data
   * @returns {Promise} Promise with customer acquisition data
   */
  getCustomerAcquisitionData() {
    return api.get('/analytics/customers/acquisition');
  },
  
  /**
   * Get customer segment data
   * @returns {Promise} Promise with customer segment data
   */
  getCustomerSegmentData() {
    return api.get('/analytics/customers/segments');
  },
  
  /**
   * Get top customers data
   * @returns {Promise} Promise with top customers data
   */
  getTopCustomersData() {
    return api.get('/analytics/customers/top');
  },
  
  /**
   * Get customer retention rate data
   * @returns {Promise} Promise with customer retention data
   */
  getCustomerRetentionData() {
    return api.get('/analytics/customers/retention');
  }
};

export default analyticsService;