import api from './api';

const employeeDashboardService = {
  // Get dashboard data for the employee
  getDashboardData() {
    return api.get('/employee/dashboard');
  },
  
  // Get employee's tasks for dashboard
  getDashboardTasks() {
    return api.get('/employee/dashboard/tasks');
  },
  
  // Get employee's schedule for dashboard
  getDashboardSchedule() {
    return api.get('/employee/dashboard/schedule');
  },
  
  // Get recent rentals for dashboard
  getDashboardRentals() {
    return api.get('/employee/dashboard/rentals');
  },
  
  // Get performance chart data
  getPerformanceData() {
    return api.get('/employee/dashboard/performance');
  },
  
  // Get task distribution data
  getTaskDistributionData() {
    return api.get('/employee/dashboard/task-distribution');
  },
  
  // Get rental statistics data
  getRentalStatsData() {
    return api.get('/employee/dashboard/rental-stats');
  },
  
  // Get team members (employees)
  getTeamMembers() {
    return api.get('/employee/dashboard/team');
  },
  
  // Get all dashboard data in a single request
  getAllDashboardData() {
    return api.get('/employee/dashboard/all');
  }
};

export default employeeDashboardService;