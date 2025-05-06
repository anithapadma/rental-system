<template>
  <EmployeeLayout pageTitle="Dashboard">
    <div class="dashboard-content">
      <!-- First row - existing cards -->
      <div class="dashboard-card">
        <div class="card-header">
          <i class="fas fa-clipboard-list"></i>
          <h3>My Tasks</h3>
        </div>
        <div class="card-body">
          <div class="task-item">
            <span class="task-name">Process rental requests</span>
            <span class="task-status pending">Pending</span>
          </div>
          <div class="task-item">
            <span class="task-name">Update inventory items</span>
            <span class="task-status completed">Completed</span>
          </div>
          <div class="task-item">
            <span class="task-name">Contact customer #12458</span>
            <span class="task-status pending">Pending</span>
          </div>
        </div>
      </div>

      <div class="dashboard-card">
        <div class="card-header">
          <i class="fas fa-calendar"></i>
          <h3>Today's Schedule</h3>
        </div>
        <div class="card-body">
          <div class="schedule-item">
            <span class="schedule-time">9:00 AM</span>
            <span class="schedule-title">Morning check-in</span>
          </div>
          <div class="schedule-item">
            <span class="schedule-time">11:30 AM</span>
            <span class="schedule-title">Inventory check</span>
          </div>
          <div class="schedule-item">
            <span class="schedule-time">2:00 PM</span>
            <span class="schedule-title">Customer follow-ups</span>
          </div>
        </div>
      </div>

      <div class="dashboard-card">
        <div class="card-header">
          <i class="fas fa-dolly"></i>
          <h3>Recent Rentals</h3>
        </div>
        <div class="card-body">
          <div class="rental-item">
            <span class="rental-id">#45782</span>
            <span class="rental-customer">John Smith</span>
            <span class="rental-date">May 4, 2025</span>
          </div>
          <div class="rental-item">
            <span class="rental-id">#45780</span>
            <span class="rental-customer">Sarah Johnson</span>
            <span class="rental-date">May 3, 2025</span>
          </div>
          <div class="rental-item">
            <span class="rental-id">#45779</span>
            <span class="rental-customer">Mike Davis</span>
            <span class="rental-date">May 3, 2025</span>
          </div>
        </div>
      </div>

      <!-- Second row - Charts -->
      <div class="dashboard-card chart-card">
        <div class="card-header">
          <i class="fas fa-chart-line"></i>
          <h3>Performance Trends</h3>
        </div>
        <div class="card-body chart-container">
          <LineChart :chartData="performanceChartData" :options="chartOptions" />
        </div>
      </div>

      <div class="dashboard-card chart-card">
        <div class="card-header">
          <i class="fas fa-chart-pie"></i>
          <h3>Task Distribution</h3>
        </div>
        <div class="card-body chart-container">
          <DoughnutChart :chartData="taskDistributionData" :options="chartOptions" />
        </div>
      </div>

      <div class="dashboard-card chart-card">
        <div class="card-header">
          <i class="fas fa-chart-bar"></i>
          <h3>Weekly Rental Statistics</h3>
        </div>
        <div class="card-body chart-container">
          <BarChart :chartData="rentalStatsData" :options="chartOptions" />
        </div>
      </div>
      
      <!-- Employee List with Images -->
      <div class="dashboard-card employee-list-card">
        <div class="card-header">
          <i class="fas fa-users"></i>
          <h3>Team Members</h3>
        </div>
        <div class="card-body">
          <div class="team-grid">
            <div 
              v-for="employee in employees" 
              :key="employee.id" 
              class="employee-card"
              @click="openEmployeeModal(employee)"
            >
              <div class="employee-image">
                <img v-if="employee.image" :src="employee.image" :alt="employee.name">
                <div v-else class="employee-initials">{{ getInitials(employee.name) }}</div>
              </div>
              <div class="employee-info">
                <h4>{{ employee.name }}</h4>
                <p>{{ employee.role }}</p>
                <span class="department-tag">{{ employee.department }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Employee Detail Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="employee-modal">
        <button class="close-btn" @click="closeModal">&times;</button>
        
        <div class="modal-header">
          <div class="employee-image large">
            <img v-if="selectedEmployee.image" :src="selectedEmployee.image" :alt="selectedEmployee.name">
            <div v-else class="employee-initials large">{{ selectedEmployee ? getInitials(selectedEmployee.name) : '' }}</div>
          </div>
          <div class="employee-header-info">
            <h2>{{ selectedEmployee.name }}</h2>
            <p class="employee-role">{{ selectedEmployee.role }}</p>
            <div class="employee-status">
              <span class="status-dot" :class="selectedEmployee.isActive ? 'active' : 'inactive'"></span>
              <span>{{ selectedEmployee.isActive ? 'Active' : 'Inactive' }}</span>
            </div>
          </div>
        </div>
        
        <div class="modal-body">
          <div class="detail-section">
            <h3>Contact Information</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedEmployee.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ selectedEmployee.phone }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Department:</span>
                <span class="detail-value">{{ selectedEmployee.department }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Joined:</span>
                <span class="detail-value">{{ formatDate(selectedEmployee.joinDate) }}</span>
              </div>
            </div>
          </div>
          
          <div class="detail-section">
            <h3>Performance Metrics</h3>
            <div class="performance-metrics">
              <div class="metric-item">
                <div class="metric-value">{{ selectedEmployee.stats?.tasksCompleted || 0 }}</div>
                <div class="metric-label">Tasks Completed</div>
              </div>
              <div class="metric-item">
                <div class="metric-value">{{ selectedEmployee.stats?.avgRating || 0 }}★</div>
                <div class="metric-label">Average Rating</div>
              </div>
              <div class="metric-item">
                <div class="metric-value">{{ selectedEmployee.stats?.efficiency || 0 }}%</div>
                <div class="metric-label">Efficiency</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button @click="viewEmployeeProfile(selectedEmployee)" class="btn btn-primary">View Full Profile</button>
          <button @click="closeModal" class="btn btn-secondary">Close</button>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue';
import LineChart from '@/components/charts/LineChart.vue';
import DoughnutChart from '@/components/charts/DoughnutChart.vue';
import BarChart from '@/components/charts/BarChart.vue';

export default {
  name: 'EmployeeDashboard',
  components: {
    EmployeeLayout,
    LineChart,
    DoughnutChart,
    BarChart
  },
  data() {
    return {
      employeeName: 'Employee User',
      showModal: false,
      selectedEmployee: {},
      employees: [
        {
          id: 1,
          name: 'John Doe',
          role: 'Senior Rental Agent',
          department: 'Rental Operations',
          image: 'https://randomuser.me/api/portraits/men/1.jpg',
          email: 'john.doe@example.com',
          phone: '(555) 123-4567',
          joinDate: '2023-03-15',
          isActive: true,
          stats: {
            tasksCompleted: 124,
            avgRating: 4.8,
            efficiency: 94
          }
        },
        {
          id: 2,
          name: 'Sarah Johnson',
          role: 'Inventory Specialist',
          department: 'Inventory Management',
          image: 'https://randomuser.me/api/portraits/women/2.jpg',
          email: 'sarah.johnson@example.com',
          phone: '(555) 234-5678',
          joinDate: '2023-05-22',
          isActive: true,
          stats: {
            tasksCompleted: 98,
            avgRating: 4.6,
            efficiency: 91
          }
        },
        {
          id: 3,
          name: 'Michael Chen',
          role: 'Customer Service Rep',
          department: 'Customer Support',
          image: 'https://randomuser.me/api/portraits/men/3.jpg',
          email: 'michael.chen@example.com',
          phone: '(555) 345-6789',
          joinDate: '2024-01-10',
          isActive: true,
          stats: {
            tasksCompleted: 45,
            avgRating: 4.9,
            efficiency: 88
          }
        },
        {
          id: 4,
          name: 'Amanda Williams',
          role: 'Team Lead',
          department: 'Rental Operations',
          image: 'https://randomuser.me/api/portraits/women/4.jpg',
          email: 'amanda.williams@example.com',
          phone: '(555) 456-7890',
          joinDate: '2022-11-05',
          isActive: true,
          stats: {
            tasksCompleted: 187,
            avgRating: 4.7,
            efficiency: 96
          }
        }
      ],
      performanceChartData: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Performance Score',
          data: [85, 88, 92, 90, 94],
          borderColor: '#3490dc',
          backgroundColor: 'rgba(52, 144, 220, 0.1)',
          tension: 0.3,
          fill: true
        }]
      },
      taskDistributionData: {
        labels: ['Pending', 'In Progress', 'Completed', 'Overdue'],
        datasets: [{
          data: [12, 8, 18, 3],
          backgroundColor: [
            '#fcd34d', // Pending (amber)
            '#60a5fa', // In Progress (blue)
            '#34d399', // Completed (green)
            '#f87171'  // Overdue (red)
          ],
          borderColor: '#ffffff',
        }]
      },
      rentalStatsData: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'New Rentals',
          data: [5, 9, 7, 8, 12, 15, 6],
          backgroundColor: '#8b5cf6', // Purple
        }]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false
      }
    }
  },
  mounted() {
    // You could fetch employee-specific data here
    const userData = JSON.parse(localStorage.getItem('user') || '{}');
    if (userData.name) {
      this.employeeName = userData.name;
    }
  },
  methods: {
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase();
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }).format(date);
    },
    openEmployeeModal(employee) {
      this.selectedEmployee = { ...employee };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    viewEmployeeProfile(employee) {
      this.$router.push({ name: 'EmployeeProfile', params: { id: employee.id } });
    }
  }
}
</script>

<style scoped>
.dashboard-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.dashboard-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.card-header {
  background-color: #f1f5f9;
  padding: 15px 20px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
}

.card-header i {
  font-size: 1.2rem;
  color: #1a365d;
  margin-right: 10px;
}

.card-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #334155;
}

.card-body {
  padding: 20px;
}

/* Task styles */
.task-item {
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.task-item:last-child {
  border-bottom: none;
}

.task-name {
  font-weight: 500;
}

.task-status {
  font-size: 0.8rem;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: 500;
}

.task-status.pending {
  background-color: #fef3c7;
  color: #92400e;
}

.task-status.completed {
  background-color: #dcfce7;
  color: #166534;
}

/* Schedule styles */
.schedule-item {
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
}

.schedule-item:last-child {
  border-bottom: none;
}

.schedule-time {
  font-weight: 600;
  width: 80px;
  color: #1a365d;
}

.schedule-title {
  font-weight: 500;
}

/* Rental styles */
.rental-item {
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  display: grid;
  grid-template-columns: 80px 1fr auto;
  align-items: center;
}

.rental-item:last-child {
  border-bottom: none;
}

.rental-id {
  font-weight: 600;
  color: #1a365d;
}

.rental-customer {
  font-weight: 500;
}

.rental-date {
  color: #64748b;
  font-size: 0.85rem;
}

/* Chart styles */
.chart-card {
  display: flex;
  flex-direction: column;
}

.chart-container {
  height: 300px;
}

/* Employee list styles */
.employee-list-card {
  grid-column: 1 / -1;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 15px;
}

.employee-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 15px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.employee-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.employee-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e2e8f0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  border: 2px solid #f1f5f9;
}

.employee-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block; /* Ensure image is properly displayed */
}

/* Add error handling for images */
.employee-image img.error {
  display: none;
}

.employee-image.large {
  width: 120px;
  height: 120px;
}

.employee-initials {
  font-size: 1.8rem;
  font-weight: 600;
  color: #1a365d;
}

.employee-initials.large {
  font-size: 2.5rem;
}

.employee-info {
  text-align: center;
  width: 100%;
}

.employee-info h4 {
  margin: 0 0 5px;
  font-weight: 600;
  font-size: 1rem;
}

.employee-info p {
  margin: 0 0 8px;
  color: #64748b;
  font-size: 0.85rem;
}

.department-tag {
  display: inline-block;
  font-size: 0.75rem;
  background-color: #dbeafe;
  color: #1e40af;
  padding: 3px 8px;
  border-radius: 12px;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.employee-modal {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  position: relative;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #64748b;
}

.modal-header {
  padding: 25px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.employee-header-info {
  margin-left: 20px;
}

.employee-header-info h2 {
  margin: 0 0 5px;
  font-size: 1.5rem;
}

.employee-role {
  margin: 0 0 10px;
  color: #64748b;
}

.employee-status {
  display: flex;
  align-items: center;
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-right: 6px;
}

.status-dot.active {
  background-color: #10b981;
}

.status-dot.inactive {
  background-color: #f87171;
}

.modal-body {
  padding: 25px;
}

.detail-section {
  margin-bottom: 25px;
}

.detail-section h3 {
  margin: 0 0 15px;
  font-size: 1.1rem;
  color: #334155;
  font-weight: 600;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 8px;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.detail-item {
  display: flex;
  flex-direction: column;
}

.detail-label {
  font-size: 0.8rem;
  color: #64748b;
  margin-bottom: 3px;
}

.detail-value {
  font-weight: 500;
}

.performance-metrics {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
}

.metric-item {
  text-align: center;
  padding: 15px 10px;
  background-color: #f1f5f9;
  border-radius: 8px;
}

.metric-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e40af;
  margin-bottom: 5px;
}

.metric-label {
  font-size: 0.85rem;
  color: #64748b;
}

.modal-footer {
  padding: 15px 25px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  font-size: 0.9rem;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-secondary {
  background-color: #e2e8f0;
  color: #334155;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-secondary:hover {
  background-color: #cbd5e1;
}
</style>