<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RentalController;

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