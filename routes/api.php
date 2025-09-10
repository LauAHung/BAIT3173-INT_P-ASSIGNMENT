<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminWebServiceController;
use App\Http\Controllers\Api\UserWebServiceController;
use App\Http\Controllers\Api\ConcessionCardWebServiceController;
use App\Http\Controllers\Api\BookingApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User Module Web Services
Route::prefix('user')->group(function () {
    // Authentication endpoints
    Route::post('/login', [UserWebServiceController::class, 'login']);
    Route::post('/register', [UserWebServiceController::class, 'register']);
    Route::post('/forgot-password', [UserWebServiceController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserWebServiceController::class, 'resetPassword']);
    
    // Profile endpoints
    Route::get('/profile/{userId}', [UserWebServiceController::class, 'getProfile']);
    Route::put('/profile/{userId}', [UserWebServiceController::class, 'updateProfile']);
});

// Admin Module Web Services
Route::prefix('admin')->group(function () {
    // User management
    Route::get('/user/{userId}', [AdminWebServiceController::class, 'getUserInfo']);
    
    // Concession card management
    Route::get('/concession/{applicationId}', [AdminWebServiceController::class, 'getConcessionApplication']);
    
    // Train management
    Route::get('/train/{trainId}', [AdminWebServiceController::class, 'getTrainInfo']);
    
    // Admin logs
    Route::get('/logs', [AdminWebServiceController::class, 'getAdminLogs']);
    
    // Newsletter management
    Route::get('/newsletter/subscribers', [AdminWebServiceController::class, 'getNewsletterSubscribers']);
});

// Concession Card Module Web Services
Route::prefix('concession')->group(function () {
    // User applications
    Route::get('/user/{userId}', [ConcessionCardWebServiceController::class, 'getUserApplications']);
    
    // Application management
    Route::get('/application/{applicationId}', [ConcessionCardWebServiceController::class, 'getApplicationDetails']);
    Route::post('/application', [ConcessionCardWebServiceController::class, 'submitApplication']);
    
    // Statistics
    Route::get('/statistics', [ConcessionCardWebServiceController::class, 'getStatistics']);
});

// Friend's Booking Module Web Services
Route::prefix('bookings')->group(function () {
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::get('/bookings_detail/{id}', [BookingApiController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingApiController::class, 'cancel']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});