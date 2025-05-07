import api from './api';

const rentalService = {
  // Get all rentals with optional filtering/pagination params
  getRentals(params = {}) {
    return api.get('/rentals', { params });
  },

  // Get a specific rental by ID
  getRental(id) {
    return api.get(`/rentals/${id}`);
  },

  // Create a new rental
  createRental(rentalData) {
    return api.post('/rentals', rentalData);
  },

  // Update an existing rental
  updateRental(id, rentalData) {
    return api.put(`/rentals/${id}`, rentalData);
  },

  // Process a rental return
  returnRental(id) {
    return api.post(`/rentals/${id}/return`);
  },

  // Delete a rental
  deleteRental(id) {
    return api.delete(`/rentals/${id}`);
  }
};

export default rentalService;