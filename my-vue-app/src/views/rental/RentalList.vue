<template>
  <div class="flex">
    <Sidebar />
    <div class="ml-64 p-6 w-full" style="    margin-top: -60px;">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Rental List</h1>
        <div class="flex gap-4">
          <button @click="fetchPageData" class="btn-refresh flex items-center gap-2 transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button @click="showNewRentalModal = true" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            + Add New Rental
          </button>
        </div>
      </div>
      
      <!-- Error Alert -->
      <div v-if="error" class="alert alert-error mb-6">
        <p>{{ error.message || 'An error occurred while loading data' }}</p>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-500 hover:shadow-lg">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
          <div class="relative">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search rentals..." 
              @input="handleSearch"
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow duration-300"
            />
            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
              <label class="text-sm text-gray-600 mr-2">Status:</label>
              <select 
                v-model="statusFilter"
                @change="applyFilters"
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                <option value="all">All Rentals</option>
                <option value="Active">Active</option>
                <option value="Overdue">Overdue</option>
                <option value="Returned">Returned</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
            
            <div class="flex items-center">
              <label class="text-sm text-gray-600 mr-2">Sort by:</label>
              <select 
                v-model="sortOption" 
                @change="applyFilters"
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                <option value="dateDesc">Date (Newest first)</option>
                <option value="dateAsc">Date (Oldest first)</option>
                <option value="customerAsc">Customer (A-Z)</option>
                <option value="customerDesc">Customer (Z-A)</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Rental ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Items
                </th>
                <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Start Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Due Date
                </th> -->
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="isLoading">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  Loading rental data...
                </td>
              </tr>
              <tr v-else-if="paginatedRentals.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  No rental records found matching your criteria.
                </td>
              </tr>
              <tr v-for="rental in paginatedRentals" :key="rental.id" class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ rental.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ rental.customer }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ rental.items }}
                </td>
                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(rental.startDate) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(rental.dueDate) }}
                </td> -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(rental.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ rental.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="viewRental(rental)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">View</button>
                  <button @click="returnRental(rental.id)" v-if="rental.status === 'Active' || rental.status === 'Overdue'" 
                    class="text-green-600 hover:text-green-900 transition-colors duration-200">Return</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mt-6 space-y-4 md:space-y-0">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ pagination.startItem }}</span> to 
            <span class="font-medium">{{ pagination.endItem }}</span> of 
            <span class="font-medium">{{ totalRentals }}</span> rentals
          </div>
          <div class="flex space-x-2">
            <button 
              @click="prevPage" 
              :disabled="pagination.currentPage === 1"
              :class="{'opacity-50 cursor-not-allowed': pagination.currentPage === 1}"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50 transition-colors duration-200">
              Previous
            </button>
            <button 
              @click="nextPage" 
              :disabled="pagination.currentPage >= pagination.totalPages"
              :class="{'opacity-50 cursor-not-allowed': pagination.currentPage >= pagination.totalPages}"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50 transition-colors duration-200">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- View Rental Modal -->
    <div v-if="viewingRental" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h2 class="text-xl font-bold text-gray-800">Rental Details</h2>
          <button @click="viewingRental = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-600">Rental ID</div>
              <div class="font-medium">{{ viewingRental?.id }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Customer</div>
              <div class="font-medium">{{ viewingRental?.customer }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Start Date</div>
              <div class="font-medium">{{ formatDate(viewingRental?.startDate) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Due Date</div>
              <div class="font-medium">{{ formatDate(viewingRental?.dueDate) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Status</div>
              <div>
                <span :class="getStatusClass(viewingRental?.status)"
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ viewingRental?.status }}
                </span>
              </div>
            </div>
            <div>
              <div class="text-sm text-gray-600">Daily Rate</div>
              <div class="font-medium">${{ viewingRental?.dailyRate || '40' }}</div>
            </div>
          </div>
          
          <div class="mb-6">
            <div class="text-sm text-gray-600 mb-2">Rented Items</div>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="font-medium">{{ viewingRental?.items }}</div>
            </div>
          </div>
          
          <div class="mb-6">
            <div class="text-sm text-gray-600 mb-2">Notes</div>
            <div class="bg-gray-50 p-4 rounded-md">
              <div>{{ viewingRental?.notes || 'No additional notes' }}</div>
            </div>
          </div>
          
          <div class="border-t border-gray-200 pt-4 mt-6 flex justify-end">
            <button @click="viewingRental = null" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none mr-2">
              Close
            </button>
            <button v-if="viewingRental?.status === 'Active' || viewingRental?.status === 'Overdue'" 
              @click="returnRental(viewingRental?.id); viewingRental = null" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none">
              Process Return
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Add New Rental Modal -->
    <div v-if="showNewRentalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h2 class="text-xl font-bold text-gray-800">Add New Rental</h2>
          <button @click="showNewRentalModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <form @submit.prevent="addNewRental" class="p-6">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
            <input 
              v-model="newRental.customer" 
              type="text" 
              required
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter customer name"
            >
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
              <input 
                v-model="newRental.startDate" 
                type="date" 
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
              <input 
                v-model="newRental.dueDate" 
                type="date" 
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Rented Items</label>
            <input 
              v-model="newRental.items" 
              type="text" 
              required
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter items being rented"
            >
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
            <textarea 
              v-model="newRental.notes" 
              rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter any additional notes"
            ></textarea>
          </div>
          
          <div class="flex justify-end">
            <button 
              type="button"
              @click="showNewRentalModal = false" 
              class="mr-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="isCreating"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
              {{ isCreating ? 'Creating...' : 'Add Rental' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" :message="loadingMessage" />
  </div>
</template>

<script>
import Sidebar from '../../components/Sidebar.vue';
import LoadingSpinner from '../../components/LoadingSpinner.vue';
import pageFunctionality from '../../mixins/pageFunctionality';
import rentalService from '../../services/rentalService';

export default {
  name: 'RentalList',
  components: {
    Sidebar,
    LoadingSpinner
  },
  mixins: [pageFunctionality],
  data() {
    return {
      rentals: [],
      filteredRentals: [], // Will store filtered results
      totalRentals: 0,
      searchQuery: '',
      statusFilter: 'all',
      sortOption: 'dateDesc',
      pagination: {
        currentPage: 1,
        itemsPerPage: 5,
        totalPages: 1,
        startItem: 1,
        endItem: 1
      },
      viewingRental: null, // Currently selected rental for viewing details
      showNewRentalModal: false,
      newRental: {
        customer: '',
        startDate: new Date().toISOString().split('T')[0],
        dueDate: '',
        items: '',
        notes: '',
        status: 'Active'
      },
      isCreating: false,
      serverSidePagination: true // Set to true if your API supports pagination
    };
  },
  watch: {
    // Re-calculate pagination when filtered rentals change
    filteredRentals() {
      this.calculatePagination();
    }
  },
  computed: {
    // Get paginated rentals for current page (for client-side pagination)
    paginatedRentals() {
      if (this.serverSidePagination) {
        return this.rentals;
      }
      
      const start = (this.pagination.currentPage - 1) * this.pagination.itemsPerPage;
      const end = start + this.pagination.itemsPerPage;
      return this.filteredRentals.slice(start, end);
    }
  },
  created() {
    this.fetchPageData();
  },
  methods: {
    getStatusClass(status) {
      switch (status) {
        case 'Active':
          return 'bg-blue-100 text-blue-800';
        case 'Returned':
          return 'bg-green-100 text-green-800';
        case 'Overdue':
          return 'bg-red-100 text-red-800';
        case 'Maintenance':
          return 'bg-yellow-100 text-yellow-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    },
    async fetchPageData() {
      this.startLoading("Loading rental data...");
      try {
        if (this.serverSidePagination) {
          const params = {
            page: this.pagination.currentPage,
            per_page: this.pagination.itemsPerPage,
            search: this.searchQuery,
            status: this.statusFilter !== 'all' ? this.statusFilter : undefined,
            sort: this.getSortParam()
          };
          const response = await rentalService.getRentals(params);
          this.rentals = response.data || [];
          this.totalRentals = response.meta?.total || this.rentals.length;
          this.pagination.currentPage = response.meta?.current_page || 1;
          this.pagination.totalPages = Math.ceil(this.totalRentals / (response.meta?.per_page || this.pagination.itemsPerPage));
          this.calculatePaginationDisplay();
        } else {
          const response = await rentalService.getRentals();
          this.rentals = response.data || [];
          this.totalRentals = this.rentals.length;
          this.applyFilters();
        }
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    async addNewRental() {
      try {
        this.isCreating = true;
        const response = await rentalService.createRental({
          customer: this.newRental.customer,
          start_date: this.newRental.startDate,
          due_date: this.newRental.dueDate,
          items: this.newRental.items,
          notes: this.newRental.notes,
          status: 'Active'
        });
        this.showSuccess(`New rental created successfully: ${response.data?.id || 'ID pending'}`, 'Rental Created');
        this.newRental = {
          customer: '',
          startDate: new Date().toISOString().split('T')[0],
          dueDate: '',
          items: '',
          notes: '',
          status: 'Active'
        };
        this.showNewRentalModal = false;
        this.fetchPageData();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.isCreating = false;
      }
    },
    async returnRental(id) {
      try {
        this.startLoading(`Processing return for rental ${id}...`);
        await rentalService.returnRental(id);
        this.showSuccess(`Rental ${id} has been marked as returned`, 'Rental Returned');
        await this.fetchPageData();
        if (this.viewingRental && this.viewingRental.id === id) {
          this.viewingRental = null;
        }
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    async viewRental(rental) {
      try {
        this.startLoading(`Loading details for rental ${rental.id}...`);
        const detailedRental = await rentalService.getRental(rental.id);
        this.viewingRental = detailedRental.data || rental;
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    handleSearch() {
      if (this.serverSidePagination) {
        // For server-side search, reset to page 1 and fetch new data
        this.pagination.currentPage = 1;
        this.fetchPageData();
      } else {
        // For client-side search
        this.pagination.currentPage = 1;
        this.applyFilters();
      }
    },
    applyFilters() {
      if (this.serverSidePagination) {
        // For server-side filtering, fetch new data
        this.fetchPageData();
      } else {
        // For client-side filtering
        let result = [...this.rentals];
        
        // Apply search query filter
        if (this.searchQuery.trim() !== '') {
          const query = this.searchQuery.toLowerCase();
          result = result.filter(rental => 
            rental.id.toLowerCase().includes(query) ||
            rental.customer.toLowerCase().includes(query) ||
            rental.items.toLowerCase().includes(query)
          );
        }
        
        // Apply status filter
        if (this.statusFilter !== 'all') {
          result = result.filter(rental => rental.status === this.statusFilter);
        }
        
        // Apply sorting
        result = this.sortRentals(result);
        
        // Store filtered results
        this.filteredRentals = result;
        this.totalRentals = result.length;
        
        // Calculate pagination
        this.calculatePagination();
      }
    },
    sortRentals(rentals) {
      switch (this.sortOption) {
        case 'dateAsc':
          return [...rentals].sort((a, b) => new Date(a.startDate) - new Date(b.startDate));
        case 'dateDesc':
          return [...rentals].sort((a, b) => new Date(b.startDate) - new Date(a.startDate));
        case 'customerAsc':
          return [...rentals].sort((a, b) => a.customer.localeCompare(b.customer));
        case 'customerDesc':
          return [...rentals].sort((a, b) => b.customer.localeCompare(a.customer));
        default:
          return rentals;
      }
    },
    getSortParam() {
      // Convert front-end sort option to API parameter
      switch (this.sortOption) {
        case 'dateAsc':
          return 'start_date,asc';
        case 'dateDesc':
          return 'start_date,desc';
        case 'customerAsc':
          return 'customer,asc';
        case 'customerDesc':
          return 'customer,desc';
        default:
          return 'start_date,desc';
      }
    },
    calculatePagination() {
      if (this.serverSidePagination) {
        // For server-side pagination, pagination is calculated when data is fetched
        return;
      }
      
      // For client-side pagination
      this.pagination.totalPages = Math.ceil(this.filteredRentals.length / this.pagination.itemsPerPage) || 1;
      
      if (this.pagination.currentPage > this.pagination.totalPages) {
        this.pagination.currentPage = 1;
      }
      
      this.calculatePaginationDisplay();
    },
    calculatePaginationDisplay() {
      // Calculate the display range for both server and client side pagination
      const start = (this.pagination.currentPage - 1) * this.pagination.itemsPerPage;
      const end = Math.min(start + this.pagination.itemsPerPage, this.totalRentals);
      
      this.pagination.startItem = this.totalRentals ? start + 1 : 0;
      this.pagination.endItem = end;
    },
    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
        
        if (this.serverSidePagination) {
          this.fetchPageData();
        } else {
          this.calculatePaginationDisplay();
        }
      }
    },
    nextPage() {
      if (this.pagination.currentPage < this.pagination.totalPages) {
        this.pagination.currentPage++;
        
        if (this.serverSidePagination) {
          this.fetchPageData();
        } else {
          this.calculatePaginationDisplay();
        }
      }
    },
    refreshData() {
      // Reset filters
      this.searchQuery = '';
      this.statusFilter = 'all';
      this.sortOption = 'dateDesc';
      this.pagination.currentPage = 1;
      
      // Fetch fresh data
      this.fetchPageData();
    }
  }
};
</script>

<style scoped>
.animate-modal-in {
  animation: modalIn 0.3s ease-out forwards;
}

@keyframes modalIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>