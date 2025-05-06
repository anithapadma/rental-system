export default {
  data() {
    return {
      isLoading: false,
      loadingMessage: '',
      refreshTimestamp: null,
      error: null,
      // Add pagination data that can be used by all components
      paginationDefaults: {
        currentPage: 1,
        itemsPerPage: 5,
        totalPages: 1,
        startItem: 1,
        endItem: 1
      }
    };
  },
  methods: {
    /**
     * Shows loading state with an optional message
     * @param {string} message - Loading message to display
     */
    startLoading(message = 'Loading...') {
      this.isLoading = true;
      this.loadingMessage = message;
    },

    /**
     * Hides loading state
     */
    stopLoading() {
      this.isLoading = false;
      this.loadingMessage = '';
    },

    /**
     * Handles any API errors
     * @param {Error} error - The error object
     * @param {boolean} notify - Whether to show notification
     */
    handleError(error, notify = true) {
      this.error = error;
      this.stopLoading();
      
      if (notify && this.$notifications) {
        this.$notifications.addNotification({
          type: 'error',
          title: 'Error',
          message: error.message || 'An error occurred. Please try again.'
        });
      }
      
      console.error(error);
    },

    /**
     * Refreshes page data
     * @param {boolean} showLoading - Whether to show loading state
     */
    async refreshData(showLoading = true) {
      if (showLoading) {
        this.startLoading('Refreshing data...');
      }
      
      this.error = null;
      this.refreshTimestamp = Date.now();
      
      try {
        // Each component will implement this method
        if (this.fetchPageData) {
          await this.fetchPageData();
        }
        
        if (this.$notifications) {
          this.$notifications.addNotification({
            type: 'success',
            title: 'Success',
            message: 'Data refreshed successfully'
          });
        }
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    
    /**
     * Format date to display format
     * @param {string} dateString - ISO date string
     * @returns {string} Formatted date string
     */
    formatDate(dateString) {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(date);
    },
    
    /**
     * Shows success notification
     * @param {string} message - Success message
     * @param {string} title - Success title
     */
    showSuccess(message, title = 'Success') {
      if (this.$notifications) {
        this.$notifications.addNotification({
          type: 'success',
          title,
          message
        });
      }
    },
    
    /**
     * Shows info notification
     * @param {string} message - Info message
     * @param {string} title - Info title
     */
    showInfo(message, title = 'Information') {
      if (this.$notifications) {
        this.$notifications.addNotification({
          type: 'info',
          title,
          message
        });
      }
    },
    
    /**
     * Shows warning notification
     * @param {string} message - Warning message
     * @param {string} title - Warning title
     */
    showWarning(message, title = 'Warning') {
      if (this.$notifications) {
        this.$notifications.addNotification({
          type: 'warning',
          title,
          message
        });
      }
    },

    /**
     * Initialize pagination for a component
     * @param {Array} items - The array of items to paginate
     * @param {Object} pagination - The pagination object to initialize
     * @param {number} itemsPerPage - Number of items per page
     */
    initializePagination(items, pagination, itemsPerPage = 5) {
      pagination.currentPage = 1;
      pagination.itemsPerPage = itemsPerPage;
      this.updatePagination(items, pagination);
      return pagination;
    },

    /**
     * Update pagination calculations based on filtered items
     * @param {Array} items - The array of items (filtered)
     * @param {Object} pagination - The pagination object to update
     */
    updatePagination(items, pagination) {
      pagination.totalPages = Math.ceil(items.length / pagination.itemsPerPage) || 1;
      
      if (pagination.currentPage > pagination.totalPages) {
        pagination.currentPage = 1;
      }
      
      const start = (pagination.currentPage - 1) * pagination.itemsPerPage;
      const end = Math.min(start + pagination.itemsPerPage, items.length);
      
      pagination.startItem = items.length ? start + 1 : 0;
      pagination.endItem = end;
      
      return pagination;
    },

    /**
     * Get the paginated subset of items for current page
     * @param {Array} items - All items (filtered)
     * @param {Object} pagination - The pagination object
     * @returns {Array} - Paginated subset of items
     */
    getPaginatedItems(items, pagination) {
      const start = (pagination.currentPage - 1) * pagination.itemsPerPage;
      const end = start + pagination.itemsPerPage;
      return items.slice(start, end);
    },

    /**
     * Go to previous page
     * @param {Array} items - All items (filtered)
     * @param {Object} pagination - The pagination object
     * @returns {Array} - Updated paginated items
     */
    prevPage(items, pagination) {
      if (pagination.currentPage > 1) {
        pagination.currentPage--;
        this.updatePagination(items, pagination);
        return this.getPaginatedItems(items, pagination);
      }
      return this.getPaginatedItems(items, pagination);
    },

    /**
     * Go to next page
     * @param {Array} items - All items (filtered)
     * @param {Object} pagination - The pagination object
     * @returns {Array} - Updated paginated items
     */
    nextPage(items, pagination) {
      if (pagination.currentPage < pagination.totalPages) {
        pagination.currentPage++;
        this.updatePagination(items, pagination);
        return this.getPaginatedItems(items, pagination);
      }
      return this.getPaginatedItems(items, pagination);
    },

    /**
     * Go to specific page
     * @param {Array} items - All items (filtered)
     * @param {Object} pagination - The pagination object
     * @param {number} pageNumber - Page number to go to
     * @returns {Array} - Updated paginated items
     */
    goToPage(items, pagination, pageNumber) {
      if (pageNumber >= 1 && pageNumber <= pagination.totalPages) {
        pagination.currentPage = pageNumber;
        this.updatePagination(items, pagination);
        return this.getPaginatedItems(items, pagination);
      }
      return this.getPaginatedItems(items, pagination);
    },

    /**
     * Download a file
     * @param {Blob} blob - The blob data to download
     * @param {string} filename - The filename to use
     */
    downloadFile(blob, filename) {
      const link = document.createElement('a');
      link.href = window.URL.createObjectURL(blob);
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
};