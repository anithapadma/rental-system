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
          <select id="status-filter" v-model="statusFilter">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="priority-filter">Priority:</label>
          <select id="priority-filter" v-model="priorityFilter">
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
          >
        </div>
      </div>

      <div class="tasks-list">
        <div class="task-card" v-for="(task, index) in filteredTasks" :key="index">
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
              @click="updateTaskStatus(index)"
            >
              {{ task.status === 'pending' ? 'Start Task' : 
                 task.status === 'in-progress' ? 'Complete Task' : 'Completed' }}
            </button>
          </div>
        </div>

        <div class="no-tasks" v-if="filteredTasks.length === 0">
          <i class="fas fa-clipboard-check"></i>
          <p>No tasks found with current filters</p>
        </div>
      </div>
    </div>
  </employee-layout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'

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
      tasks: [
        {
          title: 'Process rental request #4582',
          description: 'Review and approve rental request from John Smith for equipment rental on May 10th.',
          status: 'pending',
          priority: 'high',
          dueDate: 'May 6, 2025',
          customer: 'John Smith'
        },
        {
          title: 'Update inventory items',
          description: 'Check physical inventory against system records and update any discrepancies.',
          status: 'completed',
          priority: 'medium',
          dueDate: 'May 4, 2025'
        },
        {
          title: 'Contact customer #12458',
          description: 'Follow up with Sarah Johnson about her rental experience and collect feedback.',
          status: 'pending',
          priority: 'medium',
          dueDate: 'May 6, 2025',
          customer: 'Sarah Johnson'
        },
        {
          title: 'Prepare monthly equipment report',
          description: 'Compile data on equipment usage, maintenance, and availability for the monthly report.',
          status: 'in-progress',
          priority: 'medium',
          dueDate: 'May 7, 2025'
        },
        {
          title: 'Schedule maintenance for item #873',
          description: 'Contact maintenance team to schedule routine service for the portable generator.',
          status: 'pending',
          priority: 'low',
          dueDate: 'May 10, 2025'
        }
      ]
    }
  },
  computed: {
    filteredTasks() {
      return this.tasks.filter(task => {
        // Status filter
        if (this.statusFilter !== 'all' && task.status !== this.statusFilter) {
          return false;
        }
        
        // Priority filter
        if (this.priorityFilter !== 'all' && task.priority !== this.priorityFilter) {
          return false;
        }
        
        // Search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          return task.title.toLowerCase().includes(query) || 
                 task.description.toLowerCase().includes(query) ||
                 (task.customer && task.customer.toLowerCase().includes(query));
        }
        
        return true;
      });
    }
  },
  methods: {
    updateTaskStatus(index) {
      const task = this.tasks[index];
      if (task.status === 'pending') {
        task.status = 'in-progress';
      } else if (task.status === 'in-progress') {
        task.status = 'completed';
      }
      // No action if already completed
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