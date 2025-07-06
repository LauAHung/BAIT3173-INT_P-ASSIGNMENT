<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return "hi";
    return view('welcome');
});
