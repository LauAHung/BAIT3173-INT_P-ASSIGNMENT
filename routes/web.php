<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return "hi";
    return view('HomePage');
});

Route::get('/signin', function () {
    return view('SignInPage');
})->name('signin');