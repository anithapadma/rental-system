<template>
  <employee-layout>
    <div class="employee-profile">
      <!-- Main header section -->
      <div class="dashboard-header">
        <h1>Dashboard</h1>
        <!-- Notification icon could go here -->
      </div>
      
      <!-- Profile header section -->
      <div class="profile-header-section">
        <div class="profile-title-container">
          <h1 class="profile-title">Employee Profile</h1>
          <div class="profile-actions">
            <button class="btn btn-outline">Edit Profile</button>
            <button class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>
      
      <!-- Main content area with cards layout -->
      <div class="profile-content">
        <!-- Left card with profile details -->
        <div class="profile-card user-info-card">
          <div class="profile-center">
            <div class="profile-avatar">
              <div class="avatar-circle large">
                <span>{{ userInitials }}</span>
              </div>
            </div>
            <div class="profile-name-container">
              <h2 class="profile-name">{{ employeeData.name }}</h2>
              <p class="profile-role">{{ employeeData.role }}</p>
              <div class="status-indicator">
                <span class="status-dot active"></span>
                <span class="status-text">Active</span>
              </div>
            </div>
            
            <div class="profile-details">
              <div class="detail-item">
                <div class="detail-label">Email:</div>
                <div class="detail-value">{{ employeeData.email }}</div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Phone:</div>
                <div class="detail-value">{{ employeeData.phone }}</div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Department:</div>
                <div class="detail-value">{{ employeeData.department }}</div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Joined:</div>
                <div class="detail-value">{{ formatDate(employeeData.joinDate) }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right card with performance stats -->
        <div class="profile-card stats-card">
          <h2 class="stats-title">Performance Statistics</h2>
          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-value">{{ employeeData.stats.tasksCompleted }}</div>
              <div class="stat-label">Tasks Completed</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ employeeData.stats.avgRating }}★</div>
              <div class="stat-label">Average Rating</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ employeeData.stats.hoursLogged }}h</div>
              <div class="stat-label">Hours Logged</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ employeeData.stats.efficiency }}%</div>
              <div class="stat-label">Efficiency</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tabs section for tasks, skills, documents -->
      <div class="profile-tabs-section">
        <div class="tabs-container">
          <div class="tabs-header">
            <button 
              v-for="tab in tabs" 
              :key="tab.id" 
              :class="['tab-button', { active: activeTab === tab.id }]"
              @click="activeTab = tab.id"
            >
              <i :class="tab.icon"></i>
              {{ tab.title }}
            </button>
          </div>
          
          <div class="tab-content-container">
            <!-- Tasks tab content -->
            <div v-if="activeTab === 'tasks'" class="tab-content">
              <h3>Recent Tasks</h3>
              <div class="task-list">
                <div v-for="(task, index) in employeeData.recentTasks" :key="index" class="task-item">
                  <div class="task-status" :class="task.status"></div>
                  <div class="task-content">
                    <div class="task-header">
                      <span class="task-title">{{ task.title }}</span>
                      <span class="task-date">{{ formatDate(task.date) }}</span>
                    </div>
                    <div class="task-description">{{ task.description }}</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Skills tab content -->
            <div v-if="activeTab === 'skills'" class="tab-content">
              <h3>Skills & Expertise</h3>
              <div class="skills-container">
                <div v-for="(skill, index) in employeeData.skills" :key="index" class="skill-item">
                  <div class="skill-name">{{ skill.name }}</div>
                  <div class="skill-bar-container">
                    <div class="skill-bar" :style="{ width: skill.level + '%' }"></div>
                  </div>
                  <div class="skill-level">{{ skill.level }}%</div>
                </div>
              </div>
            </div>
            
            <!-- Documents tab content -->
            <div v-if="activeTab === 'documents'" class="tab-content">
              <h3>Documents & Certifications</h3>
              <div class="documents-container">
                <div v-for="(doc, index) in employeeData.documents" :key="index" class="document-item">
                  <i class="fas fa-file-alt document-icon"></i>
                  <div class="document-info">
                    <div class="document-name">{{ doc.name }}</div>
                    <div class="document-date">Issued: {{ formatDate(doc.issueDate) }}</div>
                  </div>
                  <button class="btn btn-outline btn-sm">View</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </employee-layout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'

export default {
  name: 'EmployeeProfile',
  components: {
    EmployeeLayout
  },
  data() {
    return {
      activeTab: 'tasks',
      tabs: [
        { id: 'tasks', title: 'Recent Tasks', icon: 'fas fa-tasks' },
        { id: 'skills', title: 'Skills', icon: 'fas fa-chart-line' },
        { id: 'documents', title: 'Documents', icon: 'fas fa-file-alt' }
      ],
      employeeData: {
        id: 1,
        name: 'John Doe',
        role: 'Senior Rental Agent',
        email: 'john.doe@example.com',
        phone: '(555) 123-4567',
        department: 'Rental Operations',
        joinDate: '2023-03-15',
        stats: {
          tasksCompleted: 124,
          avgRating: 4.8,
          hoursLogged: 186,
          efficiency: 94
        },
        skills: [
          { name: 'Customer Service', level: 95 },
          { name: 'Inventory Management', level: 85 },
          { name: 'Contract Administration', level: 78 },
          { name: 'Problem Resolution', level: 92 }
        ],
        recentTasks: [
          {
            title: 'Process Rental Returns',
            description: 'Process returns for the weekend rentals and update inventory status',
            date: '2025-05-04',
            status: 'completed'
          },
          {
            title: 'Customer Follow-up',
            description: 'Contact customers for feedback on recent rentals',
            date: '2025-05-03',
            status: 'completed'
          },
          {
            title: 'Inventory Audit',
            description: 'Complete quarterly audit of high-value rental items',
            date: '2025-05-02',
            status: 'completed'
          },
          {
            title: 'Update Training Materials',
            description: 'Review and update new employee training materials for rental procedures',
            date: '2025-05-01',
            status: 'in-progress'
          }
        ],
        documents: [
          { name: 'Equipment Certification', issueDate: '2024-09-15' },
          { name: 'Safety Training', issueDate: '2024-11-23' },
          { name: 'Customer Service Excellence', issueDate: '2024-07-10' },
          { name: 'Inventory Management Certificate', issueDate: '2023-12-05' }
        ]
      }
    }
  },
  computed: {
    userInitials() {
      if (!this.employeeData.name) return 'E';
      return this.employeeData.name
        .split(' ')
        .map(name => name.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(date);
    }
  }
}
</script>

<style scoped>
.employee-profile {
  background-color: #f1f5f9;
  min-height: 100%;
  padding: 0;
  width: 100%;
}

.dashboard-header {
  background-color: white;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.dashboard-header h1 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.profile-header-section {
  background-color: #f1f5f9;
  padding: 20px 24px;
}

.profile-title-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.profile-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.profile-actions {
  display: flex;
  gap: 12px;
}

.profile-content {
  display: grid;
  grid-template-columns: minmax(300px, 400px) 1fr;
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px 24px;
}

.profile-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.user-info-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px;
}

.profile-center {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.profile-avatar {
  margin-bottom: 16px;
}

.avatar-circle {
  width: 100px;
  height: 100px;
  background-color: #4299e1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.5rem;
  font-weight: 600;
}

.profile-name-container {
  margin-bottom: 20px;
}

.profile-name {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px;
}

.profile-role {
  font-size: 1rem;
  color: #64748b;
  margin: 0 0 8px;
}

.status-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #10b981;
}

.status-text {
  font-size: 0.875rem;
  color: #64748b;
}

.profile-details {
  width: 100%;
  border-top: 1px solid #e5e7eb;
  padding-top: 20px;
  margin-top: 20px;
}

.detail-item {
  display: flex;
  padding: 8px 0;
  text-align: left;
}

.detail-label {
  width: 100px;
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
}

.detail-value {
  flex: 1;
  font-size: 0.875rem;
  color: #334155;
}

.stats-card {
  padding: 24px;
}

.stats-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-item {
  text-align: center;
  padding: 16px;
  border-radius: 6px;
  background-color: #f8fafc;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
}

.profile-tabs-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px 24px;
}

.tabs-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-top: 24px;
}

.tabs-header {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f8fafc;
}

.tab-button {
  padding: 16px 24px;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 500;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  transition: all 0.2s;
}

.tab-button i {
  font-size: 0.875rem;
}

.tab-button.active {
  color: #3b82f6;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background-color: #3b82f6;
}

.tab-button:hover {
  background-color: rgba(59, 130, 246, 0.05);
}

.tab-content-container {
  padding: 24px;
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

.task-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-top: 16px;
}

.task-item {
  display: flex;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
}

.task-status {
  width: 4px;
  flex-shrink: 0;
  background-color: #cbd5e1;
}

.task-status.completed {
  background-color: #10b981;
}

.task-status.in-progress {
  background-color: #f59e0b;
}

.task-content {
  flex: 1;
  padding: 12px 16px;
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.task-title {
  font-weight: 500;
  color: #1a365d;
}

.task-date {
  font-size: 0.75rem;
  color: #64748b;
}

.task-description {
  font-size: 0.875rem;
  color: #475569;
}

.skills-container {
  margin-top: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.skill-item {
  display: flex;
  align-items: center;
  gap: 16px;
}

.skill-name {
  flex: 0 0 180px;
  font-weight: 500;
  color: #1a365d;
}

.skill-bar-container {
  flex: 1;
  height: 8px;
  background-color: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.skill-bar {
  height: 100%;
  background-color: #3b82f6;
  border-radius: 4px;
}

.skill-level {
  width: 40px;
  text-align: right;
  font-size: 0.875rem;
  color: #64748b;
}

.documents-container {
  margin-top: 16px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.document-item {
  display: flex;
  align-items: center;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
}

.document-icon {
  font-size: 1.25rem;
  color: #3b82f6;
  margin-right: 12px;
}

.document-info {
  flex: 1;
}

.document-name {
  font-weight: 500;
  color: #1a365d;
  margin-bottom: 2px;
}

.document-date {
  font-size: 0.75rem;
  color: #64748b;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-sm {
  padding: 6px 10px;
  font-size: 0.75rem;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-outline {
  background-color: transparent;
  color: #3b82f6;
  border: 1px solid #3b82f6;
}

.btn-outline:hover {
  background-color: rgba(59, 130, 246, 0.1);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .profile-content {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .profile-title-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .profile-actions {
    width: 100%;
  }
  
  .tabs-header {
    overflow-x: auto;
  }
  
  .tab-button {
    padding: 16px 16px;
    white-space: nowrap;
  }
  
  .documents-container {
    grid-template-columns: 1fr;
  }
  
  .skill-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .skill-name {
    width: 100%;
    margin-bottom: 8px;
  }
  
  .skill-bar-container {
    width: 100%;
  }
  
  .skill-level {
    width: 100%;
    text-align: left;
    margin-top: 4px;
  }
}
</style>