import api from './api';

const inventoryService = {
  // Get all inventory items with optional filtering/pagination params
  getInventoryItems(params = {}) {
    return api.get('/inventory', { params });
  },

  // Get a specific inventory item by ID
  getInventoryItem(id) {
    return api.get(`/inventory/${id}`);
  },

  // Create a new inventory item
  createInventoryItem(inventoryData) {
    return api.post('/inventory', inventoryData);
  },

  // Update an existing inventory item
  updateInventoryItem(id, inventoryData) {
    return api.put(`/inventory/${id}`, inventoryData);
  },

  // Delete an inventory item
  deleteInventoryItem(id) {
    return api.delete(`/inventory/${id}`);
  },

  // Get employee-specific inventory items (if different from general inventory)
  getEmployeeInventoryItems(params = {}) {
    return api.get('/employee-inventory', { params });
  },
  
  // Check out an inventory item
  checkoutItem(id, checkoutData) {
    return api.post(`/inventory/${id}/checkout`, checkoutData);
  },
  
  // Check in a returned inventory item
  checkinItem(id, checkinData) {
    return api.post(`/inventory/${id}/checkin`, checkinData);
  }
};

export default inventoryService;