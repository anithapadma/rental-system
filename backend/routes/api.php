<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SettingsController;
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