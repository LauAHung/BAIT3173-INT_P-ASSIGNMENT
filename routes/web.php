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
