import axios from 'axios';

// Create axios instance with base URL from environment variables
const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// For debugging
console.log('API Base URL:', process.env.VUE_APP_API_URL);

// Request interceptor for API calls
api.interceptors.request.use(
  config => {
    // Log the full URL for debugging
    console.log('Making API request to:', config.baseURL + config.url);
    
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Response interceptor for API calls
api.interceptors.response.use(
  response => response,
  error => {
    const originalRequest = error.config;
    
    // Handle authentication errors
    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      // Redirect to login or refresh token logic can be added here
      localStorage.removeItem('auth_token');
    }
    
    return Promise.reject(error);
  }
);

export default api;