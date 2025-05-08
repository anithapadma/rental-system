import api from './api';

const employeeTasksService = {
  // Get all tasks with optional filtering/pagination params
  getTasks(params = {}) {
    return api.get('/employee/tasks', { params });
  },

  // Get a specific task by ID
  getTask(id) {
    return api.get(`/employee/tasks/${id}`);
  },

  // Update task status
  updateTaskStatus(id, status) {
    return api.patch(`/employee/tasks/${id}/status`, { status });
  },

  // Complete a task
  completeTask(id) {
    return api.post(`/employee/tasks/${id}/complete`);
  }
};

export default employeeTasksService;