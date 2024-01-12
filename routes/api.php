<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;

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

Route::prefix('v1')->group(function () {

    /* ---------------------------------- AUTH ---------------------------------- */
    Route::prefix('auth')->group(function () {
        Route::get('@me', [AuthController::class, 'index'])->middleware('auth:sanctum');
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    /* ---------------------------------- USER ---------------------------------- */
    Route::prefix('user')->group(function () {
        Route::post('avatar', [UserController::class, 'storeAvatar'])->middleware('auth:sanctum');
    });

    /* ---------------------------------- VIDEO ---------------------------------- */
    Route::prefix('videos')->group(function () {
        Route::get('', [VideoController::class, 'index'])->middleware('auth:sanctum');
        Route::post('', [VideoController::class, 'store'])->middleware('auth:sanctum');
        Route::patch('{user_video}', [VideoController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('{user_video}', [VideoController::class, 'destroy'])->middleware('auth:sanctum');
    });
});
