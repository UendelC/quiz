<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('logout', [UserController::class, 'logout']);
        Route::post('update-password', [UserController::class, 'updatePassword']);
    }
);

Route::get('/users', [UserController::class, 'index']);
Route::post('/registration', [UserController::class, 'store']);
Route::get('/user/{user}', [UserController::class, 'show']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);