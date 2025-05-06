<template>
  <div :class="['sidebar-container', { 'collapsed': isCollapsed }]">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="logo-container">
        <i class="fas fa-laptop-code logo-icon pulse"></i>
        <h1 class="logo-text">Track New</h1>
      </div>
      <button @click="toggleCollapsed" class="collapse-btn" :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
        <i :class="isCollapsed ? 'fas fa-angle-right' : 'fas fa-angle-left'"></i>
      </button>
    </div>
    
    <!-- User Info -->
    <div class="user-info">
      <div class="user-avatar">
        <div class="avatar-circle">
          <span>{{ userInitials }}</span>
        </div>
      </div>
      <div v-if="!isCollapsed" class="user-details">
        <p style="margin-left: -50px;" class="user-name ">{{ userName }}</p>
        <div class="user-status">
          <span class="status-dot"></span>
          <span class="user-role">{{ userRole }}</span>
        </div>
      </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="sidebar-nav">
      <router-link to="/dashboard" class="nav-item" active-class="active" exact>
        <div class="nav-icon-wrapper">
          <i class="fas fa-tachometer-alt nav-icon"></i>
        </div>
        <transition name="fade-slide">
          <span v-if="!isCollapsed" class="nav-text">Dashboard</span>
        </transition>
        <div class="active-indicator"></div>
      </router-link>
      
      <!-- Rental Module Section -->
      <div class="nav-section">
        <transition name="fade">
          <p v-if="!isCollapsed" class="section-title">
            <span class="section-line"></span>
            <span>Rental Module</span>
          </p>
        </transition>
        
        <router-link to="/rental-list" class="nav-item" active-class="active">
          <div class="nav-icon-wrapper">
            <i class="fas fa-clipboard-list nav-icon"></i>
          </div>
          <transition name="fade-slide">
            <span v-if="!isCollapsed" class="nav-text">Rental List</span>
          </transition>
          <div class="active-indicator"></div>
        </router-link>
        
        <router-link to="/inventory-items" class="nav-item" active-class="active">
          <div class="nav-icon-wrapper">
            <i class="fas fa-boxes nav-icon"></i>
          </div>
          <transition name="fade-slide">
            <span v-if="!isCollapsed" class="nav-text">Inventory Items</span>
          </transition>
          <div class="active-indicator"></div>
        </router-link>
        
        <router-link to="/agreements" class="nav-item" active-class="active">
          <div class="nav-icon-wrapper">
            <i class="fas fa-file-contract nav-icon"></i>
          </div>
          <transition name="fade-slide">
            <span v-if="!isCollapsed" class="nav-text">Agreements</span>
          </transition>
          <div class="active-indicator"></div>
        </router-link>
        
        <router-link to="/settings" class="nav-item" active-class="active">
          <div class="nav-icon-wrapper">
            <i class="fas fa-cog nav-icon"></i>
          </div>
          <transition name="fade-slide">
            <span v-if="!isCollapsed" class="nav-text">Settings</span>
          </transition>
          <div class="active-indicator"></div>
        </router-link>
      </div>
      
      <!-- Reports Section -->
      <div class="nav-section">
        <transition name="fade">
          <p v-if="!isCollapsed" class="section-title">
            <span class="section-line"></span>
            <span>Analytics & Reports</span>
          </p>
        </transition>
        
        <!-- Analytics Section -->
        <div class="nav-group">
          <div @click="toggleAnalyticsMenu" class="nav-item nav-group-header">
            <div class="nav-icon-wrapper">
              <i class="fas fa-chart-line nav-icon"></i>
            </div>
            <transition name="fade-slide">
              <span v-if="!isCollapsed"  style="margin-right:80px;" class="nav-text">Analytics</span>
            </transition>
            <transition name="fade-slide">
              <i v-if="!isCollapsed" :class="['fas', analyticsMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right', 'nav-chevron']"></i>
            </transition>
            <div class="active-indicator"></div>
          </div>
          
          <transition name="collapse">
            <div v-show="analyticsMenuOpen || $route.path.startsWith('/analytics')" class="nav-group-items">
              <router-link to="/analytics" class="nav-item sub-nav-item" active-class="active" exact>
                <div class="nav-icon-wrapper">
                  <i class="fas fa-tachometer-alt nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Overview</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <!-- <router-link to="/analytics/revenue" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-dollar-sign nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Revenue Analytics</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/analytics/performance" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-tachometer-alt nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Performance</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/analytics/usage" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-chart-pie nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Usage Metrics</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/analytics/customers" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-users nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Customer Analytics</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link> -->
            </div>
          </transition>
        </div>
        
        <div class="nav-divider"></div>
        
        <!-- Reports Section -->
        <div class="nav-group">
          <div @click="toggleReportsMenu" class="nav-item nav-group-header">
            <div class="nav-icon-wrapper">
              <i class="fas fa-file-alt nav-icon"></i>
            </div>
            <transition name="fade-slide">
              <span v-if="!isCollapsed" style="margin-right:44px;" class="nav-text">Reports Center</span>
            </transition>
            <transition name="fade-slide">
              <i v-if="!isCollapsed" :class="['fas', reportsMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right', 'nav-chevron']"></i>
            </transition>
            <div class="active-indicator"></div>
          </div>
          
          <transition name="collapse">
            <div v-show="reportsMenuOpen || $route.path.startsWith('/reports')" class="nav-group-items">
              <router-link to="/reports" class="nav-item sub-nav-item" active-class="active" exact>
                <div class="nav-icon-wrapper">
                  <i class="fas fa-clipboard nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">All Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <!-- <router-link to="/reports/revenue" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-money-bill-wave nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Revenue Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/reports/inventory" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-warehouse nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Inventory Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/reports/rentals" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-truck-loading nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Rental Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/reports/customers" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-user-chart nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Customer Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link>
              
              <router-link to="/reports/export" class="nav-item sub-nav-item" active-class="active">
                <div class="nav-icon-wrapper">
                  <i class="fas fa-file-export nav-icon"></i>
                </div>
                <transition name="fade-slide">
                  <span v-if="!isCollapsed" class="nav-text">Export Reports</span>
                </transition>
                <div class="active-indicator"></div>
              </router-link> -->
            </div>
          </transition>
        </div>
      </div>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <button @click="logout" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <transition name="fade-slide">
          <span v-if="!isCollapsed">Logout</span>
        </transition>
      </button>
      
      <div class="sidebar-version">v1.0.5</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  data() {
    return {
      isCollapsed: false,
      userName: 'Admin User',
      userRole: 'Administrator',
      analyticsMenuOpen: false,
      reportsMenuOpen: false
    }
  },
  computed: {
    userInitials() {
      if (!this.userName) return 'U';
      return this.userName
        .split(' ')
        .map(name => name.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
    },
    isAnalyticsActive() {
      return this.$route && this.$route.path.startsWith('/analytics');
    },
    isReportsActive() {
      return this.$route && this.$route.path.startsWith('/reports');
    }
  },
  watch: {
    '$route'(to) {
      // Auto expand menus based on current route
      if (to.path.startsWith('/analytics')) {
        this.analyticsMenuOpen = true;
      } else if (to.path.startsWith('/reports')) {
        this.reportsMenuOpen = true;
      }
    },
    isCollapsed(val) {
      // When expanding the sidebar, auto-open the active section
      if (!val) {
        if (this.isAnalyticsActive) {
          this.analyticsMenuOpen = true;
        }
        if (this.isReportsActive) {
          this.reportsMenuOpen = true;
        }
      }
    }
  },
  mounted() {
    // Check if there's a stored collapsed state
    const storedState = localStorage.getItem('sidebarCollapsed');
    if (storedState !== null) {
      this.isCollapsed = storedState === 'true';
    }
    
    // Get user info from localStorage
    const userStr = localStorage.getItem('user');
    if (userStr) {
      const user = JSON.parse(userStr);
      this.userName = user.name || 'User';
      this.userRole = user.type === 'admin' ? 'Administrator' : 'Employee';
    }
    
    // Add class to body based on sidebar state
    this.updateBodyClass();
    
    // Listen for window resize
    window.addEventListener('resize', this.handleResize);
    this.handleResize();
    
    // Set initial menu state based on route
    if (this.$route) {
      if (this.$route.path.startsWith('/analytics')) {
        this.analyticsMenuOpen = true;
      } else if (this.$route.path.startsWith('/reports')) {
        this.reportsMenuOpen = true;
      }
    }
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
    document.body.classList.remove('sidebar-collapsed', 'sidebar-expanded');
  },
  methods: {
    toggleCollapsed() {
      this.isCollapsed = !this.isCollapsed;
      localStorage.setItem('sidebarCollapsed', this.isCollapsed);
      this.updateBodyClass();
    },
    updateBodyClass() {
      if (this.isCollapsed) {
        document.body.classList.add('sidebar-collapsed');
        document.body.classList.remove('sidebar-expanded');
      } else {
        document.body.classList.add('sidebar-expanded');
        document.body.classList.remove('sidebar-collapsed');
      }
    },
    handleResize() {
      if (window.innerWidth <= 768 && !this.isCollapsed) {
        this.isCollapsed = true;
        localStorage.setItem('sidebarCollapsed', 'true');
        this.updateBodyClass();
      }
    },
    toggleAnalyticsMenu() {
      if (this.isCollapsed) {
        // If sidebar is collapsed, navigate to the main analytics page
        this.$router.push('/analytics');
      } else {
        this.analyticsMenuOpen = !this.analyticsMenuOpen;
        // Close the other menu if this one is opening
        if (this.analyticsMenuOpen) {
          this.reportsMenuOpen = false;
        }
      }
    },
    toggleReportsMenu() {
      if (this.isCollapsed) {
        // If sidebar is collapsed, navigate to the main reports page
        this.$router.push('/reports');
      } else {
        this.reportsMenuOpen = !this.reportsMenuOpen;
        // Close the other menu if this one is opening
        if (this.reportsMenuOpen) {
          this.analyticsMenuOpen = false;
        }
      }
    },
    logout() {
      localStorage.removeItem('user');
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
.sidebar-container {
  background: linear-gradient(180deg, #1a365d 0%, #2d4e7d 100%);
  color: white;
  width: var(--sidebar-width, 260px);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 100;
  overflow: hidden;
}

.sidebar-container.collapsed {
  --sidebar-width: 70px;
}

/* Header styling */
.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
}

.sidebar-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 10%;
  right: 10%;
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo-icon {
  font-size: 24px;
  margin-right: 14px;
  color: #60a5fa;
  filter: drop-shadow(0 0 8px rgba(96, 165, 250, 0.5));
}

.logo-icon.pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    filter: drop-shadow(0 0 4px rgba(96, 165, 250, 0.5));
  }
  50% {
    filter: drop-shadow(0 0 8px rgba(96, 165, 250, 0.8));
  }
  100% {
    filter: drop-shadow(0 0 4px rgba(96, 165, 250, 0.5));
  }
}

.logo-text {
  font-size: 1.4rem;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  background: linear-gradient(90deg, #ffffff, #e2e8f0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
}

.collapse-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  border: none;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.collapse-btn:hover {
  background-color: rgba(255, 255, 255, 0.15);
  transform: scale(1.05);
}

.collapse-btn:active {
  transform: scale(0.95);
}

/* User info styling */
.user-info {
  display: flex;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  transition: all 0.3s ease;
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3182ce, #4299e1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
  color: white;
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
  margin-right: 14px;
  transition: all 0.3s ease;
}

.collapsed .avatar-circle {
  margin-right: 0;
}

.user-details {
  flex: 1;
  overflow: hidden;
}

.user-name {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 4px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
    
  color: rgba(255, 255, 255, 0.95);
}

.user-status {
  display: flex;
  align-items: center;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #10b981; /* Green for online status */
  margin-right: 6px;
}

.user-role {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.65);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Navigation styling */
.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  overflow-y: auto;
}

.nav-section:not(:first-child) {
  margin-top: 10px;
}

.section-title {
  padding: 0 10px;
  font-size: 0.75rem;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 600;
  margin: 20px 0 12px 0;
  display: flex;
  align-items: center;
}

.section-line {
  display: inline-block;
  width: 18px;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.3);
  margin-right: 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 14px;
  border-radius: 10px;
  margin-bottom: 6px;
  color: rgba(255, 255, 255, 0.75);
  text-decoration: none;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.08);
  color: white;
}

.nav-item.active {
  background-color: rgba(59, 130, 246, 0.7);
  color: white;
  font-weight: 500;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.nav-item.active::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100%;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
  pointer-events: none;
}

.nav-icon-wrapper {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  margin-right: 12px;
  background-color: rgba(255, 255, 255, 0.05);
  transition: all 0.3s ease;
}

.nav-item:hover .nav-icon-wrapper {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-item.active .nav-icon-wrapper {
  background-color: rgba(255, 255, 255, 0.15);
}

.nav-icon {
  font-size: 16px;
  transition: transform 0.3s ease;
}

.nav-item:hover .nav-icon {
  transform: scale(1.1);
}

.nav-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.active-indicator {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background: transparent;
  transition: all 0.3s ease;
  border-radius: 0 2px 2px 0;
}

.nav-item.active .active-indicator {
  background: #ffffff;
  box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
}

.nav-divider {
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.15), transparent);
  margin: 10px 8px;
}

.sub-nav-item {
  padding-left: 24px;
  margin-left: 10px;
  border-left: 1px solid rgba(255, 255, 255, 0.1);
  background-color: rgba(255, 255, 255, 0.01);
  margin-bottom: 4px;
  font-size: 0.9rem;
}

.sub-nav-item .nav-icon-wrapper {
  width: 32px;
  height: 32px;
}

.sub-nav-item .nav-text {
  font-size: 0.85rem;
}

.sub-nav-item.active {
  background-color: rgba(59, 130, 246, 0.5);
}

/* Footer styling */
.sidebar-footer {
  padding: 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #e53e3e, #c53030);
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
  overflow: hidden;
  position: relative;
}

.logout-btn::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 50%;
  background: linear-gradient(to bottom, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
}

.logout-btn i {
  margin-right: 8px;
  font-size: 16px;
}

.sidebar-container.collapsed .logout-btn i {
  margin-right: 0;
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.logout-btn:active {
  transform: translateY(1px);
}

.sidebar-version {
  text-align: center;
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.75rem;
  margin-top: 6px;
}

/* For collapsed state */
.sidebar-container.collapsed .logo-text,
.sidebar-container.collapsed .section-title,
.sidebar-container.collapsed .nav-text {
  display: none;
}

.sidebar-container.collapsed .nav-item {
  justify-content: center;
  padding: 12px 4px;
}

.sidebar-container.collapsed .nav-icon-wrapper {
  margin-right: 0;
}

/* Improved scrollbar */
.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 10px;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.25);
}

/* Transitions and animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}

.collapse-enter-active,
.collapse-leave-active {
  transition: max-height 0.3s cubic-bezier(0, 1, 0, 1), opacity 0.3s ease;
  overflow: hidden;
}

.collapse-enter-from,
.collapse-leave-to {
  max-height: 0 !important;
  opacity: 0;
}

/* Rotating chevron animation */
.nav-chevron {
  transition: transform 0.3s ease;
}

.fa-chevron-down {
  transform: rotate(0deg);
}

.fa-chevron-right {
  transform: rotate(-90deg);
}

/* Hover effects for nav group headers */
.nav-group-header:hover {
  background-color: rgba(255, 255, 255, 0.12);
}

/* Active nav group header styling */
.nav-group-header.active-group {
  background-color: rgba(59, 130, 246, 0.4);
}

/* Responsive behavior */
@media screen and (max-width: 768px) {
  .sidebar-container {
    transform: translateX(calc(-1 * var(--sidebar-width)));
    box-shadow: none;
  }
  
  .sidebar-container.collapsed {
    transform: translateX(0);
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
  }
}

/* New collapsible navigation styles */
.nav-group {
  margin-bottom: 4px;
}

.nav-group-header {
  cursor: pointer;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.nav-chevron {
  position: absolute;
  right: 14px;
  font-size: 11px;
  color: rgba(255, 255, 255, 0.6);
  transition: transform 0.3s ease;
}

.nav-group-header:hover .nav-chevron {
  color: white;
}

.nav-group-items {
  overflow: hidden;
  max-height: 500px; /* Adjust as needed */
  transition: max-height 0.3s ease, opacity 0.3s ease;
}

/* Active section styling */
.nav-group-header.active {
  background-color: rgba(59, 130, 246, 0.4);
}

.sidebar-container.collapsed .nav-group-header.active {
  background-color: rgba(59, 130, 246, 0.7);
}

/* Override for collapsed state */
.sidebar-container.collapsed .nav-group-header {
  justify-content: center;
}

.sidebar-container.collapsed .nav-group-items {
  position: absolute;
  left: 70px;
  min-width: 200px;
  background: linear-gradient(180deg, #1a365d 0%, #2d4e7d 100%);
  border-radius: 0 10px 10px 0;
  padding: 8px;
  box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
  z-index: 101;
  margin-top: -40px;
}

.sidebar-container.collapsed .nav-group-items .sub-nav-item {
  margin-left: 0;
  padding-left: 14px;
}

.sidebar-container.collapsed .nav-group-items::before {
  content: '';
  position: absolute;
  left: -8px;
  top: 40px;
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
  border-right: 8px solid #1a365d;
}
</style>