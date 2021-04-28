<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Request;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:api')
    ->get(
        '/user',
        function (Request $request) {
            return $request->user();
        }
);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/users', [UserController::class, 'index']);
    }
);

Route::post('/registration', [UserController::class, 'store']);
Route::get('/user/{user}', [UserController::class, 'show']);
// Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

Route::post('/questions', [QuestionController::class, 'store']);