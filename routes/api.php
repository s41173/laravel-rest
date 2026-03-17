<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('/users', [UserController::class, 'index']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', [AuthController::class, 'login']);
Route::get('/testing', [AuthController::class, 'testing']);
Route::post('/otp', [AuthController::class, 'otp']);
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/register', [CustomerController::class, 'register']);
Route::get('/city', [CustomerController::class, 'get_city']);
Route::get('/district/{id}', [CustomerController::class, 'get_district']);

// Route::middleware('auth:api')->group(function () {
//     // Route::get('/me', [AuthController::class, 'me']);
//     // Route::post('/logout', [AuthController::class, 'logout']);
//     // Route::post('/refresh', [AuthController::class, 'refresh']);
// });

// dibungkus autentikasi redis
Route::middleware('auth:api', 'check.session')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/decode', [AuthController::class, 'decode']);
    Route::get('/get', [CustomerController::class, 'get']);
    Route::post('/change_password', [CustomerController::class, 'change_password']);
    Route::put('/update', [CustomerController::class, 'update']);
    Route::post('/updateImage', [CustomerController::class, 'uploadImage']);
});