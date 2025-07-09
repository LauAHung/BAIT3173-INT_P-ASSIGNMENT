<?php

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

Route::get('/profile', function () {
    return view('ProfilePage');
})->name('profile');