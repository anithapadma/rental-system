<template>
  <div class="flex">
    <Sidebar />
    <div class="ml-64 p-6 w-full"  style="    margin-top: -60px;">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-bold">Rental Agreements</h1>
        <div class="flex gap-4">
          <button @click="refreshData" class="btn-refresh flex items-center gap-2 transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <button @click="showCreateAgreementModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
            + Create Agreement
          </button>
        </div>
      </div>
      
      <!-- Error Alert -->
      <div v-if="error" class="alert alert-error mb-6">
        <p>{{ error.message || 'An error occurred while loading data' }}</p>
      </div>
      
      <!-- Agreement Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 mb-6 animate-fade-in">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Active Agreements</p>
              <p class="text-2xl font-bold text-gray-800">{{ agreementCounts.active }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">Currently in effect</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Total Value</p>
              <p class="text-2xl font-bold text-gray-800">${{ totalValue }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">Current rental revenue</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500 hover:shadow-md transition-all duration-300">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Total Agreements</p>
              <p class="text-2xl font-bold text-gray-800">{{ agreements.length }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-2">All rental agreements</p>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-500 hover:shadow-lg">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
          <div class="relative">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search agreements..." 
              @input="handleSearch"
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow duration-300"
            />
            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
          
          <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Filter by:</label>
            <select 
              v-model="statusFilter"
              @change="applyFilters"
              class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
              <option value="all">All Agreements</option>
              <option value="Active">Active</option>
              <option value="Expired">Expired</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Agreement ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Start Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  End Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Value
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="paginatedAgreements.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  No agreement records found matching your criteria.
                </td>
              </tr>
              <tr 
                v-for="agreement in paginatedAgreements" 
                :key="agreement.id" 
                class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ agreement.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ agreement.customer }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(agreement.startDate) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(agreement.endDate) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ${{ agreement.value }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(agreement.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ agreement.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="viewAgreement(agreement)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">View</button>
                  <button @click="downloadAgreement(agreement.id)" class="text-gray-600 hover:text-gray-900 transition-colors duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    PDF
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mt-6 space-y-4 md:space-y-0">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ pagination.startItem }}</span> to 
            <span class="font-medium">{{ pagination.endItem }}</span> of 
            <span class="font-medium">{{ filteredAgreements.length }}</span> agreements
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
    
    <!-- View Agreement Modal -->
    <div v-if="viewingAgreement" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h2 class="text-xl font-bold text-gray-800">Agreement Details</h2>
          <button @click="viewingAgreement = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Agreement {{ viewingAgreement?.id }}</h3>
            <span :class="getStatusClass(viewingAgreement?.status)" class="px-3 py-1 rounded-full text-xs font-medium">
              {{ viewingAgreement?.status }}
            </span>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-4">
              <div>
                <h4 class="text-sm text-gray-500 mb-1">Customer Information</h4>
                <div class="bg-gray-50 rounded-md p-4">
                  <p class="font-medium text-gray-900">{{ viewingAgreement?.customer }}</p>
                  <p class="text-sm text-gray-500">{{ viewingAgreement?.customerEmail || 'customer@example.com' }}</p>
                  <p class="text-sm text-gray-500">{{ viewingAgreement?.customerPhone || '(555) 123-4567' }}</p>
                </div>
              </div>
              
              <div>
                <h4 class="text-sm text-gray-500 mb-1">Agreement Period</h4>
                <div class="bg-gray-50 rounded-md p-4">
                  <div class="flex justify-between">
                    <div>
                      <p class="text-xs text-gray-500">Start Date</p>
                      <p class="font-medium">{{ formatDate(viewingAgreement?.startDate) }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">End Date</p>
                      <p class="font-medium">{{ formatDate(viewingAgreement?.endDate) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="space-y-4">
              <div>
                <h4 class="text-sm text-gray-500 mb-1">Financial Details</h4>
                <div class="bg-gray-50 rounded-md p-4">
                  <div class="flex justify-between mb-2">
                    <span class="text-sm">Total Value:</span>
                    <span class="font-medium">${{ viewingAgreement?.value }}</span>
                  </div>
                  <div class="flex justify-between mb-2">
                    <span class="text-sm">Payment Status:</span>
                    <span class="font-medium">{{ viewingAgreement?.paymentStatus || 'Paid' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm">Payment Method:</span>
                    <span class="font-medium">{{ viewingAgreement?.paymentMethod || 'Credit Card' }}</span>
                  </div>
                </div>
              </div>
              
              <div>
                <h4 class="text-sm text-gray-500 mb-1">Document Information</h4>
                <div class="bg-gray-50 rounded-md p-4">
                  <div class="flex justify-between mb-2">
                    <span class="text-sm">Created On:</span>
                    <span class="font-medium">{{ viewingAgreement?.createdDate || formatDate(viewingAgreement?.startDate) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm">Last Updated:</span>
                    <span class="font-medium">{{ viewingAgreement?.updatedDate || '2025-04-15' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mb-6">
            <h4 class="text-sm text-gray-500 mb-1">Rental Items</h4>
            <div class="bg-gray-50 rounded-md overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Item</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Quantity</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Rate</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(item, index) in viewingAgreement?.items || defaultItems" :key="index">
                    <td class="px-4 py-2 text-sm">{{ item.name }}</td>
                    <td class="px-4 py-2 text-sm">{{ item.quantity }}</td>
                    <td class="px-4 py-2 text-sm">${{ item.rate }}/day</td>
                    <td class="px-4 py-2 text-sm">${{ item.total }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="mb-6">
            <h4 class="text-sm text-gray-500 mb-1">Additional Notes</h4>
            <div class="bg-gray-50 rounded-md p-4">
              <p class="text-sm">{{ viewingAgreement?.notes || 'Standard terms and conditions apply to this rental agreement. Equipment must be returned in the same condition as provided, normal wear and tear excepted.' }}</p>
            </div>
          </div>
          
          <div class="border-t border-gray-200 pt-4 mt-6 flex justify-between">
            <div>
              <button @click="downloadAgreement(viewingAgreement?.id)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
              </button>
            </div>
            <div>
              <button @click="viewingAgreement = null" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none mr-2">
                Close
              </button>
              <button v-if="viewingAgreement?.status === 'Active'" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                Edit Agreement
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Create Agreement Modal -->
    <div v-if="showCreateAgreementModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto animate-modal-in">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
          <h2 class="text-xl font-bold text-gray-800">Create New Agreement</h2>
          <button @click="showCreateAgreementModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <form @submit.prevent="createNewAgreement" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
              <input 
                v-model="newAgreement.customer" 
                type="text" 
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter customer name"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
              <input 
                v-model="newAgreement.customerEmail" 
                type="email" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter customer email"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone</label>
              <input 
                v-model="newAgreement.customerPhone" 
                type="text" 
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter customer phone"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
              <select
                v-model="newAgreement.paymentMethod"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
              <input 
                v-model="newAgreement.startDate" 
                type="date" 
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
              <input 
                v-model="newAgreement.endDate" 
                type="date" 
                required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
          
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-700 mb-3">Rental Items</h3>
            <div class="bg-gray-50 rounded-md p-4 mb-4">
              <div v-for="(item, index) in newAgreement.items" :key="index" class="flex items-center gap-4 mb-3">
                <div class="flex-grow">
                  <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-5">
                      <label class="block text-xs font-medium text-gray-500 mb-1">Item</label>
                      <input 
                        v-model="item.name" 
                        type="text"
                        class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Item name"
                      >
                    </div>
                    <div class="col-span-2">
                      <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                      <input 
                        v-model="item.quantity" 
                        type="number"
                        min="1"
                        class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                    </div>
                    <div class="col-span-2">
                      <label class="block text-xs font-medium text-gray-500 mb-1">Rate</label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-500 text-sm">$</span>
                        <input 
                          v-model="item.rate" 
                          type="number"
                          min="0"
                          step="0.01"
                          class="w-full border border-gray-300 rounded-md pl-6 pr-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                      </div>
                    </div>
                    <div class="col-span-2">
                      <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                      <div class="relative bg-gray-100 rounded-md px-3 py-1 text-sm">
                        ${{ calculateItemTotal(item) }}
                      </div>
                    </div>
                    <div class="col-span-1 flex items-end justify-end">
                      <button 
                        type="button"
                        @click="removeItem(index)"
                        class="text-red-500 hover:text-red-700 focus:outline-none"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <button 
                type="button"
                @click="addItem"
                class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
              </button>
            </div>
            
            <div class="bg-gray-50 rounded-md p-4 flex justify-between items-center">
              <span class="text-lg font-medium">Total Agreement Value:</span>
              <span class="text-xl font-bold">${{ calculateAgreementTotal() }}</span>
            </div>
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
            <textarea 
              v-model="newAgreement.notes" 
              rows="3"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter any additional notes or terms for this agreement"
            ></textarea>
          </div>
          
          <div class="flex justify-end">
            <button 
              type="button"
              @click="showCreateAgreementModal = false" 
              class="mr-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
              Cancel
            </button>
            <button 
              type="submit"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
              Create Agreement
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Loading Spinner -->
    <LoadingSpinner :is-visible="isLoading" :message="loadingMessage" />
    
    <!-- Download Complete Notification -->
    <div 
      v-if="downloadComplete" 
      class="fixed bottom-4 right-4 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-lg animate-fade-in"
      @animationend="downloadComplete = false">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-green-800">
            Agreement PDF downloaded successfully
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../../components/Sidebar.vue';
import LoadingSpinner from '../../components/LoadingSpinner.vue';
import pageFunctionality from '../../mixins/pageFunctionality';

export default {
  name: 'Agreements',
  components: {
    Sidebar,
    LoadingSpinner
  },
  mixins: [pageFunctionality],
  data() {
    return {
      agreements: [
        { id: 'AGR-001', customer: 'John Smith', startDate: '2025-04-28', endDate: '2025-05-04', value: 315, status: 'Active', customerEmail: 'john@example.com', customerPhone: '(555) 123-4567' },
        { id: 'AGR-002', customer: 'Emily Brown', startDate: '2025-04-28', endDate: '2025-05-05', value: 175, status: 'Active', customerEmail: 'emily@example.com', customerPhone: '(555) 234-5678' },
        { id: 'AGR-003', customer: 'Mike Williams', startDate: '2025-04-20', endDate: '2025-04-29', value: 400, status: 'Expired', customerEmail: 'mike@example.com', customerPhone: '(555) 345-6789' },
        { id: 'AGR-004', customer: 'Sarah Johnson', startDate: '2025-04-25', endDate: '2025-04-30', value: 175, status: 'Completed', customerEmail: 'sarah@example.com', customerPhone: '(555) 456-7890' },
        { id: 'AGR-005', customer: 'David Miller', startDate: '2025-04-22', endDate: '2025-04-27', value: 375, status: 'Cancelled', customerEmail: 'david@example.com', customerPhone: '(555) 567-8901' },
        { id: 'AGR-006', customer: 'Robert Jones', startDate: '2025-05-01', endDate: '2025-05-03', value: 90, status: 'Active', customerEmail: 'robert@example.com', customerPhone: '(555) 678-9012' },
        { id: 'AGR-007', customer: 'Lisa Davis', startDate: '2025-05-01', endDate: '2025-05-05', value: 80, status: 'Active', customerEmail: 'lisa@example.com', customerPhone: '(555) 789-0123' },
        { id: 'AGR-008', customer: 'James Wilson', startDate: '2025-04-29', endDate: '2025-05-02', value: 45, status: 'Active', customerEmail: 'james@example.com', customerPhone: '(555) 890-1234' }
      ],
      filteredAgreements: [],
      paginatedAgreements: [],
      searchQuery: '',
      statusFilter: 'all',
      pagination: {
        currentPage: 1,
        itemsPerPage: 5,
        totalPages: 1,
        startItem: 1,
        endItem: 1
      },
      viewingAgreement: null,
      showCreateAgreementModal: false,
      downloadComplete: false,
      defaultItems: [
        { name: 'Power Generator', quantity: 1, rate: 45, total: 135 },
        { name: 'Floor Sander', quantity: 1, rate: 35, total: 105 },
        { name: 'Pressure Washer', quantity: 1, rate: 25, total: 75 }
      ],
      newAgreement: {
        customer: '',
        customerEmail: '',
        customerPhone: '',
        startDate: new Date().toISOString().split('T')[0],
        endDate: '',
        paymentMethod: 'Credit Card',
        status: 'Active',
        notes: '',
        items: [
          { name: '', quantity: 1, rate: 0 }
        ]
      }
    };
  },
  computed: {
    agreementCounts() {
      return {
        active: this.agreements.filter(a => a.status === 'Active').length,
        completed: this.agreements.filter(a => a.status === 'Completed').length,
        cancelled: this.agreements.filter(a => a.status === 'Cancelled').length,
        expired: this.agreements.filter(a => a.status === 'Expired').length
      };
    },
    totalValue() {
      return this.agreements
        .filter(a => a.status === 'Active')
        .reduce((sum, agreement) => sum + agreement.value, 0);
    }
  },
  watch: {
    filteredAgreements() {
      this.updatePaginatedAgreements();
    },
    'pagination.currentPage'() {
      this.updatePaginatedAgreements();
    }
  },
  mounted() {
    this.fetchPageData();
  },
  methods: {
    getStatusClass(status) {
      switch (status) {
        case 'Active':
          return 'bg-blue-100 text-blue-800';
        case 'Completed':
          return 'bg-green-100 text-green-800';
        case 'Expired':
          return 'bg-red-100 text-red-800';
        case 'Cancelled':
          return 'bg-gray-100 text-gray-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    },
    async fetchPageData() {
      this.startLoading("Loading agreements...");
      
      try {
        await new Promise(resolve => setTimeout(resolve, 600));
        this.searchQuery = '';
        this.statusFilter = 'all';
        this.pagination.currentPage = 1;
        
        this.applyFilters();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    handleSearch() {
      this.pagination.currentPage = 1;
      this.applyFilters();
    },
    applyFilters() {
      let result = [...this.agreements];
      
      // Apply search filter
      if (this.searchQuery.trim() !== '') {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(agreement => 
          agreement.id.toLowerCase().includes(query) ||
          agreement.customer.toLowerCase().includes(query)
        );
      }
      
      // Apply status filter
      if (this.statusFilter !== 'all') {
        result = result.filter(agreement => agreement.status === this.statusFilter);
      }
      
      // Store filtered results
      this.filteredAgreements = result;
      
      // Calculate pagination
      this.calculatePagination();
      this.updatePaginatedAgreements();
    },
    calculatePagination() {
      this.pagination.totalPages = Math.ceil(this.filteredAgreements.length / this.pagination.itemsPerPage) || 1;
      
      if (this.pagination.currentPage > this.pagination.totalPages) {
        this.pagination.currentPage = 1;
      }
      
      const start = (this.pagination.currentPage - 1) * this.pagination.itemsPerPage;
      const end = Math.min(start + this.pagination.itemsPerPage, this.filteredAgreements.length);
      
      this.pagination.startItem = this.filteredAgreements.length ? start + 1 : 0;
      this.pagination.endItem = end;
    },
    updatePaginatedAgreements() {
      const start = (this.pagination.currentPage - 1) * this.pagination.itemsPerPage;
      const end = start + this.pagination.itemsPerPage;
      this.paginatedAgreements = this.filteredAgreements.slice(start, end);
    },
    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
        this.calculatePagination();
      }
    },
    nextPage() {
      if (this.pagination.currentPage < this.pagination.totalPages) {
        this.pagination.currentPage++;
        this.calculatePagination();
      }
    },
    viewAgreement(agreement) {
      this.viewingAgreement = {...agreement};
      
      // If the agreement doesn't have items, assign some
      if (!this.viewingAgreement.items) {
        // Map default items and adjust values based on agreement value
        const factor = this.viewingAgreement.value / 315; // Normalize factor
        this.viewingAgreement.items = this.defaultItems.map(item => {
          return {
            ...item,
            total: Math.round(item.total * factor)
          };
        });
      }
    },
    async downloadAgreement(id) {
      this.startLoading(`Preparing agreement ${id} for download...`);
      
      try {
        // Simulate PDF generation process
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Create a blob object that represents a PDF file
        const blob = new Blob(['Agreement PDF content'], { type: 'application/pdf' });
        
        // Create a download link and trigger it
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = `Agreement-${id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showSuccess(`Agreement ${id} downloaded successfully`, 'Download Complete');
        this.downloadComplete = true;
        
        // Hide download notification after 3 seconds
        setTimeout(() => {
          this.downloadComplete = false;
        }, 3000);
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
    },
    calculateItemTotal(item) {
      return (item.quantity * item.rate).toFixed(2);
    },
    calculateAgreementTotal() {
      return this.newAgreement.items
        .reduce((sum, item) => sum + (item.quantity * item.rate), 0)
        .toFixed(2);
    },
    addItem() {
      this.newAgreement.items.push({ name: '', quantity: 1, rate: 0 });
    },
    removeItem(index) {
      if (this.newAgreement.items.length > 1) {
        this.newAgreement.items.splice(index, 1);
      }
    },
    async createNewAgreement() {
      this.startLoading('Creating new agreement...');
      
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1200));
        
        // Calculate total value
        const value = parseFloat(this.calculateAgreementTotal());
        
        // Create new agreement object
        const newAgreementId = `AGR-${String(this.agreements.length + 1).padStart(3, '0')}`;
        const agreement = {
          id: newAgreementId,
          customer: this.newAgreement.customer,
          customerEmail: this.newAgreement.customerEmail,
          customerPhone: this.newAgreement.customerPhone,
          startDate: this.newAgreement.startDate,
          endDate: this.newAgreement.endDate,
          value: value,
          status: 'Active',
          paymentMethod: this.newAgreement.paymentMethod,
          notes: this.newAgreement.notes,
          items: this.newAgreement.items.map(item => ({
            ...item,
            total: parseFloat(this.calculateItemTotal(item))
          })),
          createdDate: new Date().toISOString().split('T')[0],
          updatedDate: new Date().toISOString().split('T')[0]
        };
        
        // Add to agreements array
        this.agreements.unshift(agreement);
        
        // Reset form
        this.newAgreement = {
          customer: '',
          customerEmail: '',
          customerPhone: '',
          startDate: new Date().toISOString().split('T')[0],
          endDate: '',
          paymentMethod: 'Credit Card',
          status: 'Active',
          notes: '',
          items: [
            { name: '', quantity: 1, rate: 0 }
          ]
        };
        
        // Close modal
        this.showCreateAgreementModal = false;
        
        // Show success message
        this.showSuccess(`Agreement ${newAgreementId} created successfully`, 'Agreement Created');
        
        // Refresh data to update counts and lists
        this.applyFilters();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.stopLoading();
      }
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