<template>
  <div class="flex">
    <Sidebar />
    <div class="ml-64 p-6 w-full" style="margin-top:-60px;">
      <!-- Error Alert -->
      <div v-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ error }}</p>
      </div>
      
      <!-- Success Notification -->
      <div v-if="successMessage" 
           class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 animate-fade-in" 
           role="alert">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <p>{{ successMessage }}</p>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-3xl font-bold mb-4">Company Information</h2>
        <form @submit.prevent="openCompanyModal" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
              <input type="text" v-model="settings.companyName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
              <input type="email" v-model="settings.contactEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
              <input type="text" v-model="settings.phoneNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
              <input type="text" v-model="settings.address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          
          <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
              Save Company Info
            </button>
          </div>
        </form>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Rental Settings</h2>
        <form @submit.prevent="openRentalModal" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Default Rental Period (days)</label>
              <input type="number" v-model="settings.defaultRentalPeriod" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Late Fee (% per day)</label>
              <input type="number" step="0.01" v-model="settings.lateFeePercent" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Rental Amount ($)</label>
              <input type="number" step="0.01" v-model="settings.minimumRentalAmount" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Security Deposit (% of rental)</label>
              <input type="number" step="0.01" v-model="settings.securityDepositPercent" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          
          <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
              Save Rental Settings
            </button>
          </div>
        </form>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Categories</h2>
        <div class="mb-4">
          <div class="flex items-center mb-2">
            <input type="text" v-model="newCategory" placeholder="Add new category" class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button @click="addCategory" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md">
              Add
            </button>
          </div>
        </div>
        
        <div v-if="loading" class="flex justify-center p-4">
          <div class="spinner"></div>
        </div>
        
        <div v-else class="space-y-2">
          <div v-for="category in settings.categories" :key="category.id" class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
            <span>{{ category.name }}</span>
            <button @click="confirmDeleteCategory(category)" class="text-red-600 hover:text-red-800">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Company Info Confirmation Modal -->
    <div v-if="showCompanyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h3 class="text-xl font-medium text-gray-900">Confirm Company Information</h3>
          <button @click="showCompanyModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="mb-4">
            <p class="text-sm text-gray-500">
              Are you sure you want to save the following company information?
            </p>
            <div class="mt-4 bg-gray-50 p-4 rounded-md">
              <p><strong>Company Name:</strong> {{ settings.companyName }}</p>
              <p><strong>Contact Email:</strong> {{ settings.contactEmail }}</p>
              <p><strong>Phone Number:</strong> {{ settings.phoneNumber }}</p>
              <p><strong>Address:</strong> {{ settings.address }}</p>
            </div>
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="showCompanyModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Cancel
            </button>
            <button @click="saveCompanyInfo" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Rental Settings Confirmation Modal -->
    <div v-if="showRentalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h3 class="text-xl font-medium text-gray-900">Confirm Rental Settings</h3>
          <button @click="showRentalModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="mb-4">
            <p class="text-sm text-gray-500">
              Are you sure you want to save the following rental settings?
            </p>
            <div class="mt-4 bg-gray-50 p-4 rounded-md">
              <p><strong>Default Rental Period:</strong> {{ settings.defaultRentalPeriod }} days</p>
              <p><strong>Late Fee:</strong> {{ settings.lateFeePercent }}% per day</p>
              <p><strong>Minimum Rental Amount:</strong> ${{ settings.minimumRentalAmount }}</p>
              <p><strong>Security Deposit:</strong> {{ settings.securityDepositPercent }}% of rental</p>
            </div>
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="showRentalModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Cancel
            </button>
            <button @click="saveRentalSettings" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Delete Category Confirmation Modal -->
    <div v-if="categoryToDelete" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h3 class="text-xl font-medium text-gray-900">Delete Category</h3>
          <button @click="categoryToDelete = null" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="mb-4">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete the category <strong>{{ categoryToDelete?.name }}</strong>? This action cannot be undone.
            </p>
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="categoryToDelete = null" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
              Cancel
            </button>
            <button @click="removeCategory" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Loading Spinner Overlay -->
    <div v-if="saving" class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center">
      <div class="bg-white p-5 rounded-lg shadow-lg flex items-center">
        <div class="spinner-border mr-3"></div>
        <span>Saving changes...</span>
      </div>
    </div>
    
  </div>
</template>

<script>
import Sidebar from '../../components/Sidebar.vue';
import settingsService from '../../services/settingsService';

export default {
  name: 'Settings',
  components: {
    Sidebar
  },
  data() {
    return {
      newCategory: '',
      settings: {
        companyName: '',
        contactEmail: '',
        phoneNumber: '',
        address: '',
        defaultRentalPeriod: 7,
        lateFeePercent: 5,
        minimumRentalAmount: 25,
        securityDepositPercent: 20,
        categories: []
      },
      loading: true,
      saving: false,
      error: null,
      successMessage: null,
      showCompanyModal: false,
      showRentalModal: false,
      categoryToDelete: null
    };
  },
  mounted() {
    this.fetchSettings();
  },
  methods: {
    // Show a success message that automatically disappears after a delay
    showSuccessMessage(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = null;
      }, 3000);
    },
    
    async fetchSettings() {
      this.loading = true;
      try {
        const response = await settingsService.getSettings();
        this.settings = response.data;
        this.error = null;
      } catch (error) {
        console.error('Error fetching settings:', error);
        this.error = 'Failed to load settings. Please try again later.';
        // Use default settings as fallback
        this.settings = {
          companyName: 'Track New Services',
          contactEmail: 'info@tracknew.com',
          phoneNumber: '(555) 123-4567',
          address: '123 Main Street, City, State 12345',
          defaultRentalPeriod: 7,
          lateFeePercent: 5,
          minimumRentalAmount: 25,
          securityDepositPercent: 20,
          categories: []
        };
      }
      
      // Fetch categories
      try {
        const categoriesResponse = await settingsService.getCategories();
        this.settings.categories = categoriesResponse.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
        this.error = 'Failed to load categories. Please try again later.';
        // Use default categories as fallback
        this.settings.categories = [
          { id: 1, name: 'Power Tools' },
          { id: 2, name: 'Construction' },
          { id: 3, name: 'Landscaping' },
          { id: 4, name: 'Plumbing' },
          { id: 5, name: 'Electrical' }
        ];
      } finally {
        this.loading = false;
      }
    },
    
    openCompanyModal() {
      this.showCompanyModal = true;
    },
    
    openRentalModal() {
      this.showRentalModal = true;
    },
    
    async saveCompanyInfo() {
      this.saving = true;
      try {
        const companyInfo = {
          companyName: this.settings.companyName,
          contactEmail: this.settings.contactEmail,
          phoneNumber: this.settings.phoneNumber,
          address: this.settings.address
        };
        
        await settingsService.updateCompanyInfo(companyInfo);
        this.showCompanyModal = false;
        this.error = null;
        this.showSuccessMessage('Company information saved successfully!');
      } catch (error) {
        console.error('Error saving company info:', error);
        this.error = 'Failed to save company information. Please try again.';
      } finally {
        this.saving = false;
      }
    },
    
    async saveRentalSettings() {
      this.saving = true;
      try {
        const rentalSettings = {
          defaultRentalPeriod: this.settings.defaultRentalPeriod,
          lateFeePercent: this.settings.lateFeePercent,
          minimumRentalAmount: this.settings.minimumRentalAmount,
          securityDepositPercent: this.settings.securityDepositPercent
        };
        
        await settingsService.updateRentalSettings(rentalSettings);
        this.showRentalModal = false;
        this.error = null;
        this.showSuccessMessage('Rental settings saved successfully!');
      } catch (error) {
        console.error('Error saving rental settings:', error);
        this.error = 'Failed to save rental settings. Please try again.';
      } finally {
        this.saving = false;
      }
    },
    
    async addCategory() {
      if (!this.newCategory.trim()) {
        return;
      }
      
      this.saving = true;
      try {
        const response = await settingsService.addCategory(this.newCategory);
        const newCategory = response.data;
        this.settings.categories.push(newCategory);
        this.newCategory = '';
        this.error = null;
        this.showSuccessMessage('Category added successfully!');
      } catch (error) {
        console.error('Error adding category:', error);
        this.error = 'Failed to add category. Please try again.';
      } finally {
        this.saving = false;
      }
    },
    
    confirmDeleteCategory(category) {
      this.categoryToDelete = category;
    },
    
    async removeCategory() {
      if (!this.categoryToDelete) return;
      
      this.saving = true;
      try {
        console.log('Deleting category with ID:', this.categoryToDelete.id);
        await settingsService.deleteCategory(this.categoryToDelete.id);
        
        // Remove the deleted category from the local array
        this.settings.categories = this.settings.categories.filter(
          cat => cat.id !== this.categoryToDelete.id
        );
        
        this.error = null;
        this.showSuccessMessage('Category deleted successfully!');
      } catch (error) {
        console.error('Error deleting category:', error);
        
        // Detailed error handling
        if (error.response) {
          const statusCode = error.response.status;
          const errorData = error.response.data;
          
          console.error('Status code:', statusCode);
          console.error('Error data:', errorData);
          
          // Handle specific error cases
          if (statusCode === 409) {
            // Conflict - Can't delete because of relationships
            this.error = errorData.message || 'Cannot delete this category because it is being used by other items.';
          } else if (statusCode === 404) {
            // Category not found
            this.error = 'Category not found. It may have been already deleted.';
            // Remove from local array anyway since it doesn't exist on the server
            this.settings.categories = this.settings.categories.filter(
              cat => cat.id !== this.categoryToDelete.id
            );
          } else if (errorData && errorData.message) {
            this.error = errorData.message;
          } else {
            this.error = `Failed to delete category. Server error (${statusCode}).`;
          }
        } else if (error.request) {
          // No response received
          console.error('No response received:', error.request);
          this.error = 'Network error. Please check your connection and try again.';
        } else {
          this.error = `Failed to delete category: ${error.message}`;
        }
      } finally {
        this.categoryToDelete = null;
        this.saving = false;
      }
    }
  }
};
</script>

<style scoped>
.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 3px solid #3498db;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}

.spinner-border {
  display: inline-block;
  width: 2rem;
  height: 2rem;
  vertical-align: text-bottom;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spin 0.75s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>