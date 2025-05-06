<template>
  <employee-layout>
    <div class="employee-profile">
      <div class="page-header">
        <h1>Employee Profile</h1>
        <p>Your personal information and performance statistics</p>
      </div>

      <div class="profile-sections">
        <div class="employee-profile-container">
          <header class="page-header">
            <div class="header-content">
              <h1 class="page-title">Employee Profile</h1>
              <div class="header-actions">
                <button class="btn btn-outline">Edit Profile</button>
                <button class="btn btn-primary">Save Changes</button>
              </div>
            </div>
          </header>

          <div class="profile-content">
            <div class="profile-card">
              <div class="profile-header">
                <div class="avatar-container">
                  <div class="avatar-circle large">
                    <span>{{ userInitials }}</span>
                  </div>
                </div>
                <div class="profile-info">
                  <h2 class="employee-name">{{ employeeData.name }}</h2>
                  <p class="employee-role">{{ employeeData.role }}</p>
                  <div class="employee-status">
                    <span class="status-dot active"></span>
                    <span>Active</span>
                  </div>
                </div>
              </div>

              <div class="profile-details">
                <div class="detail-group">
                  <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ employeeData.email }}</span>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ employeeData.phone }}</span>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value">{{ employeeData.department }}</span>
                  </div>
                  <div class="detail-item">
                    <span class="detail-label">Joined:</span>
                    <span class="detail-value">{{ formatDate(employeeData.joinDate) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="profile-stats">
              <div class="stats-card">
                <div class="stats-header">
                  <h3>Performance Statistics</h3>
                </div>
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

            <div class="employee-sections">
              <div class="section-tabs">
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

              <div class="section-content">
                <!-- Recent Tasks -->
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

                <!-- Skills -->
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

                <!-- Documents -->
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
.employee-profile-container {
  padding: 0;
  height: 100%;
}

.page-header {
  background-color: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  padding: 20px 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1a365d;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-sm {
  padding: 5px 10px;
  font-size: 0.875rem;
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

.profile-content {
  padding: 24px;
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}

@media (min-width: 992px) {
  .profile-content {
    grid-template-columns: 1fr 1fr;
  }
}

.profile-card {
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  grid-column: span 1;
}

.profile-header {
  padding: 24px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #e5e7eb;
}

.avatar-container {
  margin-right: 24px;
}

.avatar-circle {
  width: 60px;
  height: 60px;
  background-color: #3b82f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  font-weight: 600;
}

.avatar-circle.large {
  width: 80px;
  height: 80px;
  font-size: 2rem;
}

.profile-info {
  flex: 1;
}

.employee-name {
  margin: 0 0 4px 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a365d;
}

.employee-role {
  margin: 0 0 8px 0;
  font-size: 1rem;
  color: #64748b;
}

.employee-status {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  color: #64748b;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 6px;
  background-color: #cbd5e1;
}

.status-dot.active {
  background-color: #10b981;
}

.profile-details {
  padding: 24px;
}

.detail-group {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 768px) {
  .detail-group {
    grid-template-columns: repeat(2, 1fr);
  }
}

.detail-item {
  display: flex;
  flex-direction: column;
}

.detail-label {
  font-size: 0.875rem;
  color: #64748b;
  margin-bottom: 4px;
}

.detail-value {
  font-size: 1rem;
  color: #0f172a;
  font-weight: 500;
}

.profile-stats {
  grid-column: span 1;
}

.stats-card {
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.stats-header {
  padding: 16px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.stats-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a365d;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  padding: 16px;
  gap: 16px;
}

@media (min-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-item {
  text-align: center;
  padding: 12px;
  border-radius: 6px;
  background-color: #f8fafc;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
}

.employee-sections {
  grid-column: 1 / -1;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.section-tabs {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f8fafc;
}

.tab-button {
  padding: 16px 20px;
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

.section-content {
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
  grid-template-columns: repeat(1, 1fr);
  gap: 16px;
}

@media (min-width: 768px) {
  .documents-container {
    grid-template-columns: repeat(2, 1fr);
  }
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

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>