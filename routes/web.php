<?php
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('HomePage');
})->name('HomePage');

Route::get('/signup', function () {
    return view('SignUpPage');
})->name('signup');

// Login routes
Route::get('/signin', [LoginController::class, 'showLoginForm'])->name('signin');
Route::post('/login', [LoginController::class, 'handleLogin'])->name('login.handle');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'handleForgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'handleResetPassword'])->name('password.update');

// Email verification
Route::get('/verify-email/{token}', [LoginController::class, 'verifyEmail'])->name('verification.verify');

Route::get('/train_selection', function () {
    return view('TrainSelectionPage');
})->name('TrainSelectionPage');

Route::get('/seat_select', function () {
    return view('SelectSeatPage');
})->name('selectseat');

Route::get('/passenger_info', function () {
    return view('PassengerInfoPage');
})->name('passengerinfo');


// Signup Page & Profile Page
Route::get('/signup', [SignupController::class, 'showForm'])
->name('signup');

Route::post('/signup', [SignupController::class, 'handleSignup'])
->name('signup.handle');

Route::get('/profile', function () {
    return view('ProfilePage');
})->name('profile');

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

Route::get('/payment', function () {
    return view('PaymentPage');
})->name('payment');

Route::get('/booking', function () {
    return view('BookingPage');
})->name('booking');

Route::get('/booking_detail', function () {
    return view('BookingDetailPage');
})->name('bookingdetail');

// Admin Page
Route::get('dashboard', function () {
    return view('AdminPage/Dashboard');
})->name('dashboard');

Route::get('train-management', function () {
    return view('AdminPage/TrainManagement');
})->name('train-management');

Route::get('user-management', function () {
    return view('AdminPage/UserManagement');
})->name('user-management');

Route::get('news-email-publish', function () {
    return view('AdminPage/NewsEmailPublish');
})->name('news-email-publish');

Route::get('card-approval', function () {
    return view('AdminPage/CardApproval');
})->name('card-approval');

Route::get('scan_qr', function () {
    return view('AdminPage/ScanQR');
})->name('scan_qr');

Route::get('log', function () {
    return view('AdminPage/Log');
})->name('log');

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