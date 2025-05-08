import api from './api';

const adminDashboardService = {
  // Get all dashboard data in a single request
  getAllDashboardData() {
    return api.get('/admin/dashboard/all');
  },
  
  // Get dashboard statistics summary
  getStatsSummary() {
    return api.get('/admin/dashboard/stats');
  },
  
  // Get recent activities for admin dashboard
  getRecentActivities() {
    return api.get('/admin/dashboard/recent-activities');
  },
  
  // Get rental trends data for charts
  getRentalTrends(period = 'month') {
    return api.get(`/admin/dashboard/rental-trends?period=${period}`);
  },
  
  // Get item status distribution data
  getItemStatusDistribution() {
    return api.get('/admin/dashboard/item-status');
  },
  
  // Get revenue data for charts
  getRevenueData() {
    return api.get('/admin/dashboard/revenue');
  },
  
  // Get upcoming deadlines/returns
  getUpcomingDeadlines() {
    return api.get('/admin/dashboard/deadlines');
  }
};

export default adminDashboardService;