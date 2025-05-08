<template>
  <employee-layout>
    <div class="employee-inventory">
      <div class="page-header">
        <h1>Inventory Management</h1>
        <p>View and check availability of rental items</p>
      </div>

      <div class="inventory-filters">
        <div class="filter-group">
          <label for="category-filter">Category:</label>
          <select id="category-filter" v-model="categoryFilter" @change="applyFilters">
            <option value="all">All Categories</option>
            <option value="tools">Tools</option>
            <option value="equipment">Heavy Equipment</option>
            <option value="electronics">Electronics</option>
            <option value="furniture">Furniture</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="status-filter">Status:</label>
          <select id="status-filter" v-model="statusFilter" @change="applyFilters">
            <option value="all">All</option>
            <option value="available">Available</option>
            <option value="rented">Rented</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
        <div class="search-group">
          <input 
            type="text" 
            placeholder="Search inventory..." 
            v-model="searchQuery"
            @input="handleSearchDebounce"
          >
        </div>
      </div>

      <!-- Loading spinner -->
      <div class="loading-container" v-if="isLoading">
        <div class="loading-spinner"></div>
        <p>Loading inventory data...</p>
      </div>

      <!-- Error message -->
      <div class="error-message" v-if="error">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ error }}</p>
        <button @click="fetchInventoryData" class="retry-button">
          <i class="fas fa-sync"></i> Retry
        </button>
      </div>

      <div v-if="!isLoading && !error" class="inventory-grid">
        <div class="inventory-card" v-for="(item, index) in inventory" :key="item.id">
          <div class="inventory-image" :class="item.status.toLowerCase()">
            <div class="status-badge">{{ item.status }}</div>
            <img :src="getCategoryImage(item)" alt="Item image">
          </div>
          <div class="inventory-details">
            <h3 class="inventory-name">{{ item.name }}</h3>
            <p class="inventory-id">ID: {{ item.id }}</p>
            <div class="inventory-meta">
              <span class="inventory-category">{{ item.category }}</span>
              <span class="inventory-rate">${{ item.rate }}/day</span>
            </div>
            <p class="inventory-description">{{ item.description }}</p>
            <div class="inventory-availability" v-if="item.status.toLowerCase() === 'rented'">
              <i class="fas fa-clock"></i>
              <span>Available from: {{ formatDate(item.expected_return_date || item.availableFrom) }}</span>
            </div>
            <div class="inventory-actions">
              <button 
                class="action-button" 
                :class="{ 'disabled': item.status.toLowerCase() !== 'available' }"
                @click="checkItem(index)"
              >
                Check Item
              </button>
              <button 
                class="action-button secondary" 
                @click="viewDetails(item)"
              >
                View Details
              </button>
            </div>
          </div>
        </div>

        <div class="no-items" v-if="inventory.length === 0">
          <i class="fas fa-box-open"></i>
          <p>No inventory items found with current filters</p>
        </div>
      </div>
      
      <!-- Pagination -->
      <div class="pagination" v-if="!isLoading && !error && totalPages > 1">
        <button 
          :disabled="currentPage === 1" 
          @click="changePage(currentPage - 1)" 
          class="pagination-button"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
        <button 
          :disabled="currentPage === totalPages" 
          @click="changePage(currentPage + 1)" 
          class="pagination-button"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      
      <!-- Item Detail Modal -->
      <inventory-detail-modal 
        :show="showModal" 
        :item="selectedItem"
        @close="closeModal"
        @checkout="checkoutFromModal"
      />
    </div>
  </employee-layout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'
import InventoryDetailModal from '../../components/modals/InventoryDetailModal.vue'
import inventoryService from '@/services/inventoryService'

export default {
  name: 'EmployeeInventory',
  components: {
    EmployeeLayout,
    InventoryDetailModal
  },
  data() {
    return {
      searchQuery: '',
      categoryFilter: 'all',
      statusFilter: 'all',
      inventory: [],
      isLoading: false,
      error: null,
      searchTimeout: null,
      currentPage: 1,
      totalPages: 1,
      itemsPerPage: 8,
      showModal: false,
      selectedItem: {}
    }
  },
  created() {
    this.fetchInventoryData();
  },
  methods: {
    async fetchInventoryData() {
      this.isLoading = true;
      this.error = null;
      
      try {
        // Prepare query parameters
        const params = {
          page: this.currentPage,
          per_page: this.itemsPerPage
        };
        
        // Add filters if selected
        if (this.categoryFilter !== 'all') {
          params.category = this.categoryFilter;
        }
        
        if (this.statusFilter !== 'all') {
          params.status = this.statusFilter;
        }
        
        if (this.searchQuery) {
          params.search = this.searchQuery;
        }
        
        // Fetch data from API
        const response = await inventoryService.getInventoryItems(params);
        
        // Update component data with API response
        this.inventory = response.data.data;
        
        // Update pagination data
        if (response.data.meta) {
          this.totalPages = response.data.meta.last_page;
          this.currentPage = response.data.meta.current_page;
        }
      } catch (err) {
        console.error('Failed to fetch inventory data:', err);
        this.error = 'Unable to load inventory data. Please try again later.';
      } finally {
        this.isLoading = false;
      }
    },
    
    handleSearchDebounce() {
      // Clear any existing timeout
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      // Set a new timeout to delay the API call
      this.searchTimeout = setTimeout(() => {
        this.currentPage = 1; // Reset to first page on new search
        this.fetchInventoryData();
      }, 500);
    },
    
    applyFilters() {
      this.currentPage = 1; // Reset to first page when filters change
      this.fetchInventoryData();
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        this.fetchInventoryData();
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Unknown';
      
      try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
          return dateString; // Return original string if not a valid date
        }
        
        return date.toLocaleDateString('en-US', {
          year: 'numeric', 
          month: 'long', 
          day: 'numeric'
        });
      } catch (error) {
        return dateString;
      }
    },
    
    getCategoryImage(item) {
      const category = item.category?.toLowerCase() || 'generic';
      const itemName = item.name ? encodeURIComponent(item.name) : '';
      
      // Define image URLs for specific categories
      const imageMap = {
        'tools': `https://source.unsplash.com/300x200/?tool,${itemName}`,
        'equipment': `https://source.unsplash.com/300x200/?equipment,${itemName}`,
        'electronics': `https://source.unsplash.com/300x200/?electronics,${itemName}`,
        'furniture': `https://source.unsplash.com/300x200/?furniture,${itemName}`
      };
      
      return imageMap[category] || `https://source.unsplash.com/300x200/?${category},${itemName}`;
    },
    
    checkItem(index) {
      const item = this.inventory[index];
      if (item.status.toLowerCase() === 'available') {
        // In a real app, this would open a check-out form or dialog
        alert(`Item ${item.name} (${item.id}) is being checked for rental.`);
      }
    },
    
    viewDetails(item) {
      this.selectedItem = item;
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
    },
    
    checkoutFromModal(item) {
      alert(`Item ${item.name} (${item.id}) is being checked for rental.`);
      this.closeModal();
    }
  }
}
</script>

<style scoped>
.employee-inventory {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 25px;
}

.page-header h1 {
  font-size: 2rem;
  color: #1a365d;
  margin-bottom: 8px;
}

.page-header p {
  color: #64748b;
  font-size: 1.1rem;
}

.inventory-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 25px;
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-group, .search-group {
  display: flex;
  align-items: center;
}

.filter-group label {
  margin-right: 8px;
  font-weight: 500;
  color: #334155;
}

.filter-group select, .search-group input {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.9rem;
}

.filter-group select {
  min-width: 150px;
}

.search-group {
  margin-left: auto;
}

.search-group input {
  min-width: 200px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px 0;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(37, 99, 235, 0.1);
  border-radius: 50%;
  border-top: 4px solid #2563eb;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 15px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  flex-direction: column;
  text-align: center;
  margin-bottom: 20px;
}

.error-message i {
  font-size: 2rem;
  margin-bottom: 10px;
}

.retry-button {
  margin-top: 10px;
  padding: 8px 15px;
  background-color: #991b1b;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.retry-button:hover {
  background-color: #7f1d1d;
}

.inventory-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
}

.inventory-card {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease;
}

.inventory-card:hover {
  transform: translateY(-3px);
}

.inventory-image {
  height: 180px;
  position: relative;
  overflow: hidden;
  background-color: #f1f5f9;
}

.inventory-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.inventory-image.rented {
  opacity: 0.7;
}

.inventory-image.maintenance {
  opacity: 0.5;
}

.status-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  z-index: 1;
}

.inventory-image.available .status-badge {
  background-color: #dcfce7;
  color: #166534;
}

.inventory-image.rented .status-badge {
  background-color: #dbeafe;
  color: #1e40af;
}

.inventory-image.maintenance .status-badge {
  background-color: #fecaca;
  color: #991b1b;
}

.inventory-details {
  padding: 15px;
}

.inventory-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin-top: 0;
  margin-bottom: 5px;
}

.inventory-id {
  color: #64748b;
  font-size: 0.85rem;
  margin-top: 0;
  margin-bottom: 8px;
}

.inventory-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.inventory-category {
  background-color: #f1f5f9;
  color: #334155;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  text-transform: capitalize;
}

.inventory-rate {
  font-weight: 600;
  color: #0f766e;
}

.inventory-description {
  font-size: 0.9rem;
  color: #4b5563;
  line-height: 1.5;
  margin-bottom: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.inventory-availability {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 15px;
}

.inventory-availability i {
  margin-right: 8px;
}

.inventory-actions {
  display: flex;
  gap: 10px;
}

.action-button {
  flex: 1;
  padding: 8px 10px;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  font-size: 0.85rem;
  text-align: center;
  background-color: #2563eb;
  color: white;
  transition: all 0.2s ease;
}

.action-button:hover {
  background-color: #1d4ed8;
}

.action-button.disabled {
  background-color: #94a3b8;
  cursor: not-allowed;
}

.action-button.secondary {
  background-color: transparent;
  color: #2563eb;
  border: 1px solid #2563eb;
}

.action-button.secondary:hover {
  background-color: #eff6ff;
}

.no-items {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px 0;
  color: #94a3b8;
}

.no-items i {
  font-size: 3rem;
  margin-bottom: 15px;
}

.no-items p {
  font-size: 1.1rem;
}

.pagination {
  margin-top: 25px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
}

.pagination-button {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: white;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  color: #64748b;
}

.pagination-button:hover:not(:disabled) {
  background-color: #f1f5f9;
  color: #0f172a;
}

.pagination-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.9rem;
  color: #64748b;
}

@media (max-width: 640px) {
  .inventory-filters {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-group {
    margin-left: 0;
    width: 100%;
  }
  
  .search-group input {
    width: 100%;
  }
  
  .inventory-actions {
    flex-direction: column;
  }
}
</style>