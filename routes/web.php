<?php
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TrainSelectionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingDetailController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('HomePage');
})->name('HomePage');

Route::get('/signup', function () {
    return view('SignUpPage');
})->middleware('guest')->name('signup');

// Login routes
Route::get('/signin', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('signin');
Route::post('/login', [LoginController::class, 'handleLogin'])->name('login.handle');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'handleForgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'handleResetPassword'])->name('password.update');

// Email verification
Route::get('/verify-email/{token}', [LoginController::class, 'verifyEmail'])->name('verification.verify');

// Debug route for testing email verification
Route::get('/debug/verify-email/{token}', function($token) {
    $user = \App\Models\User::where('email_verification_token', $token)->first();
    if ($user) {
        return response()->json([
            'found' => true,
            'user_id' => $user->user_id,
            'email' => $user->email,
            'token' => $user->email_verification_token,
            'status' => $user->account_status
        ]);
    } else {
        return response()->json([
            'found' => false,
            'token' => $token
        ]);
    }
})->name('debug.verification');

// Clear session messages
Route::post('/clear-session', [LoginController::class, 'clearSession'])->name('clear.session');

Route::get('/train_selection', function () {
    return view('TrainSelectionPage');
})->name('TrainSelectionPage');

Route::get('/seat_select', function () {
    return view('SelectSeatPage');
})->name('selectseat');

// Signup Page & Profile Page
Route::get('/signup', [SignupController::class, 'showForm'])
->middleware('guest')->name('signup');

Route::post('/signup', [SignupController::class, 'handleSignup'])
->name('signup.handle');

// Profile routes
Route::middleware('auth.required')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'showChangePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password.post');
    Route::post('/profile/email-subscription', [App\Http\Controllers\ProfileController::class, 'updateEmailSubscription'])->name('profile.email-subscription');
    Route::post('/profile/delete-account', [App\Http\Controllers\ProfileController::class, 'deleteAccount'])->name('profile.delete-account');
    Route::get('/profile/activity', [App\Http\Controllers\ProfileController::class, 'activity'])->name('profile.activity');
});

// Admin routes
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/trains', [App\Http\Controllers\AdminController::class, 'trains'])->name('admin.trains');
    Route::get('/qr-scanner', [App\Http\Controllers\AdminController::class, 'qrScanner'])->name('admin.qr-scanner');
    Route::get('/newsletter', [App\Http\Controllers\AdminController::class, 'newsletter'])->name('admin.newsletter');
    Route::get('/refunds', [App\Http\Controllers\AdminController::class, 'refunds'])->name('admin.refunds');
    Route::get('/system-info', [App\Http\Controllers\AdminController::class, 'systemInfo'])->name('admin.system-info');
    
    // API endpoints for AJAX
    Route::get('/api/dashboard/stats', [App\Http\Controllers\AdminController::class, 'getDashboardStats']);
    Route::get('/api/dashboard/filters', [App\Http\Controllers\AdminController::class, 'getDashboardFilters']);
    Route::get('/api/dashboard/trips', [App\Http\Controllers\AdminController::class, 'getTripsPerMonth']);
    Route::get('/api/dashboard/users-growth', [App\Http\Controllers\AdminController::class, 'getUsersGrowth']);
    Route::get('/api/dashboard/profit', [App\Http\Controllers\AdminController::class, 'getProfitTrends']);
    // Removed conflicting user management routes - using UserController instead
    Route::get('/api/trains', [App\Http\Controllers\AdminController::class, 'getTrains']);
    Route::post('/api/trains', [App\Http\Controllers\AdminController::class, 'addTrain']);
    Route::put('/api/trains/{id}', [App\Http\Controllers\AdminController::class, 'updateTrain']);
    Route::delete('/api/trains/{id}', [App\Http\Controllers\AdminController::class, 'deleteTrain']);
    Route::post('/api/qr/scan', [App\Http\Controllers\AdminController::class, 'scanQR']);
    Route::post('/api/qr/generate', [App\Http\Controllers\AdminController::class, 'generateQR']);
    // Ticket info + status updates for ScanQR page
    Route::get('/api/tickets/{ticketId}', [App\Http\Controllers\Admin\TicketController::class, 'show']);
    Route::post('/api/tickets/{ticketId}/checkin', [App\Http\Controllers\Admin\TicketController::class, 'checkIn']);
    Route::post('/api/tickets/{ticketId}/checkout', [App\Http\Controllers\Admin\TicketController::class, 'checkOut']);
    Route::post('/api/newsletter/send', [App\Http\Controllers\AdminController::class, 'sendNewsletter']);
    Route::post('/api/refunds/process', [App\Http\Controllers\AdminController::class, 'processRefund']);
    Route::get('/api/export', [App\Http\Controllers\AdminController::class, 'exportData']);
    Route::get('/api/system/info', [App\Http\Controllers\AdminController::class, 'getSystemInfo']);
    Route::get('/api/logs', [App\Http\Controllers\Admin\LogController::class, 'list']);
});

// Booking Module
Route::get('/train-selection', [TrainSelectionController::class, 'index'])->name('train.selection');
Route::get('/passengerinfo', [TrainSelectionController::class, 'showPassengerInfo'])->name('passengerinfo');
Route::post('/passenger-info/store', [TrainSelectionController::class, 'storePassengerInfo'])->name('store.passengerinfo');
Route::get('/selectseat/', [TrainSelectionController::class, 'showSelectSeat'])->name('selectseat');
Route::post('/booking/store', [TrainSelectionController::class, 'storeBooking'])->name('booking.store');

Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::get('/booking/cancel/{bookingId}', [BookingController::class, 'cancel'])->name('cancel');
Route::get('/booking/rate', [BookingController::class, 'rate'])->name('rateTrip');
Route::get('/booking_detail/{bookingId}', [BookingController::class, 'show'])->name('bookingdetail');

Route::get('/booking_detail', function () {
    return view('BookingDetailPage');
})->name('bookingdetails');

//Googleeee
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
->name('google.login');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Facebookkkkkk
Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'handleFacebookCallback'])->name('facebook.callback');

Route::get('/feedback', function () {
    return view('FeedbackPage');
})->name('feedback');

Route::get('/selectrating', function () {
    return view('SelectRatingPage');
})->name('selectrating');

Route::get('/ratingsection', function () {
    return view('RatingSectionPage');
})->name('ratingsection');

Route::get('/viewfeedback', function () {
    return view('ViewFeedbackPage');
})->name('viewfeedback');

Route::get('/payment', function () {
    return view('PaymentPage');
})->name('payment');

// Admin Page (protected)
Route::get('dashboard', function () {
    return view('AdminPage/Dashboard');
})->middleware('admin')->name('dashboard');

// Entry point: if user is admin, go dashboard; else 403
Route::get('/admin', function () {
    $user = Auth::user();
    if ($user && $user->account_status === 'admin') {
        return redirect()->route('dashboard');
    }
    return response()->view('errors.403', [], 403);
})->name('admin.index');

Route::get('train-management', [App\Http\Controllers\Admin\TrainManagementController::class, 'index'])->middleware('admin')->name('train-management');
Route::get('test-train', function() {
    try {
        $stations = \App\Models\Station::all();
        return response()->json(['success' => true, 'stations' => $stations]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});
Route::post('admin/train-management/train', [App\Http\Controllers\Admin\TrainManagementController::class, 'storeTrain'])->name('train-management.train.store');
Route::post('admin/train-management/station', [App\Http\Controllers\Admin\TrainManagementController::class, 'storeStation'])->name('train-management.station.store');
Route::post('admin/train-management/journey', [App\Http\Controllers\Admin\TrainManagementController::class, 'storeJourney'])->name('train-management.journey.store');

// Update routes
Route::post('admin/train-management/train/update', [App\Http\Controllers\Admin\TrainManagementController::class, 'updateTrain'])->name('train-management.train.update');
Route::post('admin/train-management/station/update', [App\Http\Controllers\Admin\TrainManagementController::class, 'updateStation'])->name('train-management.station.update');
Route::post('admin/train-management/journey/update', [App\Http\Controllers\Admin\TrainManagementController::class, 'updateJourney'])->name('train-management.journey.update');

Route::get('user-management', [App\Http\Controllers\Admin\UserController::class, 'index'])->middleware('admin')->name('user-management');
Route::put('admin/api/users/{userId}/status', [App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('admin.users.update-status');
Route::delete('admin/api/users/{userId}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
Route::get('admin/api/users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('admin.users.export');

Route::get('news-email-publish', function () {
    return view('AdminPage/NewsEmailPublish');
})->middleware('admin')->name('news-email-publish');

Route::get('card-approval', function () {
    return view('AdminPage/CardApproval');
})->middleware('admin')->name('card-approval');

Route::get('scan_qr', function () {
    return view('AdminPage/ScanQR');
})->middleware('admin')->name('scan_qr');

Route::get('log', [App\Http\Controllers\Admin\LogController::class, 'index'])->middleware('admin')->name('log');

// Newsletter subscription
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/admin/api/newsletter/subscribers', [App\Http\Controllers\NewsletterController::class, 'list'])->name('admin.newsletter.subscribers');
Route::post('/admin/api/newsletter/unsubscribe', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('admin.newsletter.unsubscribe');

Route::get('/concession_card',function(){
    return view('ConcessionCardPage');
})->name('concession_card');

Route::get('/test',function(){
    return view('test');
})->name('concession_card');

//Discover
Route::get('/discover', function () {
    return view('DiscoverPage');
})->name('DiscoverPage');

Route::get('/payment', [PaymentController::class, 'showPaymentForm']);
Route::post('/payment', [PaymentController::class, 'processPayment']);


Route::get('/stripe', [StripeController::class, 'index'])->name('stripe.index');
Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('checkout');
Route::get('/stripe/success', [StripeController::class, 'success'])->name('success');

// Wallet routes
Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('wallet.topup');
Route::get('/wallet/success', [WalletController::class, 'success'])->name('wallet.success');


// -------- STRIPE TOP-UP --------
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.stripe');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.stripe.process');

// -------- BOOKING PAYMENT --------
Route::get('/payment/{bookingId}', [PaymentController::class, 'showPaymentPage'])->name('proceedPayment');
Route::post('/payment/{bookingId}/complete', [PaymentController::class, 'completePayment'])->name('payment.complete');

//Refund routes
Route::get('/refund/{bookingId}', [PaymentController::class, 'showRefundPage'])->name('refund.page');
Route::post('/refund/{bookingId}', [PaymentController::class, 'processRefund'])->name('refund.process');