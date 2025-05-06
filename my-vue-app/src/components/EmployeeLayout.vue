<template>
  <div class="employee-layout">
    <EmployeeSidebar />
    <div class="content-area" :class="{ 'with-sidebar': !sidebarCollapsed }">
      <header class="employee-header">
        <div class="header-left">
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>
        <div class="header-right">
          <div class="notification-bell" @click="toggleNotifications">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" v-if="unreadNotifications.length">{{ unreadNotifications.length }}</span>
          </div>
          <div class="notifications-panel" v-if="showNotifications">
            <div class="notifications-header">
              <h3>Notifications</h3>
              <button class="mark-all-read" @click="markAllAsRead">Mark all as read</button>
            </div>
            <div class="notifications-content">
              <div v-if="notifications.length === 0" class="no-notifications">
                No notifications
              </div>
              <div 
                v-for="(notification, index) in notifications" 
                :key="index" 
                class="notification-item"
                :class="{ 'unread': !notification.read }"
              >
                <div class="notification-icon" :class="notification.type">
                  <i :class="getIconForType(notification.type)"></i>
                </div>
                <div class="notification-content">
                  <div class="notification-message">{{ notification.message }}</div>
                  <div class="notification-time">{{ formatNotificationTime(notification.time) }}</div>
                </div>
                <button class="notification-action" @click="markAsRead(index)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>
      <main class="employee-main-content">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
import EmployeeSidebar from './EmployeeSidebar.vue';

export default {
  name: 'EmployeeLayout',
  components: {
    EmployeeSidebar
  },
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    }
  },
  data() {
    return {
      sidebarCollapsed: false,
      showNotifications: false,
      notifications: [
        {
          message: 'New task assigned: Inventory check for Section B',
          time: new Date(Date.now() - 25 * 60 * 1000), // 25 minutes ago
          type: 'task',
          read: false
        },
        {
          message: 'Rental request approved for customer #1254',
          time: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
          type: 'approval',
          read: false
        },
        {
          message: 'Your inventory report has been reviewed',
          time: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000), // 1 day ago
          type: 'info',
          read: true
        },
        {
          message: 'System maintenance scheduled for tonight at 2 AM',
          time: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago
          type: 'system',
          read: true
        }
      ]
    };
  },
  computed: {
    unreadNotifications() {
      return this.notifications.filter(n => !n.read);
    }
  },
  methods: {
    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
    },
    markAsRead(index) {
      this.notifications[index].read = true;
    },
    markAllAsRead() {
      this.notifications.forEach(notification => {
        notification.read = true;
      });
    },
    getIconForType(type) {
      const icons = {
        task: 'fas fa-tasks',
        approval: 'fas fa-check-circle',
        info: 'fas fa-info-circle',
        system: 'fas fa-cog',
        alert: 'fas fa-exclamation-circle'
      };
      return icons[type] || 'fas fa-bell';
    },
    formatNotificationTime(time) {
      const now = new Date();
      const diff = now - time;
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);
      
      if (days > 0) {
        return `${days} day${days !== 1 ? 's' : ''} ago`;
      } else if (hours > 0) {
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
      } else if (minutes > 0) {
        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
      } else {
        return 'Just now';
      }
    }
  },
  mounted() {
    // Listen for sidebar collapse/expand events
    window.addEventListener('sidebar-toggle', (event) => {
      this.sidebarCollapsed = event.detail.collapsed;
    });
  }
}
</script>

<style scoped>
.employee-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f1f5f9;
}

.content-area {
  flex: 1;
  margin-left: 70px;
  transition: margin-left 0.3s ease;
}

.content-area.with-sidebar {
  margin-left: 250px;
}

.employee-header {
  background-color: #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 16px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 70px;
  margin-top:-58px;
}

.header-left {
  display: flex;
  align-items: center;
}

.page-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #0f172a;
}

.header-right {
  display: flex;
  align-items: center;
  position: relative;
}

.notification-bell {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  transition: background-color 0.2s;
}

.notification-bell:hover {
  background-color: #e2e8f0;
}

.notification-bell i {
  font-size: 1.2rem;
  color: #64748b;
}

.notification-badge {
  position: absolute;
  top: 0;
  right: 0;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background-color: #ef4444;
  color: white;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notifications-panel {
  position: absolute;
  top: 55px;
  right: 0;
  width: 350px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  z-index: 100;
  max-height: 400px;
  display: flex;
  flex-direction: column;
}

.notifications-header {
  padding: 16px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notifications-header h3 {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #0f172a;
}

.mark-all-read {
  background: none;
  border: none;
  color: #3b82f6;
  cursor: pointer;
  font-size: 0.875rem;
}

.mark-all-read:hover {
  text-decoration: underline;
}

.notifications-content {
  overflow-y: auto;
  max-height: 350px;
}

.no-notifications {
  padding: 24px;
  text-align: center;
  color: #64748b;
}

.notification-item {
  padding: 12px 16px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background-color: #f8fafc;
}

.notification-item.unread {
  background-color: #f0f9ff;
}

.notification-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.notification-icon.task {
  background-color: #dbeafe;
  color: #2563eb;
}

.notification-icon.approval {
  background-color: #dcfce7;
  color: #16a34a;
}

.notification-icon.info {
  background-color: #e0f2fe;
  color: #0284c7;
}

.notification-icon.system {
  background-color: #fef3c7;
  color: #d97706;
}

.notification-icon.alert {
  background-color: #fee2e2;
  color: #dc2626;
}

.notification-content {
  flex: 1;
}

.notification-message {
  font-size: 0.875rem;
  color: #0f172a;
  margin-bottom: 4px;
}

.notification-time {
  font-size: 0.75rem;
  color: #64748b;
}

.notification-action {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.notification-action:hover {
  background-color: #e2e8f0;
  color: #64748b;
}

.employee-main-content {
  padding: 24px;
}

@media (max-width: 768px) {
  .content-area {
    margin-left: 70px;
  }
  
  .notifications-panel {
    width: 100%;
    right: 0;
    position: fixed;
    top: 70px;
    border-radius: 0;
  }
}
</style>