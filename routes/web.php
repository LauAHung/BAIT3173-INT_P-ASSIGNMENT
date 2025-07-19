<?php
use App\Http\Controllers\SignupController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return "hi";
    return view('HomePage');
})->name('HomePage');

Route::get('/signup', function () {
    return view('SignUpPage');
})->name('signup');

Route::get('/signin', function () {
    return view('SignInPage');
})->name('signin');

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

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
->name('google.login');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

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

