<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Auth::routes();

Route::get(
    '/{any}', 
    [App\Http\Controllers\AppController::class, 'index']
)->where('any', '.*');
