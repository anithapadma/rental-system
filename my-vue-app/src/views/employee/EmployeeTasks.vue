<template>
  <employee-layout>
    <div class="employee-tasks">
      <div class="page-header">
        <h1>My Tasks</h1>
        <p>Manage your assigned tasks and track progress</p>
      </div>

      <div class="tasks-filters">
        <div class="filter-group">
          <label for="status-filter">Status:</label>
          <select id="status-filter" v-model="statusFilter" @change="fetchTasks">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="priority-filter">Priority:</label>
          <select id="priority-filter" v-model="priorityFilter" @change="fetchTasks">
            <option value="all">All</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
        <div class="search-group">
          <input 
            type="text" 
            placeholder="Search tasks..." 
            v-model="searchQuery"
            @input="handleSearch"
          >
        </div>
      </div>

      <!-- Loading spinner when tasks are being fetched -->
      <div v-if="isLoading" class="loading-container">
        <div class="spinner"></div>
        <p>Loading tasks...</p>
      </div>

      <!-- Error message if API call fails -->
      <div v-else-if="error" class="error-container">
        <i class="fas fa-exclamation-triangle"></i>
        <p>{{ error }}</p>
        <button @click="fetchTasks" class="retry-button">Try Again</button>
      </div>

      <!-- Task list display when data is loaded -->
      <div v-else class="tasks-list">
        <div class="task-card" v-for="(task, index) in tasks" :key="task.id || index">
          <div class="task-header">
            <h3 class="task-title">{{ task.title }}</h3>
            <div class="task-badges">
              <span class="task-priority" :class="task.priority">{{ task.priority }}</span>
              <span class="task-status" :class="task.status">{{ task.status }}</span>
            </div>
          </div>
          <div class="task-body">
            <p class="task-description">{{ task.description }}</p>
            <div class="task-details">
              <div class="task-detail">
                <i class="fas fa-calendar"></i>
                <span>Due: {{ task.dueDate }}</span>
              </div>
              <div class="task-detail" v-if="task.customer">
                <i class="fas fa-user"></i>
                <span>Customer: {{ task.customer }}</span>
              </div>
            </div>
          </div>
          <div class="task-actions">
            <button 
              class="action-button" 
              :class="{ 'disabled': task.status === 'completed' }"
              @click="updateTaskStatus(task)"
              :disabled="isUpdating === task.id"
            >
              <span v-if="isUpdating === task.id">
                <i class="fas fa-spinner fa-spin"></i> Updating...
              </span>
              <span v-else>
                {{ task.status === 'pending' ? 'Start Task' : 
                   task.status === 'in-progress' ? 'Complete Task' : 'Completed' }}
              </span>
            </button>
          </div>
        </div>

        <div class="no-tasks" v-if="tasks.length === 0">
          <i class="fas fa-clipboard-check"></i>
          <p>No tasks found with current filters</p>
        </div>
      </div>
    </div>
  </employee-layout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'
import employeeTasksService from '@/services/employeeTasksService'

export default {
  name: 'EmployeeTasks',
  components: {
    EmployeeLayout
  },
  data() {
    return {
      searchQuery: '',
      statusFilter: 'all',
      priorityFilter: 'all',
      tasks: [],
      isLoading: false,
      isUpdating: null,
      error: null,
      searchTimeout: null
    }
  },
  created() {
    this.fetchTasks();
  },
  methods: {
    async fetchTasks() {
      this.isLoading = true;
      this.error = null;
      
      try {
        // Prepare query parameters
        const params = {};
        
        if (this.statusFilter !== 'all') {
          params.status = this.statusFilter;
        }
        
        if (this.priorityFilter !== 'all') {
          params.priority = this.priorityFilter;
        }
        
        if (this.searchQuery) {
          params.search = this.searchQuery;
        }
        
        // Fetch data from API
        const response = await employeeTasksService.getTasks(params);
        
        // Update component data with API response
        this.tasks = response.data.data;
      } catch (err) {
        console.error('Error fetching tasks:', err);
        this.error = 'Failed to load tasks. Please try again.';
      } finally {
        this.isLoading = false;
      }
    },
    
    handleSearch() {
      // Debounce search to prevent too many API calls
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      this.searchTimeout = setTimeout(() => {
        this.fetchTasks();
      }, 500);
    },
    
    async updateTaskStatus(task) {
      if (task.status === 'completed') return;
      
      this.isUpdating = task.id;
      
      try {
        let newStatus = task.status === 'pending' ? 'in-progress' : 'completed';
        
        // Call the API to update status
        const response = await employeeTasksService.updateTaskStatus(task.id, newStatus);
        
        // Update local task data if successful
        if (response.data.status === 'success') {
          task.status = newStatus;
          
          // Show success notification if you have one
          if (this.$notifications) {
            this.$notifications.add({
              type: 'success',
              message: `Task status updated to ${newStatus}`
            });
          }
        }
      } catch (err) {
        console.error('Error updating task status:', err);
        
        // Show error notification if you have one
        if (this.$notifications) {
          this.$notifications.add({
            type: 'error',
            message: 'Failed to update task status'
          });
        }
      } finally {
        this.isUpdating = null;
      }
    }
  }
}
</script>

<style scoped>
.employee-tasks {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 25px;
}

.page-header h1 {
  font-size: 2rem;
  color: #1a365d;
  margin-bottom: 8px;
}

.page-header p {
  color: #64748b;
  font-size: 1.1rem;
}

.tasks-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 25px;
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-group, .search-group {
  display: flex;
  align-items: center;
}

.filter-group label {
  margin-right: 8px;
  font-weight: 500;
  color: #334155;
}

.filter-group select, .search-group input {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.9rem;
  min-width: 120px;
}

.search-group {
  margin-left: auto;
}

.search-group input {
  min-width: 200px;
}

.tasks-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.task-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.task-header {
  padding: 15px 20px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.task-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #334155;
  flex-grow: 1;
}

.task-badges {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
  margin-left: 10px;
}

.task-priority, .task-status {
  font-size: 0.75rem;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.task-priority.high {
  background-color: #fecaca;
  color: #991b1b;
}

.task-priority.medium {
  background-color: #fed7aa;
  color: #9a3412;
}

.task-priority.low {
  background-color: #bfdbfe;
  color: #1e40af;
}

.task-status.pending {
  background-color: #fef3c7;
  color: #92400e;
}

.task-status.in-progress {
  background-color: #dbeafe;
  color: #1e40af;
}

.task-status.completed {
  background-color: #dcfce7;
  color: #166534;
}

.task-body {
  padding: 15px 20px;
  flex-grow: 1;
}

.task-description {
  font-size: 0.95rem;
  color: #4b5563;
  margin: 0 0 15px 0;
  line-height: 1.5;
}

.task-details {
  margin-top: 15px;
}

.task-detail {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.85rem;
  color: #64748b;
}

.task-detail i {
  margin-right: 8px;
  width: 14px;
}

.task-actions {
  padding: 15px 20px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
}

.action-button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  background-color: #2563eb;
  color: white;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.action-button:hover {
  background-color: #1d4ed8;
}

.action-button.disabled {
  background-color: #94a3b8;
  cursor: not-allowed;
}

.no-tasks {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px 0;
  color: #94a3b8;
}

.no-tasks i {
  font-size: 3rem;
  margin-bottom: 15px;
}

.no-tasks p {
  font-size: 1.1rem;
}

/* New loading and error styles */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px 0;
  color: #64748b;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #2563eb;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  text-align: center;
  padding: 30px;
  background-color: #fee2e2;
  border-radius: 8px;
  color: #b91c1c;
  margin: 20px 0;
}

.error-container i {
  font-size: 2rem;
  margin-bottom: 10px;
}

.retry-button {
  background-color: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 8px 16px;
  margin-top: 15px;
  font-weight: 500;
  cursor: pointer;
}

.retry-button:hover {
  background-color: #dc2626;
}

@media (max-width: 640px) {
  .tasks-filters {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-group {
    margin-left: 0;
    width: 100%;
  }
  
  .search-group input {
    width: 100%;
  }
}
</style>