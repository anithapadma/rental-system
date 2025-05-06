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
  
  if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
    // Redirect to login if not logged in
    next('/')
  } else if (to.matched.some(record => record.meta.adminOnly) && userRole !== 'admin') {
    // Redirect employees to employee dashboard if trying to access admin routes
    if (userRole === 'employee') {
      next('/employee/dashboard')
    } else {
      next('/')
    }
  } else if (to.matched.some(record => record.meta.employeeOnly) && userRole !== 'employee') {
    // Redirect admins to admin dashboard if trying to access employee routes
    if (userRole === 'admin') {
      next('/dashboard')
    } else {
      next('/')
    }
  } else if (to.path === '/' && isLoggedIn) {
    // Redirect logged in users to their appropriate dashboard
    if (userRole === 'admin') {
      next('/dashboard')
    } else if (userRole === 'employee') {
      next('/employee/dashboard')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router