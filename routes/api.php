<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin Module Web Services API Routes
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\AdminApiController::class, 'getDashboardStats']);
    
    // Users
    Route::get('/users', [App\Http\Controllers\Api\AdminApiController::class, 'getUsers']);
    Route::get('/users/{id}', [App\Http\Controllers\Api\AdminApiController::class, 'getUserById']);
    Route::put('/users/{id}/status', [App\Http\Controllers\Api\AdminApiController::class, 'updateUserStatus']);
    Route::get('/users/stats', [App\Http\Controllers\Api\AdminApiController::class, 'getUserStats']);
    
    // Trains
    Route::get('/trains', [App\Http\Controllers\Api\AdminApiController::class, 'getTrains']);
    Route::post('/trains', [App\Http\Controllers\Api\AdminApiController::class, 'addTrain']);
    
    // QR Operations
    Route::post('/qr/scan', [App\Http\Controllers\Api\AdminApiController::class, 'scanQR']);
    Route::post('/qr/generate', [App\Http\Controllers\Api\AdminApiController::class, 'generateQR']);
    
    // Newsletter
    Route::post('/newsletter/send', [App\Http\Controllers\Api\AdminApiController::class, 'sendNewsletter']);
    Route::get('/newsletter/stats', [App\Http\Controllers\Api\AdminApiController::class, 'getNewsletterStats']);
    
    // Refunds
    Route::post('/refunds/process', [App\Http\Controllers\Api\AdminApiController::class, 'processRefund']);
    
    // System
    Route::get('/system/info', [App\Http\Controllers\Api\AdminApiController::class, 'getSystemInfo']);
    Route::get('/health', [App\Http\Controllers\Api\AdminApiController::class, 'healthCheck']);
});

// User Module Web Services API Routes (for other modules to consume)
Route::prefix('user')->group(function () {
    // User management endpoints that other modules can consume
    Route::get('/stats', function () {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_users' => \App\Models\User::count(),
                'active_users' => \App\Models\User::where('account_status', 'active')->count(),
                'social_users' => \App\Models\User::whereNotNull('social_provider')->count(),
            ],
            'message' => 'User statistics retrieved successfully',
            'timestamp' => now()->toISOString()
        ]);
    });
    
    Route::get('/list', function (Request $request) {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        
        $query = \App\Models\User::query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage()
            ],
            'message' => 'Users retrieved successfully',
            'timestamp' => now()->toISOString()
        ]);
    });
    
    Route::get('/{id}', function ($id) {
        $user = \App\Models\User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'timestamp' => now()->toISOString()
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'message' => 'User retrieved successfully',
            'timestamp' => now()->toISOString()
        ]);
    });
    
    Route::put('/{id}/status', function (Request $request, $id) {
        $request->validate([
            'status' => 'required|string|in:active,inactive,suspended,pending_verification'
        ]);
        
        $user = \App\Models\User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'timestamp' => now()->toISOString()
            ], 404);
        }
        
        $user->account_status = $request->status;
        $user->save();
        
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'message' => 'User status updated successfully',
            'timestamp' => now()->toISOString()
        ]);
    });
    
    Route::get('/health', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'User API is healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    });
}); 