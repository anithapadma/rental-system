import api from './api';

const agreementsService = {
  /**
   * Get all agreements with optional pagination
   * @param {Object} params - Pagination and filtering parameters
   * @returns {Promise} - Promise with agreements data
   */
  getAgreements(params = {}) {
    return api.get('/agreements', { params });
  },

  /**
   * Get a single agreement by ID
   * @param {String} id - Agreement ID
   * @returns {Promise} - Promise with agreement data
   */
  getAgreement(id) {
    return api.get(`/agreements/${id}`);
  },

  /**
   * Create a new agreement
   * @param {Object} agreementData - New agreement data
   * @returns {Promise} - Promise with created agreement
   */
  createAgreement(agreementData) {
    return api.post('/agreements', agreementData);
  },

  /**
   * Update an existing agreement
   * @param {String} id - Agreement ID
   * @param {Object} agreementData - Updated agreement data
   * @returns {Promise} - Promise with updated agreement
   */
  updateAgreement(id, agreementData) {
    return api.put(`/agreements/${id}`, agreementData);
  },

  /**
   * Update agreement status
   * @param {String} id - Agreement ID
   * @param {String} status - New status
   * @returns {Promise} - Promise with updated agreement
   */
  updateStatus(id, status) {
    return api.patch(`/agreements/${id}/status`, { status });
  },

  /**
   * Delete an agreement
   * @param {String} id - Agreement ID
   * @returns {Promise} - Promise with deletion confirmation
   */
  deleteAgreement(id) {
    return api.delete(`/agreements/${id}`);
  },

  /**
   * Generate PDF for an agreement
   * @param {String} id - Agreement ID
   * @returns {Promise} - Promise with PDF blob
   */
  generatePdf(id) {
    return api.get(`/agreements/${id}/pdf`, { 
      responseType: 'blob' 
    });
  }
};

export default agreementsService;