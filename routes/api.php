<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ThriftController;
use App\Http\Controllers\Api\V1\ThriftPurchaseController;

Route::prefix('v1')->group(function () {
    Route::get('/kos', [KosController::class, 'index']);
    Route::get('/kos/{id}', [KosController::class, 'show']);

    Route::get('/thrift', [ThriftController::class, 'index']);
    Route::get('/thrift/{id}', [ThriftController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [MeController::class, 'show']);
        Route::put('/me', [MeController::class, 'update']);

        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

        Route::post('/thrift', [ThriftController::class, 'store']);
        Route::post('/thrift/{id}/purchase', [ThriftPurchaseController::class, 'purchase']);
        Route::get('/my-purchases', [ThriftPurchaseController::class, 'myPurchases']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    });
});
