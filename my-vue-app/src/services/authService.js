/* eslint-disable no-useless-catch */
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

// Create an axios instance with default config
const apiClient = axios.create({
  baseURL: API_URL,
  withCredentials: true, // Required for working with cookies
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add request interceptor to attach the auth token to every request
apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Authentication service methods
class AuthService {
  // Login user
  async login(credentials) {
    const response = await apiClient.post('/login', credentials);
    if (response.data.access_token) {
      localStorage.setItem('token', response.data.access_token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  }

  // Register user
  async register(userData) {
    const response = await apiClient.post('/register', userData);
    if (response.data.access_token) {
      localStorage.setItem('token', response.data.access_token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    return response.data;
  }

  // Logout user
  async logout() {
    try {
      await apiClient.post('/logout');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    } catch (error) {
      console.error('Logout failed', error);
      // Even if logout API call fails, we clear local storage
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      // Re-throw error for handling in component
      throw error;
    }
  }

  // Get current authenticated user
  getCurrentUser() {
    return JSON.parse(localStorage.getItem('user'));
  }

  // Check if user is authenticated
  isAuthenticated() {
    return localStorage.getItem('token') !== null;
  }
}

export default new AuthService();