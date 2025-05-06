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
          <select id="category-filter" v-model="categoryFilter">
            <option value="all">All Categories</option>
            <option value="tools">Tools</option>
            <option value="equipment">Heavy Equipment</option>
            <option value="electronics">Electronics</option>
            <option value="furniture">Furniture</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="status-filter">Status:</label>
          <select id="status-filter" v-model="statusFilter">
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
          >
        </div>
      </div>

      <div class="inventory-grid">
        <div class="inventory-card" v-for="(item, index) in filteredInventory" :key="index">
          <div class="inventory-image" :class="item.status">
            <div class="status-badge">{{ item.status }}</div>
            <img :src="getImagePlaceholder(item.category)" alt="Item image">
          </div>
          <div class="inventory-details">
            <h3 class="inventory-name">{{ item.name }}</h3>
            <p class="inventory-id">ID: {{ item.id }}</p>
            <div class="inventory-meta">
              <span class="inventory-category">{{ item.category }}</span>
              <span class="inventory-rate">${{ item.rate }}/day</span>
            </div>
            <p class="inventory-description">{{ item.description }}</p>
            <div class="inventory-availability" v-if="item.status === 'rented'">
              <i class="fas fa-clock"></i>
              <span>Available from: {{ item.availableFrom }}</span>
            </div>
            <div class="inventory-actions">
              <button 
                class="action-button" 
                :class="{ 'disabled': item.status !== 'available' }"
                @click="checkItem(index)"
              >
                Check Item
              </button>
              <button 
                class="action-button secondary" 
                @click="viewDetails(index)"
              >
                View Details
              </button>
            </div>
          </div>
        </div>

        <div class="no-items" v-if="filteredInventory.length === 0">
          <i class="fas fa-box-open"></i>
          <p>No inventory items found with current filters</p>
        </div>
      </div>
    </div>
  </employee-layout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'

export default {
  name: 'EmployeeInventory',
  components: {
    EmployeeLayout
  },
  data() {
    return {
      searchQuery: '',
      categoryFilter: 'all',
      statusFilter: 'all',
      inventory: [
        {
          id: 'T-1001',
          name: 'Power Drill',
          category: 'tools',
          description: 'Professional grade power drill with variable speed and multiple attachments.',
          status: 'available',
          rate: 15,
          availableFrom: null
        },
        {
          id: 'T-1002',
          name: 'Circular Saw',
          category: 'tools',
          description: '7-1/4" circular saw with laser guide, 15-amp motor.',
          status: 'rented',
          rate: 20,
          availableFrom: 'May 12, 2025'
        },
        {
          id: 'E-2001',
          name: 'Mini Excavator',
          category: 'equipment',
          description: 'Compact excavator perfect for small to medium digging projects.',
          status: 'available',
          rate: 150,
          availableFrom: null
        },
        {
          id: 'E-2002',
          name: 'Portable Generator',
          category: 'equipment',
          description: '5000W portable generator with multiple outlets and low noise operation.',
          status: 'maintenance',
          rate: 75,
          availableFrom: 'May 15, 2025'
        },
        {
          id: 'EL-3001',
          name: 'Professional DSLR Camera',
          category: 'electronics',
          description: 'High-end DSLR camera with 24.2MP sensor and 4K video capabilities.',
          status: 'available',
          rate: 45,
          availableFrom: null
        },
        {
          id: 'EL-3002',
          name: 'PA System',
          category: 'electronics',
          description: 'Complete PA system with speakers, mixer, and microphones for events.',
          status: 'rented',
          rate: 85,
          availableFrom: 'May 8, 2025'
        },
        {
          id: 'F-4001',
          name: 'Folding Tables (set of 5)',
          category: 'furniture',
          description: '6ft rectangular folding tables, perfect for events.',
          status: 'available',
          rate: 50,
          availableFrom: null
        },
        {
          id: 'F-4002',
          name: 'Stacking Chairs (set of 20)',
          category: 'furniture',
          description: 'Comfortable padded stacking chairs for events and gatherings.',
          status: 'rented',
          rate: 40,
          availableFrom: 'May 9, 2025'
        }
      ]
    }
  },
  computed: {
    filteredInventory() {
      return this.inventory.filter(item => {
        // Category filter
        if (this.categoryFilter !== 'all' && item.category !== this.categoryFilter) {
          return false;
        }
        
        // Status filter
        if (this.statusFilter !== 'all' && item.status !== this.statusFilter) {
          return false;
        }
        
        // Search query
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          return item.name.toLowerCase().includes(query) || 
                 item.description.toLowerCase().includes(query) ||
                 item.id.toLowerCase().includes(query);
        }
        
        return true;
      });
    }
  },
  methods: {
    getImagePlaceholder(category) {
      // In a real app, you would use actual images
      // This is just a placeholder function
      return 'https://via.placeholder.com/300x200?text='+category;
    },
    checkItem(index) {
      const item = this.inventory[index];
      if (item.status === 'available') {
        // In a real app, this would open a check-out form or dialog
        alert(`Item ${item.name} (${item.id}) is being checked for rental.`);
      }
    },
    viewDetails(index) {
      // In a real app, this would navigate to a detailed view or open a modal
      const item = this.inventory[index];
      alert(`Viewing details for ${item.name} (${item.id})`);
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