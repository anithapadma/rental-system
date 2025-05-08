<template>
  <div class="modal-overlay" v-if="show" @click.self="closeModal">
    <div class="modal-container">
      <div class="modal-header">
        <h2>{{ item.name }}</h2>
        <button class="close-button" @click="closeModal">&times;</button>
      </div>
      <div class="modal-content">
        <div class="item-image">
          <img :src="getCategoryImage(item)" :alt="item.name">
          <span class="status-badge" :class="item.status?.toLowerCase()">{{ item.status }}</span>
        </div>
        
        <div class="item-details">
          <div class="detail-row">
            <span class="detail-label">ID:</span>
            <span class="detail-value">{{ item.id }}</span>
          </div>
          
          <div class="detail-row">
            <span class="detail-label">Category:</span>
            <span class="detail-value">{{ item.category }}</span>
          </div>
          
          <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value" :class="item.status?.toLowerCase()">{{ item.status }}</span>
          </div>
          
          <div class="detail-row">
            <span class="detail-label">Rate:</span>
            <span class="detail-value">${{ item.rate }}/day</span>
          </div>
          
          <div class="detail-row" v-if="item.expected_return_date && item.status?.toLowerCase() === 'rented'">
            <span class="detail-label">Expected Return:</span>
            <span class="detail-value">{{ formatDate(item.expected_return_date) }}</span>
          </div>
          
          <div class="detail-row" v-if="item.last_maintenance">
            <span class="detail-label">Last Maintenance:</span>
            <span class="detail-value">{{ formatDate(item.last_maintenance) }}</span>
          </div>
        </div>
        
        <div class="item-description">
          <h3>Description</h3>
          <p>{{ item.description || 'No description available for this item.' }}</p>
        </div>
        
        <div class="item-features" v-if="item.features && item.features.length > 0">
          <h3>Features</h3>
          <ul>
            <li v-for="(feature, index) in item.features" :key="index">{{ feature }}</li>
          </ul>
        </div>
        
        <div class="item-notes" v-if="item.notes">
          <h3>Notes</h3>
          <p>{{ item.notes }}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button 
          class="cancel-button" 
          @click="closeModal"
        >
          Close
        </button>
        <button 
          class="action-button" 
          :class="{ 'disabled': item.status?.toLowerCase() !== 'available' }"
          :disabled="item.status?.toLowerCase() !== 'available'"
          @click="checkout"
        >
          Check Out Item
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InventoryDetailModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    item: {
      type: Object,
      default: () => ({})
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    checkout() {
      if (this.item.status?.toLowerCase() === 'available') {
        this.$emit('checkout', this.item);
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
          return dateString;
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
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
}

.modal-header {
  padding: 15px 20px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  color: #1a202c;
  font-size: 1.5rem;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #64748b;
}

.close-button:hover {
  color: #0f172a;
}

.modal-content {
  padding: 20px;
}

.item-image {
  position: relative;
  height: 220px;
  border-radius: 6px;
  overflow: hidden;
  background: #f1f5f9;
  margin-bottom: 20px;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
}

.status-badge.available {
  background-color: #dcfce7;
  color: #166534;
}

.status-badge.rented {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-badge.maintenance {
  background-color: #fecaca;
  color: #991b1b;
}

.item-details {
  margin-bottom: 25px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
}

.detail-row {
  display: flex;
  padding: 10px 15px;
  border-bottom: 1px solid #e2e8f0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row:nth-child(odd) {
  background-color: #f8fafc;
}

.detail-label {
  width: 180px;
  font-weight: 600;
  color: #334155;
}

.detail-value {
  flex: 1;
  color: #1e293b;
}

.detail-value.available {
  color: #166534;
  font-weight: 600;
}

.detail-value.rented {
  color: #1e40af;
  font-weight: 600;
}

.detail-value.maintenance {
  color: #991b1b;
  font-weight: 600;
}

.item-description h3,
.item-features h3,
.item-notes h3 {
  font-size: 1.1rem;
  margin-top: 0;
  margin-bottom: 10px;
  color: #334155;
}

.item-description p,
.item-notes p {
  margin-top: 0;
  color: #475569;
  line-height: 1.6;
}

.item-features {
  margin-top: 20px;
}

.item-features ul {
  margin: 0;
  padding-left: 20px;
  color: #475569;
}

.item-features li {
  margin-bottom: 5px;
}

.item-notes {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.modal-footer {
  padding: 15px 20px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 15px;
}

.cancel-button,
.action-button {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
}

.cancel-button {
  background-color: transparent;
  border: 1px solid #e2e8f0;
  color: #64748b;
}

.cancel-button:hover {
  background-color: #f8fafc;
}

.action-button {
  background-color: #2563eb;
  border: none;
  color: white;
}

.action-button:hover:not(.disabled) {
  background-color: #1d4ed8;
}

.action-button.disabled {
  background-color: #94a3b8;
  cursor: not-allowed;
  opacity: 0.7;
}

@media (max-width: 640px) {
  .modal-container {
    width: 95%;
  }
  
  .detail-row {
    flex-direction: column;
  }
  
  .detail-label {
    width: 100%;
    margin-bottom: 5px;
  }
}
</style>