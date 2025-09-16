<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminWebServiceController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\UserWebServiceController;
use App\Http\Controllers\Api\ConcessionCardWebServiceController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\TrainApiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\NewsletterController;


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

    // Admin-use user management (open for now; can add middleware later)
    Route::get('/list', [UserWebServiceController::class, 'listUsers']);
    Route::put('/{userId}/status', [UserWebServiceController::class, 'updateUserStatus']);
    Route::delete('/{userId}', [UserWebServiceController::class, 'deleteUser']);
});


// Admin Module Web Services + Admin UI AJAX
Route::prefix('admin')->middleware(['web','admin','admin.2fa'])->group(function () {
    // User management
    Route::get('/user/{userId}', [AdminWebServiceController::class, 'getUserInfo']);
    
    // Concession card management
    Route::get('/concession/{applicationId}', [AdminWebServiceController::class, 'getConcessionApplication']);
    
    // Train management
    Route::get('/train/{trainId}', [AdminWebServiceController::class, 'getTrainInfo']);
    
    // Admin logs (deduped: handled by Admin\LogController below)

    // Journey details provider for Booking module
    Route::get('/journey/{journeyId}', [AdminWebServiceController::class, 'getJourneyById']);
    
    // Newsletter management
    Route::get('/newsletter/subscribers', [NewsletterController::class, 'list']);
    Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);

    // Admin UI AJAX endpoints (moved from web.php)
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/dashboard/filters', [AdminController::class, 'getDashboardFilters']);
    Route::get('/dashboard/trips', [AdminController::class, 'getTripsPerMonth']);
    Route::get('/dashboard/users-growth', [AdminController::class, 'getUsersGrowth']);
    Route::get('/dashboard/profit', [AdminController::class, 'getProfitTrends']);

    // Trains
    Route::get('/trains', [AdminController::class, 'getTrains']);
    Route::post('/trains', [AdminController::class, 'addTrain']);
    Route::put('/trains/{id}', [AdminController::class, 'updateTrain']);
    Route::delete('/trains/{id}', [AdminController::class, 'deleteTrain']);

    // QR
    Route::post('/qr/scan', [AdminController::class, 'scanQR']);
    Route::post('/qr/generate', [AdminController::class, 'generateQR']);

    // Tickets
    Route::get('/tickets/{ticketId}', [AdminTicketController::class, 'show']);
    Route::post('/tickets/{ticketId}/checkin', [AdminTicketController::class, 'checkIn']);
    Route::post('/tickets/{ticketId}/checkout', [AdminTicketController::class, 'checkOut']);

    // Newsletter send (use in-app API controller)
    Route::post('/newsletter/send', [AdminApiController::class, 'sendNewsletter']);
    // Refunds
    Route::post('/refunds/process', [AdminController::class, 'processRefund']);

    // Users management
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::put('/users/{userId}/status', [AdminUserController::class, 'updateStatus']);
    Route::delete('/users/{userId}', [AdminUserController::class, 'destroy']);
    Route::get('/users/export', [AdminUserController::class, 'export'])
        ->middleware(['admin.recent','throttle:1,2']);

    // Sensitive endpoints: throttle + recent admin reauth
    Route::middleware(['throttle:5,1','admin.recent'])->group(function () {
        Route::get('/export', [AdminController::class, 'exportData']);
        Route::get('/export/download', [AdminController::class, 'downloadExport']);
        Route::post('/refunds/process', [AdminController::class, 'processRefund']);
        Route::put('/trains/{id}', [AdminController::class, 'updateTrain']);
        Route::delete('/trains/{id}', [AdminController::class, 'deleteTrain']);
    });

    // System info and logs
    Route::get('/system/info', [AdminController::class, 'getSystemInfo']);
    Route::get('/logs', [AdminLogController::class, 'list']);

    // Concession decision publishing (Admin provides -> Concession consumes)
    Route::post('/concession/decision', [AdminWebServiceController::class, 'decideConcession']);
});

// Concession Card Module Web Services
Route::prefix('concession')->middleware('auth')->group(function () {
    // User applications
    Route::get('/user/{userId}', [ConcessionCardWebServiceController::class, 'getUserApplications']);
    
    // Application management
    Route::get('/application/{applicationId}', [ConcessionCardWebServiceController::class, 'getApplicationDetails']);
    Route::post('/application', [ConcessionCardWebServiceController::class, 'submitApplication']);
    
    // Admin approval routes
    Route::get('/applications', [ConcessionCardWebServiceController::class, 'getAllApplications']);
    Route::get('/applications/{applicationId}', [ConcessionCardWebServiceController::class, 'getApplicationDetails']);
    Route::post('/applications/{applicationId}/approve', [ConcessionCardWebServiceController::class, 'approveApplication']);
    Route::post('/applications/{applicationId}/reject', [ConcessionCardWebServiceController::class, 'rejectApplication']);
    
    // Statistics
    Route::get('/statistics', [ConcessionCardWebServiceController::class, 'getStatistics']);
});



// Normalized Booking APIs (explicit user and booking params)
Route::get('/bookings/{userId}', [BookingApiController::class, 'index']);
Route::get('/booking/{bookingId}/{userId}', [BookingApiController::class, 'show']);
Route::patch('/booking/cancel/{bookingId}/{userId}', [BookingApiController::class, 'cancel']);

Route::get('/journeys', [TrainApiController::class, 'getJourney']);
Route::get('/journeys/return', [TrainApiController::class, 'getJourneyReturn']);
Route::post('/journeys/passenger-info', [TrainApiController::class, 'showPassengerInfo']);
Route::post('/journeys/passenger-info/store', [TrainApiController::class, 'storePassengerInfo']);
Route::post('/bookings', [TrainApiController::class, 'storeBooking']);

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});

// Ticket APIs for QR scan/check-in/out (Booking module)
Route::get('/tickets/{ticketId}', [AdminTicketController::class, 'show']);
Route::post('/tickets/{ticketId}/checkin', [AdminTicketController::class, 'checkIn']);
Route::post('/tickets/{ticketId}/checkout', [AdminTicketController::class, 'checkOut']);
