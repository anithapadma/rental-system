import api from './api';

const settingsService = {
  /**
   * Get all settings
   * @returns {Promise} - Promise with settings data
   */
  getSettings() {
    return api.get('/settings');
  },

  /**
   * Update company information
   * @param {Object} companyInfo - Company information data
   * @returns {Promise} - Promise with updated company info
   */
  updateCompanyInfo(companyInfo) {
    return api.put('/settings/company', companyInfo);
  },

  /**
   * Update rental settings
   * @param {Object} rentalSettings - Rental settings data
   * @returns {Promise} - Promise with updated rental settings
   */
  updateRentalSettings(rentalSettings) {
    return api.put('/settings/rental', rentalSettings);
  },

  /**
   * Get all categories
   * @returns {Promise} - Promise with categories data
   */
  getCategories() {
    return api.get('/settings/categories');
  },

  /**
   * Add a new category
   * @param {String} category - Category name
   * @returns {Promise} - Promise with new category data
   */
  addCategory(category) {
    return api.post('/settings/categories', { name: category });
  },

  /**
   * Delete a category
   * @param {Number} id - Category ID
   * @returns {Promise} - Promise with deletion confirmation
   */
  deleteCategory(id) {
    return api.delete(`/settings/categories/${id}`);
  }
};

export default settingsService;