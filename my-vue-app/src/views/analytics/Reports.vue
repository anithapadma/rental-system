<template>
  <div class="reports-page">
    <div class="dashboard-header">
      <div class="header-content">
        <h1 class="dashboard-title">Reports</h1>
        <p class="dashboard-subtitle">Generate and export custom reports for your business</p>
      </div>
      <div class="actions">
        <div class="view-toggle">
          <button class="view-btn" :class="{ 'active': viewMode === 'single' }" @click="viewMode = 'single'">
            <i class="fas fa-table"></i> Single Report
          </button>
          <button class="view-btn" :class="{ 'active': viewMode === 'dashboard' }" @click="viewMode = 'dashboard'">
            <i class="fas fa-th-large"></i> Dashboard
          </button>
        </div>

        <div class="saved-reports-dropdown" v-click-outside="closeSavedReportsDropdown">
          <button class="saved-reports-btn" @click="toggleSavedReportsDropdown">
            <i class="fas fa-bookmark"></i> Saved Reports
            <i class="fas fa-chevron-down"></i>
          </button>
          <div class="saved-reports-menu" v-show="showSavedReportsDropdown">
            <div v-if="savedReports.length === 0" class="no-saved-reports">
              <p>No saved reports yet</p>
            </div>
            <div v-else class="saved-reports-list">
              <div v-for="(report, index) in savedReports" :key="index" class="saved-report-item" @click="loadSavedReport(report)">
                <div class="report-info">
                  <span class="report-name">{{ report.name }}</span>
                  <span class="report-type">{{ getReportTypeName(report.type) }}</span>
                </div>
                <div class="report-actions">
                  <button class="delete-report-btn" @click.stop="deleteSavedReport(index)">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button v-if="reportGenerated" class="save-report-btn" @click="showSaveReportDialog = true">
          <i class="fas fa-save"></i> Save Report
        </button>

        <button v-if="reportGenerated" class="share-report-btn" @click="showShareReportDialog = true">
          <i class="fas fa-share-alt"></i> Share
        </button>
      </div>
    </div>

    <div class="report-actions">
      <div class="report-selector">
        <label for="report-type">Report type:</label>
        <select id="report-type" v-model="selectedReportType">
          <option v-for="type in reportTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>
      </div>
      <div class="date-range-picker">
        <label>Date range:</label>
        <div class="date-inputs">
          <div class="date-input">
            <label for="start-date">From:</label>
            <input id="start-date" type="date" v-model="startDate" />
          </div>
          <div class="date-input">
            <label for="end-date">To:</label>
            <input id="end-date" type="date" v-model="endDate" />
          </div>
        </div>
      </div>
      <button class="generate-btn" @click="generateReport">
        <i class="fas fa-chart-line"></i> Generate Report
      </button>
    </div>

    <div class="reports-content">
      <!-- Report filters -->
      <div class="report-filters">
        <div class="filter-header">
          <h3><i class="fas fa-filter"></i> Filters</h3>
          <button class="reset-filters-btn" @click="resetFilters" title="Reset all filters">
            <i class="fas fa-undo"></i>
          </button>
        </div>
        
        <!-- Revenue report filters -->
        <div v-if="selectedReportType === 'revenue'" class="filter-section">
          <div class="filter-group">
            <label>Revenue sources:</label>
            <div class="checkbox-group">
              <div v-for="source in revenueSources" :key="source.id" class="checkbox-item">
                <input type="checkbox" :id="source.id" v-model="source.selected" />
                <label :for="source.id">{{ source.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label>Locations:</label>
            <div class="checkbox-group">
              <div v-for="location in locations" :key="location.id" class="checkbox-item">
                <input type="checkbox" :id="location.id" v-model="location.selected" />
                <label :for="location.id">{{ location.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label for="revenue-grouping">Group by:</label>
            <select id="revenue-grouping" v-model="revenueGrouping">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="revenue-chart-type">Chart type:</label>
            <select id="revenue-chart-type" v-model="revenueChartType">
              <option value="line">Line Chart</option>
              <option value="bar">Bar Chart</option>
              <option value="stacked">Stacked Bar Chart</option>
            </select>
          </div>
        </div>
        
        <!-- Inventory report filters -->
        <div v-if="selectedReportType === 'inventory'" class="filter-section">
          <div class="filter-group">
            <label>Item categories:</label>
            <div class="checkbox-group">
              <div v-for="category in itemCategories" :key="category.id" class="checkbox-item">
                <input type="checkbox" :id="category.id" v-model="category.selected" />
                <label :for="category.id">{{ category.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label>Inventory status:</label>
            <div class="checkbox-group">
              <div v-for="status in inventoryStatuses" :key="status.id" class="checkbox-item">
                <input type="checkbox" :id="status.id" v-model="status.selected" />
                <label :for="status.id">{{ status.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label>Show only:</label>
            <div class="radio-group">
              <div class="radio-item">
                <input type="radio" id="all-items" value="all" v-model="inventoryFilter" />
                <label for="all-items">All items</label>
              </div>
              <div class="radio-item">
                <input type="radio" id="low-stock" value="low-stock" v-model="inventoryFilter" />
                <label for="low-stock">Low stock items</label>
              </div>
              <div class="radio-item">
                <input type="radio" id="high-utilization" value="high-utilization" v-model="inventoryFilter" />
                <label for="high-utilization">High utilization items</label>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Rental report filters -->
        <div v-if="selectedReportType === 'rentals'" class="filter-section">
          <div class="filter-group">
            <label>Rental status:</label>
            <div class="checkbox-group">
              <div v-for="status in rentalStatuses" :key="status.id" class="checkbox-item">
                <input type="checkbox" :id="status.id" v-model="status.selected" />
                <label :for="status.id">{{ status.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label>Customer type:</label>
            <div class="checkbox-group">
              <div v-for="type in customerTypes" :key="type.id" class="checkbox-item">
                <input type="checkbox" :id="type.id" v-model="type.selected" />
                <label :for="type.id">{{ type.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label for="rental-duration">Duration:</label>
            <select id="rental-duration" v-model="rentalDuration">
              <option value="all">All durations</option>
              <option value="short">Short-term (1-7 days)</option>
              <option value="medium">Medium-term (8-30 days)</option>
              <option value="long">Long-term (31+ days)</option>
            </select>
          </div>
        </div>
        
        <!-- Customer report filters -->
        <div v-if="selectedReportType === 'customers'" class="filter-section">
          <div class="filter-group">
            <label>Customer segments:</label>
            <div class="checkbox-group">
              <div v-for="segment in customerSegments" :key="segment.id" class="checkbox-item">
                <input type="checkbox" :id="segment.id" v-model="segment.selected" />
                <label :for="segment.id">{{ segment.name }}</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label>Activity level:</label>
            <div class="radio-group">
              <div class="radio-item">
                <input type="radio" id="all-customers" value="all" v-model="customerActivity" />
                <label for="all-customers">All customers</label>
              </div>
              <div class="radio-item">
                <input type="radio" id="active-customers" value="active" v-model="customerActivity" />
                <label for="active-customers">Active (last 90 days)</label>
              </div>
              <div class="radio-item">
                <input type="radio" id="inactive-customers" value="inactive" v-model="customerActivity" />
                <label for="inactive-customers">Inactive (no rental in 90+ days)</label>
              </div>
            </div>
          </div>
          
          <div class="filter-group">
            <label for="customer-sort">Sort by:</label>
            <select id="customer-sort" v-model="customerSort">
              <option value="lifetime-value">Lifetime Value</option>
              <option value="rental-count">Rental Count</option>
              <option value="recent">Most Recent</option>
              <option value="alphabetical">Alphabetical</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Report preview -->
      <div class="report-preview">
        <h3><i class="fas fa-chart-bar"></i> Report Preview</h3>
        <div v-if="reportGenerated" class="preview-content">
          <div class="preview-header">
            <h4>{{ getReportTitle() }}</h4>
            <div class="preview-date">{{ formatDateRange() }}</div>
          </div>
          
          <div v-if="selectedReportType === 'revenue'" class="revenue-report">
            <div class="revenue-summary">
              <div class="summary-card">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value">${{ formatNumber(revenueSummary.total) }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Avg. Daily Revenue</div>
                <div class="summary-value">${{ formatNumber(revenueSummary.daily) }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Growth (vs Previous)</div>
                <div class="summary-value" :class="{ 'positive': revenueSummary.growth > 0, 'negative': revenueSummary.growth < 0 }">
                  {{ revenueSummary.growth > 0 ? '+' : '' }}{{ revenueSummary.growth }}%
                </div>
              </div>
            </div>
            
            <div class="revenue-chart">
              <component :is="revenueChartType === 'line' ? 'LineChart' : revenueChartType === 'bar' ? 'BarChart' : 'StackedBarChart'" :chartData="revenueReportChartData" />
            </div>
            
            <div class="revenue-table">
              <table>
                <thead>
                  <tr>
                    <th>Period</th>
                    <th>Rental Revenue</th>
                    <th>Additional Services</th>
                    <th>Late Fees</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in revenueTableData" :key="index">
                    <td>{{ item.period }}</td>
                    <td>${{ formatNumber(item.rental) }}</td>
                    <td>${{ formatNumber(item.services) }}</td>
                    <td>${{ formatNumber(item.fees) }}</td>
                    <td>${{ formatNumber(item.total) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>${{ formatNumber(revenueTotals.rental) }}</strong></td>
                    <td><strong>${{ formatNumber(revenueTotals.services) }}</strong></td>
                    <td><strong>${{ formatNumber(revenueTotals.fees) }}</strong></td>
                    <td><strong>${{ formatNumber(revenueTotals.total) }}</strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          
          <div v-if="selectedReportType === 'inventory'" class="inventory-report">
            <div class="inventory-summary">
              <div class="summary-card">
                <div class="summary-label">Total Items</div>
                <div class="summary-value">{{ inventorySummary.total }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Currently Rented</div>
                <div class="summary-value">{{ inventorySummary.rented }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Utilization Rate</div>
                <div class="summary-value">{{ inventorySummary.utilization }}%</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Need Maintenance</div>
                <div class="summary-value">{{ inventorySummary.maintenance }}</div>
              </div>
            </div>
            
            <div class="inventory-charts">
              <div class="chart-container">
                <h5>Status Distribution</h5>
                <DoughnutChart :chartData="inventoryStatusData" />
              </div>
              <div class="chart-container">
                <h5>Utilization by Category</h5>
                <BarChart :chartData="inventoryUtilizationData" />
              </div>
            </div>
            
            <div class="inventory-table">
              <table>
                <thead>
                  <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Rented</th>
                    <th>Maintenance</th>
                    <th>Utilization %</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in inventoryTableData" :key="index">
                    <td>{{ item.name }}</td>
                    <td>{{ item.category }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.available }}</td>
                    <td>{{ item.rented }}</td>
                    <td>{{ item.maintenance }}</td>
                    <td>{{ item.utilization }}%</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div v-if="selectedReportType === 'rentals'" class="rentals-report">
            <div class="rentals-summary">
              <div class="summary-card">
                <div class="summary-label">Total Rentals</div>
                <div class="summary-value">{{ rentalsSummary.total }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Active Rentals</div>
                <div class="summary-value">{{ rentalsSummary.active }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Avg. Duration</div>
                <div class="summary-value">{{ rentalsSummary.avgDuration }} days</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Avg. Value</div>
                <div class="summary-value">${{ formatNumber(rentalsSummary.avgValue) }}</div>
              </div>
            </div>
            
            <div class="rentals-charts">
              <div class="chart-container">
                <h5>Rentals by Duration</h5>
                <DoughnutChart :chartData="rentalsDurationData" />
              </div>
              <div class="chart-container">
                <h5>Rentals by Customer Type</h5>
                <DoughnutChart :chartData="rentalsCustomerData" />
              </div>
            </div>
            
            <div class="rentals-table">
              <table>
                <thead>
                  <tr>
                    <th>Agreement #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(rental, index) in rentalsTableData" :key="index">
                    <td>{{ rental.id }}</td>
                    <td>{{ rental.customer }}</td>
                    <td>{{ rental.items }}</td>
                    <td>{{ rental.startDate }}</td>
                    <td>{{ rental.endDate }}</td>
                    <td>
                      <span :class="'status-' + rental.status.toLowerCase()">{{ rental.status }}</span>
                    </td>
                    <td>${{ formatNumber(rental.value) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div v-if="selectedReportType === 'customers'" class="customers-report">
            <div class="customers-summary">
              <div class="summary-card">
                <div class="summary-label">Total Customers</div>
                <div class="summary-value">{{ customersSummary.total }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Active Customers</div>
                <div class="summary-value">{{ customersSummary.active }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">New Customers</div>
                <div class="summary-value">{{ customersSummary.new }}</div>
              </div>
              <div class="summary-card">
                <div class="summary-label">Avg. Lifetime Value</div>
                <div class="summary-value">${{ formatNumber(customersSummary.avgLTV) }}</div>
              </div>
            </div>
            
            <div class="customers-charts">
              <div class="chart-container">
                <h5>Customer Segments</h5>
                <DoughnutChart :chartData="customersSegmentData" />
              </div>
              <div class="chart-container">
                <h5>Customer Growth</h5>
                <LineChart :chartData="customersGrowthData" />
              </div>
            </div>
            
            <div class="customers-table">
              <table>
                <thead>
                  <tr>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Total Rentals</th>
                    <th>First Rental</th>
                    <th>Last Rental</th>
                    <th>Lifetime Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(customer, index) in customersTableData" :key="index">
                    <td>{{ customer.name }}</td>
                    <td>{{ customer.type }}</td>
                    <td>{{ customer.rentals }}</td>
                    <td>{{ customer.firstRental }}</td>
                    <td>{{ customer.lastRental }}</td>
                    <td>${{ formatNumber(customer.lifetimeValue) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="export-options">
            <button class="export-btn" @click="exportPDF"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="export-btn" @click="exportCSV"><i class="fas fa-file-csv"></i> Export CSV</button>
            <button class="export-btn" @click="exportExcel"><i class="fas fa-file-excel"></i> Export Excel</button>
            <button class="export-btn" @click="scheduleReport"><i class="fas fa-calendar-alt"></i> Schedule</button>
          </div>
        </div>
        <div v-else class="no-report-message">
          <div class="message-icon"><i class="fas fa-chart-bar"></i></div>
          <div class="message-text">
            <p>Configure your report options and click "Generate Report" to see a preview</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Report Dialog -->
    <div v-if="showSaveReportDialog" class="dialog-backdrop" @click.self="showSaveReportDialog = false">
      <div class="dialog-content">
        <h3>Save Report</h3>
        <div class="form-group">
          <label for="report-name">Report Name:</label>
          <input type="text" id="report-name" v-model="newReportName" placeholder="Enter a name for this report">
        </div>
        <div class="form-group">
          <label for="report-description">Description (optional):</label>
          <textarea id="report-description" v-model="newReportDescription" placeholder="Add a description"></textarea>
        </div>
        <div class="dialog-actions">
          <button class="cancel-btn" @click="showSaveReportDialog = false">Cancel</button>
          <button class="save-btn" @click="saveReport" :disabled="!newReportName">Save Report</button>
        </div>
      </div>
    </div>

    <!-- Schedule Report Dialog -->
    <div v-if="showScheduleDialog" class="dialog-backdrop" @click.self="showScheduleDialog = false">
      <div class="dialog-content">
        <h3>Schedule Report</h3>
        <div class="form-group">
          <label>Frequency:</label>
          <div class="radio-group schedule-options">
            <div class="radio-item">
              <input type="radio" id="schedule-daily" value="daily" v-model="scheduleFrequency">
              <label for="schedule-daily">Daily</label>
            </div>
            <div class="radio-item">
              <input type="radio" id="schedule-weekly" value="weekly" v-model="scheduleFrequency">
              <label for="schedule-weekly">Weekly</label>
            </div>
            <div class="radio-item">
              <input type="radio" id="schedule-monthly" value="monthly" v-model="scheduleFrequency">
              <label for="schedule-monthly">Monthly</label>
            </div>
          </div>
        </div>
        
        <div class="form-group" v-if="scheduleFrequency === 'weekly'">
          <label>Day of week:</label>
          <select v-model="scheduleDayOfWeek">
            <option value="1">Monday</option>
            <option value="2">Tuesday</option>
            <option value="3">Wednesday</option>
            <option value="4">Thursday</option>
            <option value="5">Friday</option>
            <option value="6">Saturday</option>
            <option value="0">Sunday</option>
          </select>
        </div>
        
        <div class="form-group" v-if="scheduleFrequency === 'monthly'">
          <label>Day of month:</label>
          <select v-model="scheduleDayOfMonth">
            <option v-for="i in 31" :key="i" :value="i">{{ i }}</option>
            <option value="last">Last day</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Delivery method:</label>
          <div class="checkbox-group">
            <div class="checkbox-item">
              <input type="checkbox" id="delivery-email" v-model="deliveryEmail">
              <label for="delivery-email">Email</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="delivery-dashboard" v-model="deliveryDashboard">
              <label for="delivery-dashboard">Dashboard</label>
            </div>
          </div>
        </div>
        
        <div class="form-group" v-if="deliveryEmail">
          <label for="email-recipients">Email recipients:</label>
          <input type="text" id="email-recipients" v-model="emailRecipients" placeholder="Enter email addresses separated by commas">
        </div>
        
        <div class="form-group">
          <label>Report format:</label>
          <div class="checkbox-group">
            <div class="checkbox-item">
              <input type="checkbox" id="format-pdf" v-model="formatPDF">
              <label for="format-pdf">PDF</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="format-excel" v-model="formatExcel">
              <label for="format-excel">Excel</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="format-csv" v-model="formatCSV">
              <label for="format-csv">CSV</label>
            </div>
          </div>
        </div>
        
        <div class="dialog-actions">
          <button class="cancel-btn" @click="showScheduleDialog = false">Cancel</button>
          <button class="save-btn" @click="saveSchedule">Schedule</button>
        </div>
      </div>
    </div>

    <!-- Share Report Dialog -->
    <div v-if="showShareReportDialog" class="dialog-backdrop" @click.self="showShareReportDialog = false">
      <div class="dialog-content">
        <h3><i class="fas fa-share-alt"></i> Share Report</h3>
        <div class="form-group">
          <label>Share via:</label>
          <div class="share-options">
            <button class="share-option" @click="shareViaEmail">
              <i class="fas fa-envelope"></i>
              <span>Email</span>
            </button>
            <button class="share-option" @click="shareViaLink">
              <i class="fas fa-link"></i>
              <span>Copy Link</span>
            </button>
            <button class="share-option" @click="exportAndShare">
              <i class="fas fa-file-export"></i>
              <span>Export & Share</span>
            </button>
          </div>
        </div>
        <div class="form-group" v-if="shareMethod === 'email'">
          <label for="share-email-recipients">Email recipients:</label>
          <div class="recipients-input">
            <input type="text" id="share-email-recipients" v-model="shareEmailRecipients" placeholder="Enter email addresses separated by commas">
          </div>
          <div class="form-group">
            <label for="share-message">Message (optional):</label>
            <textarea id="share-message" v-model="shareMessage" placeholder="Add a message"></textarea>
          </div>
        </div>
        <div class="form-group" v-if="shareMethod === 'link'">
          <label>Shareable link:</label>
          <div class="copy-link-container">
            <input type="text" readonly v-model="shareableLink">
            <button class="copy-btn" @click="copyLink"><i class="fas fa-copy"></i></button>
          </div>
          <div class="permissions-options">
            <label>Link permissions:</label>
            <div class="radio-group">
              <div class="radio-item">
                <input type="radio" id="perm-view" value="view" v-model="linkPermission">
                <label for="perm-view">View only</label>
              </div>
              <div class="radio-item">
                <input type="radio" id="perm-edit" value="edit" v-model="linkPermission">
                <label for="perm-edit">View and edit</label>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group" v-if="shareMethod === 'export'">
          <label>Export format:</label>
          <div class="checkbox-group">
            <div class="checkbox-item">
              <input type="checkbox" id="share-pdf" v-model="shareFormatPDF">
              <label for="share-pdf">PDF</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="share-excel" v-model="shareFormatExcel">
              <label for="share-excel">Excel</label>
            </div>
          </div>
        </div>
        <div class="dialog-actions">
          <button class="cancel-btn" @click="showShareReportDialog = false">Cancel</button>
          <button class="share-btn" @click="submitShare" :disabled="!canShare">Share</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineChart from '@/components/charts/LineChart.vue';
import BarChart from '@/components/charts/BarChart.vue';
import DoughnutChart from '@/components/charts/DoughnutChart.vue';
import analyticsService from '@/services/analyticsService';

export default {
  name: 'Reports',
  components: {
    LineChart,
    BarChart,
    DoughnutChart
  },
  data() {
    return {
      // Debug flag to help troubleshoot API issues
      debugMode: true,
      
      // API status for tracking requests
      apiStatus: {
        loading: false,
        error: null
      },
      
      reportTypes: [
        { id: 'revenue', name: 'Revenue Report' },
        { id: 'inventory', name: 'Inventory Report' },
        { id: 'rentals', name: 'Rental Agreements Report' },
        { id: 'customers', name: 'Customer Report' }
      ],
      selectedReportType: 'revenue',
      startDate: '2025-04-01',
      endDate: '2025-04-30',
      reportGenerated: false,
      
      // Filter options
      revenueSources: [
        { id: 'rental', name: 'Rental Revenue', selected: true },
        { id: 'services', name: 'Additional Services', selected: true },
        { id: 'fees', name: 'Late Fees', selected: true }
      ],
      locations: [
        { id: 'north', name: 'North Branch', selected: true },
        { id: 'south', name: 'South Branch', selected: true },
        { id: 'east', name: 'East Branch', selected: true },
        { id: 'west', name: 'West Branch', selected: true },
        { id: 'central', name: 'Central HQ', selected: true }
      ],
      revenueGrouping: 'weekly',
      
      itemCategories: [
        { id: 'construction', name: 'Construction', selected: true },
        { id: 'photography', name: 'Photography', selected: true },
        { id: 'power', name: 'Power Equipment', selected: true },
        { id: 'events', name: 'Event Equipment', selected: true },
        { id: 'other', name: 'Other', selected: true }
      ],
      inventoryStatuses: [
        { id: 'available', name: 'Available', selected: true },
        { id: 'rented', name: 'Rented', selected: true },
        { id: 'maintenance', name: 'Maintenance', selected: true },
        { id: 'reserved', name: 'Reserved', selected: true }
      ],
      inventoryFilter: 'all',
      
      rentalStatuses: [
        { id: 'active', name: 'Active', selected: true },
        { id: 'completed', name: 'Completed', selected: true },
        { id: 'overdue', name: 'Overdue', selected: true },
        { id: 'cancelled', name: 'Cancelled', selected: false }
      ],
      customerTypes: [
        { id: 'business', name: 'Business', selected: true },
        { id: 'individual', name: 'Individual', selected: true },
        { id: 'government', name: 'Government', selected: true },
        { id: 'non-profit', name: 'Non-Profit', selected: true }
      ],
      rentalDuration: 'all',
      
      customerSegments: [
        { id: 'business', name: 'Business', selected: true },
        { id: 'individual', name: 'Individual', selected: true },
        { id: 'government', name: 'Government', selected: true },
        { id: 'non-profit', name: 'Non-Profit', selected: true }
      ],
      customerActivity: 'all',
      customerSort: 'lifetime-value',
      
      // Report data
      revenueSummary: {
        total: 56840,
        daily: 1894.67,
        growth: 8.5
      },
      revenueTableData: [
        { period: 'Week 1 (Apr 1 - Apr 7)', rental: 8240, services: 1250, fees: 320, total: 9810 },
        { period: 'Week 2 (Apr 8 - Apr 14)', rental: 9850, services: 1480, fees: 410, total: 11740 },
        { period: 'Week 3 (Apr 15 - Apr 21)', rental: 10780, services: 1620, fees: 290, total: 12690 },
        { period: 'Week 4 (Apr 22 - Apr 30)', rental: 18900, services: 2840, fees: 860, total: 22600 }
      ],
      revenueTotals: {
        rental: 47770,
        services: 7190,
        fees: 1880,
        total: 56840
      },
      
      inventorySummary: {
        total: 248,
        rented: 124,
        utilization: 76,
        maintenance: 18
      },
      inventoryTableData: [
        { name: 'Construction Equipment', category: 'Construction', quantity: 65, available: 21, rented: 38, maintenance: 6, utilization: 84 },
        { name: 'Photography Equipment', category: 'Photography', quantity: 42, available: 12, rented: 28, maintenance: 2, utilization: 82 },
        { name: 'Power Equipment', category: 'Power', quantity: 56, available: 18, rented: 32, maintenance: 6, utilization: 78 },
        { name: 'Event Equipment', category: 'Events', quantity: 48, available: 14, rented: 26, maintenance: 8, utilization: 72 },
        { name: 'Other Equipment', category: 'Other', quantity: 37, available: 15, rented: 20, maintenance: 2, utilization: 64 }
      ],
      
      rentalsSummary: {
        total: 124,
        active: 68,
        avgDuration: 14.5,
        avgValue: 458.25
      },
      rentalsTableData: [
        { id: 'R2505-001', customer: 'Acme Construction Ltd.', items: 3, startDate: '2025-04-22', endDate: '2025-05-12', status: 'Active', value: 3800 },
        { id: 'R2505-002', customer: 'GreenScape Landscaping', items: 2, startDate: '2025-04-18', endDate: '2025-05-02', status: 'Active', value: 1650 },
        { id: 'R2504-058', customer: 'Elite Media Productions', items: 4, startDate: '2025-04-15', endDate: '2025-04-30', status: 'Overdue', value: 2400 },
        { id: 'R2504-045', customer: 'Horizon Events Co.', items: 6, startDate: '2025-04-10', endDate: '2025-04-20', status: 'Completed', value: 2850 },
        { id: 'R2504-032', customer: 'Metro Property Services', items: 2, startDate: '2025-04-05', endDate: '2025-04-15', status: 'Completed', value: 1200 }
      ],
      
      customersSummary: {
        total: 248,
        active: 164,
        new: 28,
        avgLTV: 8450
      },
      customersTableData: [
        { name: 'Acme Construction Ltd.', type: 'Business', rentals: 48, firstRental: '2024-06-15', lastRental: '2025-04-28', lifetimeValue: 42500 },
        { name: 'GreenScape Landscaping', type: 'Business', rentals: 36, firstRental: '2024-08-03', lastRental: '2025-04-22', lifetimeValue: 28750 },
        { name: 'Elite Media Productions', type: 'Business', rentals: 29, firstRental: '2024-09-12', lastRental: '2025-04-15', lifetimeValue: 22400 },
        { name: 'Horizon Events Co.', type: 'Business', rentals: 27, firstRental: '2024-07-25', lastRental: '2025-04-06', lifetimeValue: 19600 },
        { name: 'Metro Property Services', type: 'Business', rentals: 23, firstRental: '2024-10-08', lastRental: '2025-03-30', lifetimeValue: 17850 }
      ],
      
      // New data properties
      revenueChartType: 'line',
      showSavedReportsDropdown: false,
      showSaveReportDialog: false,
      showScheduleDialog: false,
      newReportName: '',
      newReportDescription: '',
      savedReports: [],
      
      // Schedule dialog properties
      scheduleFrequency: 'weekly',
      scheduleDayOfWeek: '1',
      scheduleDayOfMonth: '1',
      deliveryEmail: true,
      deliveryDashboard: true,
      emailRecipients: '',
      formatPDF: true,
      formatExcel: false,
      formatCSV: false,

      // View mode
      viewMode: 'single',
      showShareReportDialog: false,

      // Share dialog properties
      shareMethod: '',
      shareEmailRecipients: '',
      shareMessage: '',
      shareableLink: 'https://example.com/report/12345',
      linkPermission: 'view',
      shareFormatPDF: true,
      shareFormatExcel: false
    };
  },
  computed: {
    revenueReportChartData() {
      return {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
          {
            label: 'Rental Revenue',
            data: [8240, 9850, 10780, 18900],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          },
          {
            label: 'Additional Services',
            data: [1250, 1480, 1620, 2840],
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            borderWidth: 2,
            fill: true
          },
          {
            label: 'Late Fees',
            data: [320, 410, 290, 860],
            borderColor: '#ed8936',
            backgroundColor: 'rgba(237, 137, 54, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    inventoryStatusData() {
      return {
        labels: ['Available', 'Rented', 'Maintenance', 'Reserved'],
        datasets: [
          {
            data: [80, 124, 18, 26],
            backgroundColor: [
              'rgba(72, 187, 120, 0.8)',
              'rgba(66, 153, 225, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ]
          }
        ]
      };
    },
    inventoryUtilizationData() {
      return {
        labels: ['Construction', 'Photography', 'Power', 'Events', 'Other'],
        datasets: [
          {
            label: 'Utilization %',
            data: [84, 82, 78, 72, 64],
            backgroundColor: 'rgba(66, 153, 225, 0.7)',
            borderColor: 'rgba(66, 153, 225, 1)',
            borderWidth: 1
          }
        ]
      };
    },
    rentalsDurationData() {
      return {
        labels: ['Short-term', 'Medium-term', 'Long-term'],
        datasets: [
          {
            data: [42, 58, 24],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)'
            ]
          }
        ]
      };
    },
    rentalsCustomerData() {
      return {
        labels: ['Business', 'Individual', 'Government', 'Non-profit'],
        datasets: [
          {
            data: [68, 42, 10, 4],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ]
          }
        ]
      };
    },
    customersSegmentData() {
      return {
        labels: ['Business', 'Individual', 'Government', 'Non-profit'],
        datasets: [
          {
            data: [112, 98, 27, 11],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ]
          }
        ]
      };
    },
    customersGrowthData() {
      return {
        labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [
          {
            label: 'New Customers',
            data: [18, 22, 14, 19, 25, 28],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          }
        ]
      };
    },
    canShare() {
      if (this.shareMethod === 'email') {
        return this.shareEmailRecipients.trim().length > 0;
      } else if (this.shareMethod === 'export') {
        return this.shareFormatPDF || this.shareFormatExcel;
      }
      return true;
    }
  },
  mounted() {
    // Load saved reports from localStorage
    this.loadSavedReports();
    
    // Click outside directive for dropdowns
    this.$root.$el.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    // Remove event listeners
    this.$root.$el.removeEventListener('click', this.handleClickOutside);
  },
  directives: {
    'click-outside': {
      bind(el, binding, vnode) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            vnode.context[binding.expression](event);
          }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
      },
      unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  },
  methods: {
    generateReport() {
      // Show loading state
      this.reportGenerated = false;
      this.apiStatus.loading = true;
      this.apiStatus.error = null;
      
      // Log API request for debugging
      if (this.debugMode) {
        console.log(`📊 Generating ${this.selectedReportType} report`);
        console.log(`📅 Date range: ${this.startDate} to ${this.endDate}`);
      }
      
      // Fetch report data based on the selected report type
      if (this.selectedReportType === 'revenue') {
        this.fetchRevenueReportData();
      } else if (this.selectedReportType === 'inventory') {
        this.fetchInventoryReportData();
      } else if (this.selectedReportType === 'rentals') {
        this.fetchRentalsReportData();
      } else if (this.selectedReportType === 'customers') {
        this.fetchCustomersReportData();
      }
    },
    
    fetchRevenueReportData() {
      if (this.debugMode) {
        console.log('🔍 Fetching revenue report data...');
      }
      
      // Create a promise array for all revenue-related API calls
      const promises = [
        analyticsService.getRevenueAnalysisData()
          .catch(error => {
            console.error('Revenue Analysis API error:', error);
            // Return mock data structure in case of API failure
            return { 
              data: { 
                summary: this.revenueSummary, 
                periodicData: this.revenueTableData,
                totals: this.revenueTotals
              } 
            };
          }),
        analyticsService.getRevenueChartData()
          .catch(error => {
            console.error('Revenue Chart API error:', error);
            // Return mock data structure
            return { 
              data: { 
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: this.revenueReportChartData.datasets.map(dataset => ({
                  ...dataset,
                  sourceId: dataset.label.toLowerCase().replace(' ', '-')
                }))
              } 
            };
          }),
        analyticsService.getRevenueByCategoryData()
          .catch(error => {
            console.error('Revenue by Category API error:', error);
            return { data: null };
          }),
        analyticsService.getRevenueByLocationData()
          .catch(error => {
            console.error('Revenue by Location API error:', error);
            return { data: null };
          })
      ];
      
      // Execute all API calls in parallel
      Promise.all(promises)
        .then(([analysisData, chartData, categoryData, locationData]) => {
          if (this.debugMode) {
            console.log('✅ Revenue API calls completed');
            console.log('📊 Analysis data:', analysisData);
            console.log('📈 Chart data:', chartData);
          }
          
          try {
            // Update the revenue summary data
            if (analysisData.data && analysisData.data.summary) {
              this.revenueSummary = analysisData.data.summary;
            } else {
              console.warn('Invalid or missing revenue summary data format');
            }
            
            // Update revenue table data
            if (analysisData.data && analysisData.data.periodicData) {
              this.revenueTableData = analysisData.data.periodicData;
            } else {
              console.warn('Invalid or missing revenue periodic data format');
            }
            
            // Update revenue totals
            if (analysisData.data && analysisData.data.totals) {
              this.revenueTotals = analysisData.data.totals;
            } else {
              console.warn('Invalid or missing revenue totals data format');
            }
            
            // Update chart data with selected sources
            if (chartData.data && chartData.data.datasets) {
              const chartDatasets = chartData.data.datasets.filter(dataset => {
                // Filter datasets based on selected revenue sources
                const sourceId = dataset.sourceId;
                const source = this.revenueSources.find(s => s.id === sourceId);
                return source && source.selected;
              });
              
              this.revenueReportChartData = {
                labels: chartData.data.labels || ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: chartDatasets
              };
            } else {
              console.warn('Invalid or missing chart data format');
            }
            
            // Use categoryData and locationData if available
            if (categoryData && categoryData.data) {
              console.log('Revenue by category data available:', categoryData.data);
            }
            
            if (locationData && locationData.data) {
              console.log('Revenue by location data available:', locationData.data);
              
              // Filter by selected locations if location data is available
              if (locationData.data.locations) {
                const filteredLocations = locationData.data.locations.filter(loc => {
                  return this.locations.some(l => l.selected && l.id === loc.id);
                });
                console.log('Filtered locations:', filteredLocations);
              }
            }
            
            // Mark report as generated
            this.reportGenerated = true;
            this.apiStatus.loading = false;
            
          } catch (error) {
            console.error('Error processing revenue data:', error);
            // Still show the report with available data
            this.reportGenerated = true;
            this.apiStatus.loading = false;
            this.apiStatus.error = 'Error processing data: ' + error.message;
          }
        })
        .catch(error => {
          console.error("Error fetching revenue report data:", error);
          
          // Still show the report with default data
          this.reportGenerated = true;
          this.apiStatus.loading = false;
          this.apiStatus.error = 'API connection failed: ' + error.message;
          
          // Show error notification
          this.$emit('notify', {
            type: 'error',
            message: 'Failed to fetch revenue report data. Using cached data instead.'
          });
        });
    },
    
    fetchInventoryReportData() {
      // Create a promise array for all inventory-related API calls
      const promises = [
        analyticsService.getInventoryStatusData(),
        analyticsService.getUtilizationRateData(),
        analyticsService.getTopPerformingItemsData()
      ];
      
      // Add low stock items data if that filter is selected
      if (this.inventoryFilter === 'low-stock') {
        promises.push(analyticsService.getLowStockItemsData());
      }
      
      // Execute all API calls in parallel
      Promise.all(promises)
        .then(responses => {
          const statusData = responses[0].data;
          const utilizationData = responses[1].data;
          const topPerformingData = responses[2].data;
          
          // Update inventory summary data
          this.inventorySummary = statusData.summary;
          
          // Update inventory status chart data
          this.inventoryStatusData = {
            labels: statusData.chart.labels,
            datasets: statusData.chart.datasets
          };
          
          // Update inventory utilization chart data
          this.inventoryUtilizationData = {
            labels: utilizationData.chart.labels,
            datasets: utilizationData.chart.datasets.filter(dataset => {
              // Filter datasets based on selected categories
              return this.itemCategories.some(c => c.selected && c.id === dataset.categoryId);
            })
          };
          
          // Update inventory table data
          this.inventoryTableData = topPerformingData.items.filter(item => {
            // Filter items based on selected categories
            return this.itemCategories.some(c => c.selected && c.id === item.categoryId);
          });
          
          // Mark report as generated
          this.reportGenerated = true;
        })
        .catch(error => {
          console.error("Error fetching inventory report data:", error);
          // Show error notification
          this.$emit('notify', {
            type: 'error',
            message: 'Failed to fetch inventory report data'
          });
        });
    },
    
    fetchRentalsReportData() {
      // Create a promise array for rental-related API calls
      const promises = [
        analyticsService.getRentalTypeDistributionData(),
        analyticsService.getTopRentalItemsData()
      ];
      
      // Execute all API calls in parallel
      Promise.all(promises)
        .then(([typeDistribution, topRentals]) => {
          // Update rentals summary data
          this.rentalsSummary = typeDistribution.data.summary;
          
          // Update rentals duration chart data
          this.rentalsDurationData = {
            labels: typeDistribution.data.durationChart.labels,
            datasets: typeDistribution.data.durationChart.datasets
          };
          
          // Update rentals customer type chart data
          this.rentalsCustomerData = {
            labels: typeDistribution.data.customerChart.labels,
            datasets: typeDistribution.data.customerChart.datasets
          };
          
          // Update rentals table data
          this.rentalsTableData = topRentals.data.rentals.filter(rental => {
            // Filter by selected rental status
            const statusMatch = this.rentalStatuses.some(s => s.selected && s.id === rental.statusId);
            
            // Filter by selected customer type
            const typeMatch = this.customerTypes.some(t => t.selected && t.id === rental.customerTypeId);
            
            // Filter by rental duration
            let durationMatch = true;
            if (this.rentalDuration !== 'all') {
              const days = rental.durationDays;
              if (this.rentalDuration === 'short' && days > 7) durationMatch = false;
              if (this.rentalDuration === 'medium' && (days < 8 || days > 30)) durationMatch = false;
              if (this.rentalDuration === 'long' && days <= 30) durationMatch = false;
            }
            
            return statusMatch && typeMatch && durationMatch;
          });
          
          // Mark report as generated
          this.reportGenerated = true;
        })
        .catch(error => {
          console.error("Error fetching rentals report data:", error);
          // Show error notification
          this.$emit('notify', {
            type: 'error',
            message: 'Failed to fetch rentals report data'
          });
        });
    },
    
    fetchCustomersReportData() {
      // Create a promise array for customer-related API calls
      const promises = [
        analyticsService.getCustomerSegmentData(),
        analyticsService.getCustomerAcquisitionData(),
        analyticsService.getTopCustomersData(),
        analyticsService.getCustomerRetentionData()
      ];
      
      // Execute all API calls in parallel
      Promise.all(promises)
        .then(([segmentData, acquisitionData, topCustomers, retentionData]) => {
          // Update customers summary data
          this.customersSummary = {
            total: segmentData.data.totalCustomers,
            active: segmentData.data.activeCustomers,
            new: acquisitionData.data.newCustomersCurrentPeriod,
            avgLTV: topCustomers.data.averageLifetimeValue
          };
          
          // Update customers segment chart data
          this.customersSegmentData = {
            labels: segmentData.data.chart.labels,
            datasets: segmentData.data.chart.datasets
          };
          
          // Update customers growth chart data
          this.customersGrowthData = {
            labels: acquisitionData.data.chart.labels,
            datasets: acquisitionData.data.chart.datasets
          };
          
          // Update customers table data
          this.customersTableData = topCustomers.data.customers.filter(customer => {
            // Filter by customer segment
            const segmentMatch = this.customerSegments.some(s => s.selected && s.id === customer.segmentId);
            
            // Filter by activity level
            let activityMatch = true;
            if (this.customerActivity === 'active') {
              activityMatch = customer.isActive;
            } else if (this.customerActivity === 'inactive') {
              activityMatch = !customer.isActive;
            }
            
            return segmentMatch && activityMatch;
          });
          
          // Sort customers based on selected sort option
          if (this.customerSort === 'lifetime-value') {
            this.customersTableData.sort((a, b) => b.lifetimeValue - a.lifetimeValue);
          } else if (this.customerSort === 'rental-count') {
            this.customersTableData.sort((a, b) => b.rentals - a.rentals);
          } else if (this.customerSort === 'recent') {
            this.customersTableData.sort((a, b) => new Date(b.lastRental) - new Date(a.lastRental));
          } else if (this.customerSort === 'alphabetical') {
            this.customersTableData.sort((a, b) => a.name.localeCompare(b.name));
          }
          
          // Process retention data if needed
          if (retentionData && retentionData.data) {
            console.log('Customer retention data available:', retentionData.data);
            // You could use this for a customer retention chart or metrics
            const retentionRate = retentionData.data.retentionRate;
            console.log(`Overall customer retention rate: ${retentionRate}%`);
          }
          
          // Mark report as generated
          this.reportGenerated = true;
        })
        .catch(error => {
          console.error("Error fetching customers report data:", error);
          // Show error notification
          this.$emit('notify', {
            type: 'error',
            message: 'Failed to fetch customers report data'
          });
        });
    },
    
    exportPDF() {
      alert('Exporting PDF report...');
      // Implementation would connect to a PDF generation library
    },
    exportCSV() {
      alert('Exporting CSV report...');
      // Implementation would generate and download a CSV file
    },
    exportExcel() {
      alert('Exporting Excel report...');
      // Implementation would generate and download an Excel file
    },
    formatNumber(value) {
      return value.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    },
    formatDateRange() {
      const start = new Date(this.startDate).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
      const end = new Date(this.endDate).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
      return `${start} - ${end}`;
    },
    getReportTitle() {
      const type = this.reportTypes.find(t => t.id === this.selectedReportType);
      return type ? type.name : 'Custom Report';
    },
    getReportTypeName(typeId) {
      const type = this.reportTypes.find(t => t.id === typeId);
      return type ? type.name : 'Custom Report';
    },
    closeSavedReportsDropdown() {
      this.showSavedReportsDropdown = false;
    },
    toggleSavedReportsDropdown() {
      this.showSavedReportsDropdown = !this.showSavedReportsDropdown;
    },
    resetFilters() {
      // Reset all filters to their default state
      this.revenueSources.forEach(source => source.selected = true);
      this.locations.forEach(location => location.selected = true);
      this.revenueGrouping = 'weekly';
      this.revenueChartType = 'line';
      
      this.itemCategories.forEach(category => category.selected = true);
      this.inventoryStatuses.forEach(status => status.selected = true);
      this.inventoryFilter = 'all';
      
      this.rentalStatuses.forEach(status => status.selected = true);
      this.customerTypes.forEach(type => type.selected = true);
      this.rentalDuration = 'all';
      
      this.customerSegments.forEach(segment => segment.selected = true);
      this.customerActivity = 'all';
      this.customerSort = 'lifetime-value';
      
      // Re-generate the report if one was already generated
      if (this.reportGenerated) {
        this.generateReport();
      }
    },
    loadSavedReports() {
      const savedReports = localStorage.getItem('savedReports');
      if (savedReports) {
        this.savedReports = JSON.parse(savedReports);
      }
    },
    saveReport() {
      if (!this.newReportName) return;
      
      const report = {
        name: this.newReportName,
        description: this.newReportDescription,
        type: this.selectedReportType,
        dateRange: {
          start: this.startDate,
          end: this.endDate
        },
        filters: this.getReportFilters(),
        createdAt: new Date().toISOString()
      };
      
      this.savedReports.push(report);
      localStorage.setItem('savedReports', JSON.stringify(this.savedReports));
      
      this.showSaveReportDialog = false;
      this.newReportName = '';
      this.newReportDescription = '';
      
      // Show success notification
      this.$emit('notify', {
        type: 'success',
        message: `Report "${report.name}" saved successfully`
      });
    },
    deleteSavedReport(index) {
      const reportName = this.savedReports[index].name;
      this.savedReports.splice(index, 1);
      localStorage.setItem('savedReports', JSON.stringify(this.savedReports));
      
      // Show success notification
      this.$emit('notify', {
        type: 'success',
        message: `Report "${reportName}" deleted`
      });
    },
    loadSavedReport(report) {
      this.selectedReportType = report.type;
      this.startDate = report.dateRange.start;
      this.endDate = report.dateRange.end;
      
      // Apply filters based on report type
      this.applyReportFilters(report.filters);
      
      // Generate the report
      this.generateReport();
      
      // Close the dropdown
      this.showSavedReportsDropdown = false;
      
      // Show success notification
      this.$emit('notify', {
        type: 'info',
        message: `Loaded report "${report.name}"`
      });
    },
    getReportFilters() {
      // Get current filters based on report type
      switch (this.selectedReportType) {
        case 'revenue':
          return {
            sources: this.revenueSources.map(s => ({ id: s.id, selected: s.selected })),
            locations: this.locations.map(l => ({ id: l.id, selected: l.selected })),
            grouping: this.revenueGrouping,
            chartType: this.revenueChartType
          };
        case 'inventory':
          return {
            categories: this.itemCategories.map(c => ({ id: c.id, selected: c.selected })),
            statuses: this.inventoryStatuses.map(s => ({ id: s.id, selected: s.selected })),
            filter: this.inventoryFilter
          };
        case 'rentals':
          return {
            statuses: this.rentalStatuses.map(s => ({ id: s.id, selected: s.selected })),
            customerTypes: this.customerTypes.map(t => ({ id: t.id, selected: t.selected })),
            duration: this.rentalDuration
          };
        case 'customers':
          return {
            segments: this.customerSegments.map(s => ({ id: s.id, selected: s.selected })),
            activity: this.customerActivity,
            sort: this.customerSort
          };
        default:
          return {};
      }
    },
    applyReportFilters(filters) {
      // Apply filters based on report type
      switch (this.selectedReportType) {
        case 'revenue':
          this.applyFilterSelection(this.revenueSources, filters.sources);
          this.applyFilterSelection(this.locations, filters.locations);
          this.revenueGrouping = filters.grouping || 'weekly';
          this.revenueChartType = filters.chartType || 'line';
          break;
        case 'inventory':
          this.applyFilterSelection(this.itemCategories, filters.categories);
          this.applyFilterSelection(this.inventoryStatuses, filters.statuses);
          this.inventoryFilter = filters.filter || 'all';
          break;
        case 'rentals':
          this.applyFilterSelection(this.rentalStatuses, filters.statuses);
          this.applyFilterSelection(this.customerTypes, filters.customerTypes);
          this.rentalDuration = filters.duration || 'all';
          break;
        case 'customers':
          this.applyFilterSelection(this.customerSegments, filters.segments);
          this.customerActivity = filters.activity || 'all';
          this.customerSort = filters.sort || 'lifetime-value';
          break;
      }
    },
    applyFilterSelection(targetArray, sourceArray) {
      if (!sourceArray) return;
      
      targetArray.forEach(item => {
        const sourceItem = sourceArray.find(s => s.id === item.id);
        if (sourceItem) {
          item.selected = sourceItem.selected;
        }
      });
    },
    scheduleReport() {
      this.showScheduleDialog = true;
    },
    saveSchedule() {
      // In a real application, this would be saved to the server
      // For now, we'll just show a notification
      
      this.showScheduleDialog = false;
      
      // Show success notification
      this.$emit('notify', {
        type: 'success',
        message: `Report scheduled successfully. It will run ${this.scheduleFrequency}.`
      });
    },
    shareViaEmail() {
      this.shareMethod = 'email';
    },
    shareViaLink() {
      this.shareMethod = 'link';
      // In a real application, you would generate a unique shareable link
      this.shareableLink = `https://example.com/report/${Date.now()}`;
    },
    exportAndShare() {
      this.shareMethod = 'export';
    },
    copyLink() {
      // In a real application, this would copy the link to the clipboard
      navigator.clipboard.writeText(this.shareableLink)
        .then(() => {
          alert('Link copied to clipboard');
        })
        .catch(err => {
          console.error('Failed to copy link: ', err);
        });
    },
    submitShare() {
      // Handle the share action based on the selected method
      if (this.shareMethod === 'email') {
        // In a real application, this would send the report via email
        console.log('Sharing report via email to:', this.shareEmailRecipients);
        console.log('Message:', this.shareMessage);
      } else if (this.shareMethod === 'link') {
        // The link has already been generated
        console.log('Sharing report via link with permission:', this.linkPermission);
      } else if (this.shareMethod === 'export') {
        // Export the report in the selected formats
        if (this.shareFormatPDF) {
          this.exportPDF();
        }
        if (this.shareFormatExcel) {
          this.exportExcel();
        }
      }
      
      this.showShareReportDialog = false;
      this.$nextTick(() => {
        alert('Report shared successfully');
      });
    }
  }
};
</script>

<style scoped>
.reports-page {
  padding: 18px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header-content {
  display: flex;
  flex-direction: column;
}

.actions {
  display: flex;
  gap: 12px;
}

.view-toggle {
  display: flex;
  gap: 8px;
}

.view-btn {
  padding: 8px 12px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.view-btn.active {
  background-color: #3182ce;
  color: white;
}

.view-btn:hover {
  background-color: #edf2f7;
}

.saved-reports-dropdown {
  position: relative;
}

.saved-reports-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.saved-reports-btn:hover {
  background-color: #edf2f7;
}

.saved-reports-menu {
  position: absolute;
  top: 100%;
  right: 0;
  width: 280px;
  max-height: 320px;
  overflow-y: auto;
  background-color: white;
  border-radius: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
  margin-top: 4px;
}

.no-saved-reports {
  padding: 16px;
  text-align: center;
  color: #718096;
}

.saved-report-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #e2e8f0;
  cursor: pointer;
}

.saved-report-item:hover {
  background-color: #f8fafc;
}

.saved-report-item:last-child {
  border-bottom: none;
}

.report-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.report-name {
  font-weight: 500;
  color: #2d3748;
}

.report-type {
  font-size: 12px;
  color: #718096;
}

.delete-report-btn {
  padding: 4px;
  border: none;
  background: none;
  color: #a0aec0;
  cursor: pointer;
  border-radius: 4px;
}

.delete-report-btn:hover {
  color: #e53e3e;
  background-color: rgba(229, 62, 62, 0.1);
}

.save-report-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.save-report-btn:hover {
  background-color: #3182ce;
}

.share-report-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background-color: #48bb78;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.share-report-btn:hover {
  background-color: #38a169;
}

.report-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
  padding: 16px;
  background-color: #f8fafc;
  border-radius: 6px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.report-selector, .date-range-picker {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.report-selector {
  width: 200px;
}

.date-range-picker {
  flex-grow: 1;
}

.date-inputs {
  display: flex;
  gap: 12px;
}

.date-input {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

select, input[type="date"] {
  padding: 8px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  background-color: white;
}

.generate-btn {
  align-self: flex-end;
  padding: 8px 16px;
  background-color: #3182ce;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.generate-btn:hover {
  background-color: #2c5282;
}

.reports-content {
  display: flex;
  gap: 24px;
}

.report-filters {
  flex: 0 0 280px;
  padding: 16px;
  background-color: #f8fafc;
  border-radius: 6px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.reset-filters-btn {
  padding: 4px 8px;
  background-color: transparent;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  color: #718096;
  cursor: pointer;
}

.reset-filters-btn:hover {
  background-color: #edf2f7;
  color: #4a5568;
}

.filter-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.checkbox-group, .radio-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.checkbox-item, .radio-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.report-preview {
  flex: 1;
  min-width: 0;
  padding: 16px;
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.preview-content {
  margin-top: 16px;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e2e8f0;
}

.no-report-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 48px;
  color: #718096;
  text-align: center;
}

.message-icon {
  font-size: 48px;
  color: #cbd5e0;
}

.export-options {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
}

.export-btn {
  padding: 8px 16px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  color: #4a5568;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.export-btn:hover {
  background-color: #edf2f7;
}

/* Dialog styles */
.dialog-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}

.dialog-content {
  background-color: white;
  border-radius: 6px;
  padding: 24px;
  width: 480px;
  max-width: 90%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.dialog-content h3 {
  margin-top: 0;
  margin-bottom: 16px;
  color: #2d3748;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
}

.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
}

.form-group textarea {
  min-height: 80px;
  resize: vertical;
}

.dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.cancel-btn {
  padding: 8px 16px;
  background-color: white;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  color: #4a5568;
  cursor: pointer;
}

.cancel-btn:hover {
  background-color: #f8fafc;
}

.save-btn {
  padding: 8px 16px;
  background-color: #3182ce;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.save-btn:hover {
  background-color: #2c5282;
}

.save-btn:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
}

.schedule-options {
  flex-direction: row;
  gap: 16px;
}

.share-options {
  display: flex;
  gap: 12px;
}

.share-option {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  cursor: pointer;
}

.share-option:hover {
  background-color: #edf2f7;
}

.copy-link-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.copy-btn {
  padding: 8px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  cursor: pointer;
}

.copy-btn:hover {
  background-color: #edf2f7;
}

.share-btn {
  padding: 8px 16px;
  background-color: #48bb78;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.share-btn:hover {
  background-color: #38a169;
}
</style>