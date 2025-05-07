import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

export default {
  /**
   * Fetch all inventory items with optional filtering and pagination
   * @param {Object} params - Query parameters for filtering and pagination
   * @returns {Promise} - Promise with inventory items data
   */
  getInventoryItems(params = {}) {
    return axios.get(`${API_URL}/inventory`, { params });
  },
  
  /**
   * Fetch a single inventory item by ID
   * @param {string} id - The inventory item ID
   * @returns {Promise} - Promise with inventory item data
   */
  getInventoryItem(id) {
    return axios.get(`${API_URL}/inventory/${id}`);
  },
  
  /**
   * Create a new inventory item
   * @param {Object} itemData - The inventory item data
   * @returns {Promise} - Promise with created item data
   */
  createInventoryItem(itemData) {
    return axios.post(`${API_URL}/inventory`, itemData);
  },
  
  /**
   * Update an inventory item
   * @param {string} id - The inventory item ID
   * @param {Object} itemData - The updated inventory item data
   * @returns {Promise} - Promise with updated item data
   */
  updateInventoryItem(id, itemData) {
    return axios.put(`${API_URL}/inventory/${id}`, itemData);
  },
  
  /**
   * Update the status of an inventory item
   * @param {string} id - The inventory item ID
   * @param {string} status - The new status
   * @returns {Promise} - Promise with updated item data
   */
  updateItemStatus(id, status) {
    return axios.patch(`${API_URL}/inventory/${id}/status`, { status });
  },
  
  /**
   * Delete an inventory item
   * @param {string} id - The inventory item ID
   * @returns {Promise} - Promise with deletion confirmation
   */
  deleteInventoryItem(id) {
    return axios.delete(`${API_URL}/inventory/${id}`);
  }
}