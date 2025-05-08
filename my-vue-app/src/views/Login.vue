<template>
  <div class="login-container">
    <div class="background-overlay"></div>
    
    <!-- Logo and company name with animation -->
    <div class="brand-container animate-fade-in">
      <h1 class="company-name">Track New</h1>
      <p class="company-tagline">Professional IT Services</p>
    </div>
    
    <!-- Login card with animation - removed negative margin -->
    <div class="login-card animate-slide-up" style="margin-top:-95px;">
      <div class="login-header">
        <h2>Welcome Back</h2>
        <p>Sign in to your account</p>
      </div>
      
      <!-- Login type selector -->
      <div class="login-type-selector">
        <button 
          @click="loginType = 'admin'" 
          :class="{ active: loginType === 'admin' }"
          class="login-type-btn"
        >
          <i class="fas fa-user-shield"></i> Admin
        </button>
        <button 
          @click="loginType = 'employee'" 
          :class="{ active: loginType === 'employee' }"
          class="login-type-btn"
        >
          <i class="fas fa-user"></i> Employee
        </button>
      </div>
      
      <!-- Login form with animation -->
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">
            <i class="fas fa-envelope"></i> Email
          </label>
          <input 
            type="email" 
            id="email" 
            v-model="credentials.email" 
            placeholder="Enter your email"
            required
            class="form-input"
          />
        </div>
        
        <div class="form-group">
          <label for="password">
            <i class="fas fa-lock"></i> Password
          </label>
          <input 
            type="password" 
            id="password" 
            v-model="credentials.password" 
            placeholder="Enter your password"
            required
            class="form-input"
          />
        </div>
        
        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" v-model="rememberMe"> Remember me
          </label>
          <a href="#" class="forgot-password">Forgot password?</a>
        </div>
        
        <button 
          type="submit" 
          class="login-btn"
          :disabled="isLoading"
        >
          <span v-if="!isLoading">Sign In</span>
          <div v-else class="btn-spinner"></div>
        </button>
      </form>
      
      <!-- Error message with animation -->
      <transition name="fade">
        <div v-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i> {{ error }}
        </div>
      </transition>
    </div>
    
    <!-- Footer -->
    <div class="login-footer">
      <p>&copy; 2025 Track New. All rights reserved.</p>
    </div>
    
    <!-- Loading overlay -->
    <LoadingSpinner :isVisible="isLoading" message="Authenticating..." />
  </div>
</template>

<script>
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import authService from '@/services/authService';

export default {
  name: 'Login',
  components: {
    LoadingSpinner
  },
  data() {
    return {
      credentials: {
        email: '',
        password: ''
      },
      loginType: 'admin',
      error: '',
      isLoading: false,
      rememberMe: false,
      loginAnimationComplete: false
    }
  },
  mounted() {
    // Add animation class to body
    document.body.classList.add('login-page');
    
    // Allow scrolling if needed - removed the overflow: hidden
    
    // Simulate any initial loading if needed
    setTimeout(() => {
      this.loginAnimationComplete = true;
    }, 500);
  },
  beforeUnmount() {
    // Remove class from body when component is destroyed
    document.body.classList.remove('login-page');
  },
  methods: {
    async handleLogin() {
      this.error = '';
      this.isLoading = true;
      
      try {
        // Special validation for admin login attempts
        if (this.loginType === 'admin' && this.credentials.email !== 'admin@example.com') {
          this.error = 'This email is not authorized for admin access.';
          this.isLoading = false;
          return;
        }
        
        // Special validation for employee login attempts
        if (this.loginType === 'employee' && this.credentials.email === 'admin@example.com') {
          this.error = 'Admin account cannot be used for employee login.';
          this.isLoading = false;
          return;
        }
        
        // Add user type to credentials
        this.credentials.type = this.loginType;
        
        // Call the authentication service
        await authService.login(this.credentials);
        
        // Get user data
        const user = authService.getCurrentUser();
        
        // Ensure user type is set properly
        if (user) {
          // If user doesn't have a type from the backend, use the selected login type
          if (!user.type) {
            user.type = this.loginType;
            // Update user in localStorage with the type
            localStorage.setItem('user', JSON.stringify(user));
          }
          
          // Additional security check - ensure only admin@example.com can be admin
          if (user.type === 'admin' && this.credentials.email !== 'admin@example.com') {
            this.error = 'Unauthorized admin access attempt.';
            authService.logout();
            this.isLoading = false;
            return;
          }
          
          // Prevent admin account from accessing employee routes
          if (user.type === 'employee' && this.credentials.email === 'admin@example.com') {
            this.error = 'Admin account cannot access employee section.';
            authService.logout();
            this.isLoading = false;
            return;
          }
          
          console.log('Login successful, user:', user);
          console.log('User type:', user.type);
          
          // Force a small delay to ensure localStorage is updated
          setTimeout(() => {
            // Redirect based on user type
            if (user.type === 'admin') {
              console.log('Redirecting to /dashboard');
              this.$router.push({ path: '/dashboard', replace: true });
            } else if (user.type === 'employee') {
              console.log('Redirecting to /employee/dashboard');
              this.$router.push({ path: '/employee/dashboard', replace: true });
            } else {
              console.log('Redirecting to default dashboard');
              this.$router.push({ path: '/dashboard', replace: true });
            }
          }, 100);
        } else {
          this.error = 'User information not available.';
          this.isLoading = false;
        }
      } catch (error) {
        if (error.response) {
          // Handle different response errors
          if (error.response.status === 422) {
            this.error = error.response.data.message || 'Validation failed. Please check your input.';
          } else if (error.response.status === 401) {
            this.error = 'Invalid email or password.';
          } else {
            this.error = 'Login failed. Please try again.';
          }
        } else {
          this.error = 'Network error. Please check your connection.';
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
/* Base styling */
.login-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  background-image: url('https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  color: #333;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  padding: 20px;
  overflow-x: hidden;
  overflow-y: hidden;
  
}

/* Background overlay with gradient */
.background-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.65);
  z-index: 1;
}

/* Brand container styling */
.brand-container {
  margin-bottom: 20px;
  text-align: center;
  z-index: 2;
}

.company-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 5px;
  text-shadow: 0px 2px 4px rgba(0, 0, 0, 0.5);
}

.company-tagline {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
  margin-top: 0;
  text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Login card styling */
.login-card {
  background-color: rgba(255, 255, 255, 0.95);
  border-radius: 12px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
  width: 400px;
  max-width: 90%;
  padding: 25px;
  position: relative;
  z-index: 2;
  backdrop-filter: blur(5px);
  margin-bottom: 20px;
}

.login-header {
  text-align: center;
  margin-bottom: 20px;
}

.login-header h2 {
  font-size: 1.6rem;
  color: #1a365d;
  margin-bottom: 8px;
}

.login-header p {
  color: #64748b;
  margin-top: 0;
}

/* Login type selector styling */
.login-type-selector {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 15px;
}

.login-type-btn {
  padding: 8px 14px;
  margin: 0 5px;
  border: none;
  background: none;
  font-size: 0.95rem;
  font-weight: 500;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-type-btn:hover {
  background-color: rgba(241, 245, 249, 0.8);
}

.login-type-btn.active {
  background-color: #1a365d;
  color: white;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

/* Form styling */
.login-form {
  margin-bottom: 15px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 6px;
  color: #4b5563;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background-color: rgba(255, 255, 255, 0.9);
}

.form-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  outline: none;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  font-size: 0.85rem;
}

.remember-me {
  display: flex;
  align-items: center;
  color: #64748b;
}

.remember-me input {
  margin-right: 6px;
}

.forgot-password {
  color: #1a365d;
  text-decoration: none;
  transition: all 0.2s;
}

.forgot-password:hover {
  text-decoration: underline;
}

/* Button styling */
.login-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 10px;
  background: linear-gradient(90deg, #1a365d 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  height: 42px;
}

.login-btn:hover {
  background: linear-gradient(90deg, #15294b 0%, #1d4ed8 100%);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  transform: translateY(-2px);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: translateY(0);
}

/* Button loading spinner */
.btn-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

/* Error message styling */
.error-message {
  margin-top: 16px;
  padding: 8px 12px;
  background-color: rgba(254, 226, 226, 0.9);
  color: #b91c1c;
  border-radius: 6px;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
}

.error-message i {
  margin-right: 8px;
}

/* Footer styling */
.login-footer {
  margin-top: 20px;
  margin-bottom: 10px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.8rem;
  text-align: center;
  z-index: 2;
  text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Animations */
.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
}

.animate-slide-up {
  animation: slideUp 0.8s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Transition for Vue transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Media query for smaller screens */
@media (max-height: 700px) {
  .brand-container {
    margin-bottom: 15px;
  }
  
  .company-name {
    font-size: 2rem;
  }
  
  .company-tagline {
    font-size: 0.9rem;
  }
  
  .login-card {
    padding: 20px;
  }
  
  .login-footer {
    margin-top: 15px;
  }
}
</style>