import { createRouter, createWebHistory } from 'vue-router'

// Import components
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import RentalList from '../views/rental/RentalList.vue'
import InventoryItems from '../views/rental/InventoryItems.vue'
import Agreements from '../views/rental/Agreements.vue'
import Settings from '../views/rental/Settings.vue'
import Analytics from '../views/analytics/Analytics.vue'
import Reports from '../views/analytics/Reports.vue'

// Import employee components
import EmployeeDashboard from '../views/employee/EmployeeDashboard.vue'
import EmployeeInventory from '../views/employee/EmployeeInventory.vue'
import EmployeeTasks from '../views/employee/EmployeeTasks.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/rental-list',
    name: 'RentalList',
    component: RentalList,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/inventory-items',
    name: 'InventoryItems',
    component: InventoryItems,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/agreements',
    name: 'Agreements',
    component: Agreements,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/analytics',
    name: 'Analytics',
    component: Analytics,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true, adminOnly: true }
  },
  // Employee routes
  {
    path: '/employee/dashboard',
    name: 'EmployeeDashboard',
    component: EmployeeDashboard,
    meta: { requiresAuth: true, employeeOnly: true }
  },
  {
    path: '/employee/inventory',
    name: 'EmployeeInventory',
    component: EmployeeInventory,
    meta: { requiresAuth: true, employeeOnly: true }
  },
  {
    path: '/employee/tasks',
    name: 'EmployeeTasks',
    component: EmployeeTasks,
    meta: { requiresAuth: true, employeeOnly: true }
  },
  {
    path: '/employee',
    component: () => import('../views/employee/EmployeeBase.vue'),
    meta: { requiresAuth: true, employeeOnly: true },
    children: [
      {
        path: 'profile/:id?',
        name: 'EmployeeProfile',
        component: () => import('../views/employee/EmployeeProfile.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

// Navigation guard for authentication and role-based access
router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem('user')
  const userData = isLoggedIn ? JSON.parse(isLoggedIn) : {}
  const userRole = userData.type || ''
  const userEmail = userData.email || ''
  
  // Special check for admin routes - only allow admin@example.com
  if (to.matched.some(record => record.meta.adminOnly)) {
    if (!isLoggedIn) {
      next('/')
    } else if (userRole !== 'admin' || userEmail !== 'admin@example.com') {
      // If not admin or not the correct email, redirect accordingly
      if (userRole === 'employee') {
        next('/employee/dashboard')
      } else {
        // Log out and redirect to login
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        next('/')
      }
    } else {
      next() // Allow admin@example.com to admin routes
    }
  }
  // Special check for employee routes - don't allow admin@example.com
  else if (to.matched.some(record => record.meta.employeeOnly)) {
    if (!isLoggedIn) {
      next('/')
    } else if (userEmail === 'admin@example.com' || userRole !== 'employee') {
      if (userRole === 'admin') {
        next('/dashboard')
      } else {
        // Log out and redirect to login
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        next('/')
      }
    } else {
      next() // Allow non-admin employees to employee routes
    }
  }
  // Regular authentication check for other routes
  else if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
    next('/')
  }
  // Auto-redirect logged-in users from login page
  else if (to.path === '/' && isLoggedIn) {
    if (userRole === 'admin') {
      next('/dashboard')
    } else if (userRole === 'employee') {
      next('/employee/dashboard')
    } else {
      next()
    }
  } 
  else {
    next()
  }
})

export default router