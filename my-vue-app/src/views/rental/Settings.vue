<template>
  <div class="flex">
    <Sidebar />
    <div class="ml-64 p-6 w-full" style="margin-top:-60px;">
      <!-- <h1 class="text-3xl font-bold mb-6">Settings</h1> -->
      
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-3xl font-bold mb-4">Company Information</h2>
        <form @submit.prevent="saveCompanyInfo" class="space-y-4">
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
        <form @submit.prevent="saveRentalSettings" class="space-y-4">
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
        
        <div class="space-y-2">
          <div v-for="(category, index) in settings.categories" :key="index" class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
            <span>{{ category }}</span>
            <button @click="removeCategory(index)" class="text-red-600 hover:text-red-800">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../../components/Sidebar.vue';

export default {
  name: 'Settings',
  components: {
    Sidebar
  },
  data() {
    return {
      newCategory: '',
      settings: {
        companyName: 'Track New Services',
        contactEmail: 'info@tracknew.com',
        phoneNumber: '(555) 123-4567',
        address: '123 Main Street, City, State 12345',
        defaultRentalPeriod: 7,
        lateFeePercent: 5,
        minimumRentalAmount: 25,
        securityDepositPercent: 20,
        categories: [
          'Power Tools',
          'Construction',
          'Landscaping',
          'Plumbing',
          'Electrical'
        ]
      }
    };
  },
  methods: {
    saveCompanyInfo() {
      // Logic to save company info would go here
      // For now, we'll just show an alert
      alert('Company information saved successfully!');
    },
    saveRentalSettings() {
      // Logic to save rental settings would go here
      // For now, we'll just show an alert
      alert('Rental settings saved successfully!');
    },
    addCategory() {
      if (this.newCategory.trim()) {
        this.settings.categories.push(this.newCategory);
        this.newCategory = '';
      }
    },
    removeCategory(index) {
      this.settings.categories.splice(index, 1);
    }
  }
};
</script>