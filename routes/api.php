<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExamController;
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

Route::prefix('me')->group(
    function () {
        Route::get('/user', [UserController::class, 'show'])
            ->middleware('auth:sanctum');
    }
);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/exams', [ExamController::class, 'store']);
        Route::get('/exams', [ExamController::class, 'index']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/takeexam', [UserController::class, 'takeExam']);
        Route::get('/grades', [UserController::class, 'grades']);
    }
);

Route::post('/registration', [UserController::class, 'store']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/questions', [QuestionController::class, 'store']);