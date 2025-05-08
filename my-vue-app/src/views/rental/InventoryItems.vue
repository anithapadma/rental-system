<template>
  <div class="flex">
    <Sidebar />
    <div class="ml-64 p-6 w-full" style="    margin-top: -60px;">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-bold">Inventory Items</h1>
        <div class="flex gap-4">
          <button @click="refreshData" class="btn-refresh flex items-center gap-2 transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button @click="showAddItemModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            + Add New Item
          </button>
        </div>
      </div>
      
      <!-- Error Alert -->
      <div v-if="error" class="alert alert-error mb-6">
        <p>{{ error.message || 'An error occurred while loading data' }}</p>
      </div>
      
      <!-- Inventory Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-fade-in">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Available Items</p>
              <p class="text-2xl font-bold text-gray-800">{{ stockCounts.available }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">Ready for rental</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Rented Items</p>
              <p class="text-2xl font-bold text-gray-800">{{ stockCounts.rented }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">Currently with customers</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Maintenance</p>
              <p class="text-2xl font-bold text-gray-800">{{ stockCounts.maintenance }}</p>
            </div>
            <div class="rounded-full bg-yellow-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">Under repair or maintenance</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Total Inventory</p>
              <p class="text-2xl font-bold text-gray-800">{{ inventoryItems.length }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">All items in inventory</p>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-500 hover:shadow-lg">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
          <div class="relative">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search inventory..." 
              @input="handleSearch"
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow duration-300"
            />
            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
              <label class="text-sm text-gray-600 mr-2">Category:</label>
              <select 
                v-model="categoryFilter"
                @change="applyFilters"
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                <option value="all">All Categories</option>
                <option value="Power Tools">Power Tools</option>
                <option value="Construction">Construction</option>
                <option value="Landscaping">Landscaping</option>
                <option value="Plumbing">Plumbing</option>
              </select>
            </div>
            
            <div class="flex items-center">
              <label class="text-sm text-gray-600 mr-2">Status:</label>
              <select 
                v-model="statusFilter"
                @change="applyFilters"
                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                <option value="all">All Status</option>
                <option value="Available">Available</option>
                <option value="Rented">Rented</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>

            <!-- Adding view toggle button after the filters -->
            <div class="flex items-center ml-4">
              <label class="text-sm text-gray-600 mr-2">View:</label>
              <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                <input 
                  type="checkbox" 
                  name="toggle" 
                  id="toggle" 
                  v-model="showTableView"
                  class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                />
                <label 
                  for="toggle" 
                  class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                ></label>
              </div>
              <span class="text-sm text-gray-700">{{ showTableView ? 'Table' : 'Card' }}</span>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <!-- Card Layout for Items -->
          <div v-if="!showTableView && paginatedItems.length === 0" class="text-center py-8 text-gray-500">
            No inventory items found matching your criteria.
          </div>
          
          <div v-if="!showTableView && paginatedItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-4">
            <div 
              v-for="item in paginatedItems" 
              :key="item.id" 
              class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 cursor-pointer hover:scale-[1.02]"
              @click="viewItemDetails(item)">
              <div class="h-24 bg-gray-50 flex items-center justify-center relative">
                <span class="text-gray-300 text-6xl font-light">{{ item.name[0] }}</span>
                <span :class="getStatusClass(item.status)" class="absolute top-2 right-2 px-2 py-1 text-xs rounded-full font-medium">
                  {{ item.status }}
                </span>
              </div>
              <div class="p-3">
                <h3 class="font-medium text-base mb-1">{{ item.name }}</h3>
                <p class="text-gray-600 text-xs mb-1">{{ item.category }}</p>
                <div class="flex justify-between items-center">
                  <span class="font-bold text-blue-700">${{ item.rate }}/day</span>
                  <button 
                    @click.stop="viewItemDetails(item)" 
                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                    View Details
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Table View for Items -->
          <div v-if="showTableView" class="mt-8 border-t border-gray-200 pt-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-800">Inventory Items Table</h3>
              <div class="flex gap-2">
                <button 
                  @click="exportToCSV" 
                  class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                  <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                  </svg>
                  Export
                </button>
                <button 
                  @click="printTable" 
                  class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                  <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                  </svg>
                  Print
                </button>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 border">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ID
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Category
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Daily Rate
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acquisition Date
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in paginatedItems" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                      {{ item.id }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-gray-500 text-sm font-medium">{{ item.name[0] }}</span>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                      {{ item.category }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span :class="getStatusClass(item.status)" class="px-2 py-1 text-xs rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                      ${{ item.rate }}/day
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                      {{ item.acquisitionDate || 'Not specified' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <button @click.stop="viewItemDetails(item)" class="text-blue-600 hover:text-blue-900">
                          View
                        </button>
                        <button v-if="item.status === 'Available'" class="text-green-600 hover:text-green-900">
                          Rent
                        </button>
                        <button v-if="item.status === 'Rented'" class="text-purple-600 hover:text-purple-900">
                          Return
                        </button>
                        <button v-if="item.status !== 'Maintenance'" class="text-yellow-600 hover:text-yellow-900">
                          Maintain
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mt-6 space-y-4 md:space-y-0">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ pagination.startItem }}</span> to 
            <span class="font-medium">{{ pagination.endItem }}</span> of 
            <span class="font-medium">{{ filteredItems.length }}</span> items
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
    
    <!-- View Item Details Modal -->
    <div v-if="viewingItem" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h2 class="text-xl font-bold text-gray-800">Item Details</h2>
          <button @click="viewingItem = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="flex flex-col md:flex-row gap-6 mb-6">
            <div class="md:w-1/3 bg-gray-100 rounded-lg flex items-center justify-center h-48">
              <span class="text-gray-400 text-9xl font-light">{{ viewingItem?.name[0] }}</span>
            </div>
            <div class="md:w-2/3">
              <div class="flex justify-between items-start mb-4">
                <h3 class="text-2xl font-bold text-gray-800">{{ viewingItem?.name }}</h3>
                <span :class="getStatusClass(viewingItem?.status)" class="px-2 py-1 text-xs rounded-full font-medium">
                  {{ viewingItem?.status }}
                </span>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <div class="text-sm text-gray-600">Item ID</div>
                  <div class="font-medium">{{ viewingItem?.id }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-600">Category</div>
                  <div class="font-medium">{{ viewingItem?.category }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-600">Daily Rate</div>
                  <div class="font-medium">${{ viewingItem?.rate }}/day</div>
                </div>
                <div>
                  <div class="text-sm text-gray-600">Acquisition Date</div>
                  <div class="font-medium">{{ viewingItem?.acquisitionDate || 'Jan 12, 2025' }}</div>
                </div>
              </div>
              
              <div class="mb-4">
                <div class="text-sm text-gray-600 mb-1">Description</div>
                <div class="text-gray-800">{{ viewingItem?.description || 'Professional grade equipment suitable for both home and commercial use.' }}</div>
              </div>
              
              <div class="flex flex-wrap gap-2">
                <div 
                  v-for="(feature, index) in viewingItem?.features || ['Durable', 'Heavy Duty', 'Easy to Use']" 
                  :key="index" 
                  class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                  {{ feature }}
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="viewingItem?.rentalHistory?.length" class="mb-6">
            <h4 class="font-semibold mb-2 text-gray-700">Rental History</h4>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                  <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="record in viewingItem.rentalHistory" :key="record.id">
                  <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ record.date }}</td>
                  <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ record.customer }}</td>
                  <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ record.duration }}</td>
                  <td class="px-4 py-2 whitespace-nowrap">
                    <span :class="getStatusClass(record.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ record.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="viewingItem?.maintenanceRecords?.length" class="mb-6">
            <h4 class="font-semibold mb-2 text-gray-700">Maintenance Records</h4>
            <div v-for="record in viewingItem.maintenanceRecords" :key="record.id" class="bg-gray-50 p-3 rounded-md mb-2">
              <div class="flex justify-between">
                <span class="text-sm font-medium">{{ record.date }}</span>
                <span class="text-sm text-gray-600">{{ record.type }}</span>
              </div>
              <p class="text-sm mt-1">{{ record.description }}</p>
            </div>
          </div>
          
          <div class="border-t border-gray-200 pt-4 mt-4 flex justify-end gap-2">
            <button @click="viewingItem = null" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
              Close
            </button>
            <button 
              v-if="viewingItem?.status === 'Available'" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
              Rent This Item
            </button>
            <button 
              v-if="viewingItem?.status === 'Rented'" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none">
              Process Return
            </button>
            <button 
              v-if="viewingItem?.status === 'Maintenance'" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none">
              Mark as Available
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Add New Item Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-3 py-2">
          <h2 class="text-lg font-bold text-gray-800">Add New Item</h2>
          <button @click="closeAddModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-3">
          <form @submit.prevent="addNewItem">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Item Name</label>
                <input 
                  v-model="newItem.name" 
                  type="text" 
                  required
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter item name"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Category</label>
                <select 
                  v-model="newItem.category" 
                  required
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="Power Tools">Power Tools</option>
                  <option value="Construction">Construction</option>
                  <option value="Landscaping">Landscaping</option>
                  <option value="Plumbing">Plumbing</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Status</label>
                <select 
                  v-model="newItem.status" 
                  required
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="Available">Available</option>
                  <option value="Maintenance">Maintenance</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Daily Rate ($)</label>
                <input 
                  v-model.number="newItem.rate" 
                  type="number" 
                  min="0"
                  required
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="0.00"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Acquisition Date</label>
                <input 
                  v-model="newItem.acquisitionDate" 
                  type="date" 
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Description</label>
                <textarea 
                  v-model="newItem.description" 
                  rows="1"
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter item description"
                ></textarea>
              </div>
              
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-0.5">Features</label>
                <input 
                  v-model="featuresInput" 
                  type="text" 
                  class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g. Durable, Heavy Duty, Easy to Use"
                />
                <p class="mt-0.5 text-xs text-gray-500">Separate features with commas</p>
              </div>
            </div>
            
            <div class="mt-4 flex justify-end gap-2">
              <button 
                type="button"
                @click="closeAddModal" 
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                Cancel
              </button>
              <button 
                type="submit" 
                class="px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                Add Item
              </button>
            </div>
          </form>
        </div>
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
import inventoryService from '../../services/inventoryService';

export default {
  name: 'InventoryItems',
  components: {
    Sidebar,
    LoadingSpinner
  },
  mixins: [pageFunctionality],
  data() {
    return {
      inventoryItems: [],
      searchQuery: '',
      categoryFilter: 'all',
      statusFilter: 'all',
      filteredItems: [],
      paginatedItems: [],
      pagination: {
        currentPage: 1,
        itemsPerPage: 8,
        totalPages: 1,
        startItem: 1,
        endItem: 1
      },
      error: null,
      viewingItem: null,
      showAddModal: false,
      newItem: {
        name: '',
        category: '',
        status: 'Available',
        rate: 0,
        acquisitionDate: '',
        description: '',
        features: []
      },
      featuresInput: '',
      showTableView: false,
      serverSidePagination: true // Use server-side pagination by default
    };
  },
  computed: {
    stockCounts() {
      return {
        available: this.inventoryItems.filter(item => item.status === 'Available').length,
        rented: this.inventoryItems.filter(item => item.status === 'Rented').length,
        maintenance: this.inventoryItems.filter(item => item.status === 'Maintenance').length
      };
    }
  },
  watch: {
    filteredItems() {
      if (!this.serverSidePagination) {
        this.updatePaginatedItems();
      }
    },
    'pagination.currentPage'() {
      if (this.serverSidePagination) {
        this.fetchPageData();
      } else {
        this.updatePaginatedItems();
      }
    }
  },
  mounted() {
    this.fetchPageData();
  },
  methods: {
    getStatusClass(status) {
      switch (status) {
        case 'Available':
          return 'bg-green-100 text-green-800';
        case 'Rented':
          return 'bg-blue-100 text-blue-800';
        case 'Maintenance':
          return 'bg-yellow-100 text-yellow-800';
        case 'Completed':
          return 'bg-gray-100 text-gray-800';
        case 'Active':
          return 'bg-blue-100 text-blue-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    },
    async fetchPageData() {
      this.startLoading("Loading inventory items...");
      
      try {
        // Prepare parameters for API request based on filter and pagination settings
        const params = {
          page: this.pagination.currentPage,
          per_page: this.pagination.itemsPerPage
        };

        // Add search query if any
        if (this.searchQuery.trim() !== '') {
          params.search = this.searchQuery.trim();
        }

        // Add category filter if not 'all'
        if (this.categoryFilter !== 'all') {
          params.category = this.categoryFilter;
        }

        // Add status filter if not 'all'
        if (this.statusFilter !== 'all') {
          params.status = this.statusFilter;
        }

        // Call API to fetch inventory items
        const response = await inventoryService.getInventoryItems(params);
        
        // Process the response
        this.inventoryItems = response.data.data || [];
        this.filteredItems = this.inventoryItems;
        this.paginatedItems = this.serverSidePagination ? this.inventoryItems : this.getPaginatedItems(this.filteredItems, this.pagination);
        
        // Update pagination info from API response
        if (this.serverSidePagination && response.data.meta) {
          this.pagination.currentPage = response.data.meta.current_page || 1;
          this.pagination.totalPages = response.data.meta.last_page || 1;
          const total = response.data.meta.total || 0;
          const perPage = response.data.meta.per_page || this.pagination.itemsPerPage;
          const start = (this.pagination.currentPage - 1) * perPage + 1;
          const end = Math.min(start + perPage - 1, total);
          
          this.pagination.startItem = total ? start : 0;
          this.pagination.endItem = end;
        }

        this.error = null;
      } catch (error) {
        this.handleError(error);
        this.error = error;
        // Fallback to show empty state
        this.inventoryItems = [];
        this.filteredItems = [];
        this.paginatedItems = [];
      } finally {
        this.stopLoading();
      }
    },
    handleSearch() {
      if (this.serverSidePagination) {
        this.pagination.currentPage = 1;
        this.fetchPageData();
      } else {
        this.pagination.currentPage = 1;
        this.applyFilters();
      }
    },
    applyFilters() {
      if (this.serverSidePagination) {
        this.fetchPageData();
      } else {
        // Client-side filtering logic - only used if serverSidePagination is false
        let result = [...this.inventoryItems];
        
        // Apply search filter
        if (this.searchQuery.trim() !== '') {
          const query = this.searchQuery.toLowerCase();
          result = result.filter(item => 
            item.id.toLowerCase().includes(query) ||
            item.name.toLowerCase().includes(query) ||
            item.category.toLowerCase().includes(query)
          );
        }
        
        // Apply category filter
        if (this.categoryFilter !== 'all') {
          result = result.filter(item => item.category === this.categoryFilter);
        }
        
        // Apply status filter
        if (this.statusFilter !== 'all') {
          result = result.filter(item => item.status === this.statusFilter);
        }
        
        // Store filtered results
        this.filteredItems = result;
        
        // Calculate pagination
        this.updatePagination(this.filteredItems, this.pagination);
        this.updatePaginatedItems();
      }
    },
    updatePaginatedItems() {
      if (!this.serverSidePagination) {
        this.paginatedItems = this.getPaginatedItems(this.filteredItems, this.pagination);
      }
    },
    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
        if (this.serverSidePagination) {
          this.fetchPageData();
        } else {
          this.updatePagination(this.filteredItems, this.pagination);
          this.updatePaginatedItems();
        }
      }
    },
    nextPage() {
      if (this.pagination.currentPage < this.pagination.totalPages) {
        this.pagination.currentPage++;
        if (this.serverSidePagination) {
          this.fetchPageData();
        } else {
          this.updatePagination(this.filteredItems, this.pagination);
          this.updatePaginatedItems();
        }
      }
    },
    updatePagination(items, pagination) {
      if (!this.serverSidePagination) {
        pagination.totalPages = Math.ceil(items.length / pagination.itemsPerPage) || 1;
        
        // Ensure current page is within bounds
        if (pagination.currentPage > pagination.totalPages) {
          pagination.currentPage = 1;
        }
        
        const start = (pagination.currentPage - 1) * pagination.itemsPerPage;
        const end = Math.min(start + pagination.itemsPerPage, items.length);
        
        pagination.startItem = items.length ? start + 1 : 0;
        pagination.endItem = end;
      }
    },
    getPaginatedItems(items, pagination) {
      if (this.serverSidePagination) {
        return items;
      }
      
      const start = (pagination.currentPage - 1) * pagination.itemsPerPage;
      const end = start + pagination.itemsPerPage;
      return items.slice(start, end);
    },
    async viewItemDetails(item) {
      try {
        this.startLoading(`Loading details for ${item.id}...`);
        
        // Fetch full item details from API
        const response = await inventoryService.getInventoryItem(item.id);
        this.viewingItem = response.data.data;
      } catch (error) {
        this.handleError(error);
        // Fallback to the data we already have
        this.viewingItem = {...item};
      } finally {
        this.stopLoading();
      }
    },
    async updateItemStatus(id, newStatus) {
      try {
        this.startLoading(`Updating status to ${newStatus}...`);
        
        // Call API to update item status
        await inventoryService.updateItemStatus(id, newStatus);
        
        this.showSuccess(`Item ${id} status updated to ${newStatus}`, 'Status Updated');
        
        // Refresh data to get the updated status
        this.fetchPageData();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    showAddItemModal() {
      this.showAddModal = true;
    },
    closeAddModal() {
      this.showAddModal = false;
      this.resetNewItem();
    },
    async addNewItem() {
      try {
        this.startLoading('Creating new inventory item...');
        
        // Process features if entered
        let features = [];
        if (this.featuresInput.trim()) {
          features = this.featuresInput.split(',').map(feature => feature.trim());
        }
        
        // Format data for API
        const formattedData = {
          name: this.newItem.name,
          category: this.newItem.category,
          status: this.newItem.status,
          rate: this.newItem.rate,
          description: this.newItem.description,
          features: features,
          acquisition_date: this.newItem.acquisitionDate || null
        };
        
        // Call API to create new item
        await inventoryService.createInventoryItem(formattedData);
        
        this.showSuccess('New item added successfully!', 'Item Added');
        this.closeAddModal();
        
        // Refresh the data
        this.fetchPageData();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    resetNewItem() {
      this.newItem = {
        name: '',
        category: '',
        status: 'Available',
        rate: 0,
        acquisitionDate: '',
        description: '',
        features: []
      };
      this.featuresInput = '';
    },
    refreshData() {
      // Reset filters
      this.searchQuery = '';
      this.categoryFilter = 'all';
      this.statusFilter = 'all';
      this.pagination.currentPage = 1;
      
      // Fetch fresh data
      this.fetchPageData();
    },
    exportToCSV() {
      // Create CSV content
      const headers = ['ID', 'Name', 'Category', 'Status', 'Daily Rate', 'Acquisition Date'];
      const itemData = this.filteredItems.map(item => [
        item.id,
        item.name,
        item.category,
        item.status,
        `$${item.rate}`,
        item.acquisition_date || ''
      ]);
      
      // Combine headers and data
      const csvContent = [
        headers.join(','),
        ...itemData.map(row => row.map(cell => `"${cell}"`).join(','))
      ].join('\n');
      
      // Create download link
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
      
      link.setAttribute('href', url);
      link.setAttribute('download', `inventory-items-${timestamp}.csv`);
      link.style.display = 'none';
      
      // Append to the document, trigger download, and clean up
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      setTimeout(() => window.URL.revokeObjectURL(url), 100);
      
      this.showSuccess('Inventory data exported successfully', 'Export Complete');
    },
    printTable() {
      // Save current document body
      const originalContents = document.body.innerHTML;
      
      // Create a print-friendly version of the table
      let printContents = '<h2 style="text-align: center; margin-bottom: 20px;">Inventory Items</h2>';
      printContents += '<table style="width: 100%; border-collapse: collapse;">';
      
      // Table headers
      printContents += '<thead><tr>';
      ['ID', 'Name', 'Category', 'Status', 'Daily Rate', 'Acquisition Date'].forEach(header => {
        printContents += `<th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">${header}</th>`;
      });
      printContents += '</tr></thead>';
      
      // Table body
      printContents += '<tbody>';
      this.filteredItems.forEach(item => {
        printContents += '<tr>';
        [
          item.id,
          item.name,
          item.category,
          item.status,
          `$${item.rate}/day`,
          item.acquisition_date || 'Not specified'
        ].forEach(cell => {
          printContents += `<td style="border: 1px solid #ddd; padding: 8px;">${cell}</td>`;
        });
        printContents += '</tr>';
      });
      printContents += '</tbody></table>';
      
      // Add print date
      const today = new Date().toLocaleDateString();
      printContents += `<p style="text-align: right; margin-top: 20px; font-style: italic;">Generated on ${today}</p>`;
      
      // Replace the entire body with our print content
      document.body.innerHTML = printContents;
      
      // Trigger print dialog
      window.print();
      
      // Restore original content
      document.body.innerHTML = originalContents;
      
      this.showSuccess('Print job sent to printer', 'Print Initiated');
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