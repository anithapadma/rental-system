<template>
  <div class="employee-sidebar" :class="{ collapsed: isCollapsed }">
    <div class="sidebar-header">
      <div class="logo-container">
        <img src="../assets/logo.png" alt="Company Logo" class="logo">
        <span v-if="!isCollapsed" class="company-name">RentalPro</span>
      </div>
      <button class="collapse-button" @click="toggleCollapse">
        <i class="fas" :class="isCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
      </button>
    </div>
    
    <div class="user-info" v-if="!isCollapsed">
      <div class="avatar">{{ userInitials }}</div>
      <div class="user-details">
        <div class="user-name">{{ userName }}</div>
        <div class="user-role">{{ userRole }}</div>
      </div>
    </div>
    <div class="user-info-collapsed" v-else>
      <div class="avatar small">{{ userInitials }}</div>
    </div>
    
    <nav class="sidebar-nav">
      <router-link 
        v-for="item in navItems" 
        :key="item.path" 
        :to="item.path" 
        class="nav-item" 
        :class="{ active: $route.path === item.path }">
        <i :class="['nav-icon', item.icon]"></i>
        <span v-if="!isCollapsed" class="nav-label">{{ item.label }}</span>
      </router-link>
    </nav>
    
    <div class="sidebar-footer">
      <button class="logout-button" @click="logout">
        <i class="fas fa-sign-out-alt"></i>
        <span v-if="!isCollapsed">Logout</span>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmployeeSidebar',
  data() {
    return {
      isCollapsed: false,
      navItems: [
        { path: '/employee/dashboard', label: 'Dashboard', icon: 'fas fa-tachometer-alt' },
        { path: '/employee/inventory', label: 'Inventory', icon: 'fas fa-boxes' },
        { path: '/employee/tasks', label: 'My Tasks', icon: 'fas fa-tasks' },
        // { path: '/employee/profile', label: 'My Profile', icon: 'fas fa-user' }
      ]
    }
  },
  computed: {
    userName() {
      // Get from localStorage or Vuex store
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.name || 'Employee'
    },
    userRole() {
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.role || 'employee'
    },
    userInitials() {
      const name = this.userName
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
    }
  },
  methods: {
    toggleCollapse() {
      this.isCollapsed = !this.isCollapsed
    },
    logout() {
      // Clear user data
      localStorage.removeItem('user')
      // Redirect to login page
      this.$router.push('/')
    }
  }
}
</script>

<style scoped>
.employee-sidebar {
  background-color: #1e293b;
  color: #f8fafc;
  height: 100vh;
  width: 250px;
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  z-index: 100;
}

.employee-sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  border-bottom: 1px solid #334155;
}

.logo-container {
  display: flex;
  align-items: center;
  overflow: hidden;
}

.logo {
  height: 30px;
  width: 30px;
  object-fit: contain;
}

.company-name {
  margin-left: 10px;
  font-weight: 600;
  font-size: 1.25rem;
  white-space: nowrap;
}

.collapse-button {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.collapse-button:hover {
  color: #f8fafc;
  background-color: #334155;
}

.user-info {
  padding: 16px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #334155;
}

.user-info-collapsed {
  padding: 16px 0;
  display: flex;
  justify-content: center;
  border-bottom: 1px solid #334155;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.avatar.small {
  width: 32px;
  height: 32px;
  font-size: 0.8rem;
}

.user-details {
  margin-left: 12px;
  overflow: hidden;
}

.user-name {
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.8rem;
  color: #94a3b8;
  white-space: nowrap;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  text-decoration: none;
  color: #cbd5e1;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background-color: #334155;
  color: #f8fafc;
}

.nav-item.active {
  color: #f8fafc;
  background-color: rgba(59, 130, 246, 0.1);
  border-left-color: #3b82f6;
}

.nav-icon {
  font-size: 1.1rem;
  width: 24px;
  text-align: center;
}

.nav-label {
  margin-left: 12px;
  white-space: nowrap;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid #334155;
}

.logout-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  border: none;
  border-radius: 6px;
  background-color: #475569;
  color: #f8fafc;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.logout-button:hover {
  background-color: #64748b;
}

.logout-button i {
  margin-right: 8px;
}

@media (max-width: 768px) {
  .employee-sidebar {
    width: 70px;
  }
  
  .employee-sidebar:not(.collapsed) {
    width: 250px;
  }
}
</style>