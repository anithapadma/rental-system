import axios from 'axios';

// Define base API URL - you should update this with your actual backend API URL
const API_URL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api';

class RentalService {
  /**
   * Get all rentals with optional pagination
   * 
   * @param {Object} params - Query parameters (page, per_page, status, sort, search)
   * @returns {Promise} - Promise with rental data
   */
  async getRentals(params = {}) {
    try {
      const response = await axios.get(`${API_URL}/rentals`, { 
        params,
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Get a single rental by ID
   * 
   * @param {string|number} id - Rental ID
   * @returns {Promise} - Promise with rental data
   */
  async getRental(id) {
    try {
      const response = await axios.get(`${API_URL}/rentals/${id}`, {
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Create a new rental
   * 
   * @param {Object} rentalData - New rental data
   * @returns {Promise} - Promise with created rental data
   */
  async createRental(rentalData) {
    try {
      const response = await axios.post(`${API_URL}/rentals`, rentalData, {
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Update a rental
   * 
   * @param {string|number} id - Rental ID to update
   * @param {Object} rentalData - Updated rental data
   * @returns {Promise} - Promise with updated rental data
   */
  async updateRental(id, rentalData) {
    try {
      const response = await axios.put(`${API_URL}/rentals/${id}`, rentalData, {
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Update rental status
   * 
   * @param {string|number} id - Rental ID
   * @param {string} status - New status (Active, Returned, Overdue, etc.)
   * @returns {Promise} - Promise with updated rental data
   */
  async updateRentalStatus(id, status) {
    try {
      const response = await axios.patch(`${API_URL}/rentals/${id}/status`, { status }, {
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Mark a rental as returned
   * 
   * @param {string|number} id - Rental ID
   * @returns {Promise} - Promise with updated rental data
   */
  async returnRental(id) {
    try {
      const response = await axios.post(`${API_URL}/rentals/${id}/return`, {}, {
        headers: this.getAuthHeader()
      });
      return response.data;
    } catch (error) {
      this.handleError(error);
      throw error;
    }
  }

  /**
   * Get authentication header with token
   * 
   * @returns {Object} - Headers object with authorization token
   */
  getAuthHeader() {
    const token = localStorage.getItem('token');
    return {
      'Authorization': token ? `Bearer ${token}` : '',
      'Content-Type': 'application/json'
    };
  }

  /**
   * Handle API errors
   * 
   * @param {Error} error - Error object
   */
  handleError(error) {
    console.error('API Error:', error.response || error);
    
    // You could add additional error handling logic here,
    // such as logging out if authentication fails
    if (error.response && error.response.status === 401) {
      // Handle unauthorized error
      console.log('Authentication error - consider redirecting to login');
      // window.location.href = '/login';
    }
  }
}

export default new RentalService();