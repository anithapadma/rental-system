<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\EmployeeTaskController;
use App\Http\Controllers\Api\EmployeeDashboardController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\AgreementController;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/cors-test', function (Request $request) {
    return response()->json([
        'message' => 'CORS is working correctly!',
        'origin' => $request->header('Origin'),
        'method' => $request->method()
    ]);
});
// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Analytics routes
Route::get('/analytics/overview', [AnalyticsController::class, 'overview']);
Route::get('/analytics/revenue/chart', [AnalyticsController::class, 'revenueChart']);
Route::get('/analytics/rentals/types', [AnalyticsController::class, 'rentalTypeDistribution']);
Route::get('/analytics/rentals/top-items', [AnalyticsController::class, 'topRentalItems']);
Route::get('/analytics/revenue', [AnalyticsController::class, 'revenueAnalysis']);
Route::get('/analytics/revenue/categories', [AnalyticsController::class, 'revenueByCategory']);
Route::get('/analytics/revenue/locations', [AnalyticsController::class, 'revenueByLocation']);
Route::get('/analytics/revenue/average-value', [AnalyticsController::class, 'averageRentalValue']);

// Inventory analysis routes
Route::get('/analytics/inventory/status', [AnalyticsController::class, 'inventoryStatus']);
Route::get('/analytics/inventory/low-stock', [AnalyticsController::class, 'lowStockItems']);
Route::get('/analytics/inventory/utilization', [AnalyticsController::class, 'utilizationRate']);
Route::get('/analytics/inventory/top-performing', [AnalyticsController::class, 'topPerformingItems']);

// Customer analysis routes
Route::get('/analytics/customers/acquisition', [AnalyticsController::class, 'customerAcquisition']);
Route::get('/analytics/customers/segments', [AnalyticsController::class, 'customerSegment']);
Route::get('/analytics/customers/top', [AnalyticsController::class, 'topCustomers']);
Route::get('/analytics/customers/retention', [AnalyticsController::class, 'customerRetention']);

// Rental routes
Route::get('/rentals', [RentalController::class, 'index']);
Route::post('/rentals', [RentalController::class, 'store']);
Route::get('/rentals/{id}', [RentalController::class, 'show']);
Route::put('/rentals/{id}', [RentalController::class, 'update']);
Route::delete('/rentals/{id}', [RentalController::class, 'destroy']);
Route::patch('/rentals/{id}/status', [RentalController::class, 'updateStatus']);
Route::post('/rentals/{id}/return', [RentalController::class, 'returnRental']);

// Inventory routes
Route::get('/inventory', [InventoryController::class, 'index']);
Route::post('/inventory', [InventoryController::class, 'store']);
Route::get('/inventory/{id}', [InventoryController::class, 'show']);
Route::put('/inventory/{id}', [InventoryController::class, 'update']);
Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);
Route::patch('/inventory/{id}/status', [InventoryController::class, 'updateStatus']);

// Employee Inventory routes
Route::get('/employee-inventory', [InventoryController::class, 'employeeInventory']);
Route::post('/inventory/{id}/assign', [InventoryController::class, 'assignToEmployee']);
Route::post('/inventory/{id}/return', [InventoryController::class, 'returnFromEmployee']);

// Agreement routes
Route::get('/agreements', [AgreementController::class, 'index']);
Route::post('/agreements', [AgreementController::class, 'store']);
Route::get('/agreements/{id}', [AgreementController::class, 'show']);
Route::put('/agreements/{id}', [AgreementController::class, 'update']);
Route::delete('/agreements/{id}', [AgreementController::class, 'destroy']);
Route::patch('/agreements/{id}/status', [AgreementController::class, 'updateStatus']);
Route::get('/agreements/{id}/pdf', [AgreementController::class, 'generatePdf']);

// Category routes
Route::get('/settings/categories', [CategoryController::class, 'index']);
Route::post('/settings/categories', [CategoryController::class, 'store']);
Route::get('/settings/categories/{id}', [CategoryController::class, 'show']);
Route::put('/settings/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/settings/categories/{id}', [CategoryController::class, 'destroy']);

// Settings routes
Route::get('/settings', [SettingsController::class, 'index']);
Route::put('/settings/company', [SettingsController::class, 'updateCompanyInfo']);
Route::put('/settings/rental', [SettingsController::class, 'updateRentalSettings']);

// Employee Task routes
Route::get('/employee/tasks', [EmployeeTaskController::class, 'index']);
Route::get('/employee/tasks/{id}', [EmployeeTaskController::class, 'show']);
Route::patch('/employee/tasks/{id}/status', [EmployeeTaskController::class, 'updateStatus']);
Route::post('/employee/tasks/{id}/complete', [EmployeeTaskController::class, 'complete']);

// Employee Dashboard routes
Route::get('/employee/dashboard/all', [EmployeeDashboardController::class, 'getAll']);
Route::get('/employee/dashboard/tasks', [EmployeeDashboardController::class, 'getTasks']);
Route::get('/employee/dashboard/schedule', [EmployeeDashboardController::class, 'getSchedule']);
Route::get('/employee/dashboard/rentals', [EmployeeDashboardController::class, 'getRentals']);
Route::get('/employee/dashboard/performance', [EmployeeDashboardController::class, 'getPerformance']);
Route::get('/employee/dashboard/task-distribution', [EmployeeDashboardController::class, 'getTaskDistribution']);
Route::get('/employee/dashboard/rental-stats', [EmployeeDashboardController::class, 'getRentalStats']);
Route::get('/employee/dashboard/team', [EmployeeDashboardController::class, 'getTeam']);

// Debug routes
Route::get('/debug/categories', function () {
    try {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'count' => $categories->count(),
            'categories' => $categories
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching categories: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/debug/category/{id}', function ($id) {
    try {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found with ID: ' . $id
            ], 404);
        }
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error finding category: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/debug/delete-category/{id}', function ($id) {
    try {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found with ID: ' . $id
            ], 404);
        }
        
        $name = $category->name;
        $result = $category->delete();
        
        return response()->json([
            'success' => true,
            'deleted' => $result,
            'message' => 'Category "' . $name . '" deleted successfully',
            'id' => $id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting category: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});