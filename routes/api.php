<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\ThriftController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\KosResourceController;
use App\Http\Controllers\Api\V1\ThriftProductController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\KosMonitoringController;

use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        Route::get('/kos', [KosController::class, 'index']);
        Route::get('/kos/{id}', [KosController::class, 'show']);
        Route::post('/kos', [KosResourceController::class, 'store']);
        Route::put('/kos/{id}', [KosResourceController::class, 'update']);
        Route::delete('/kos/{id}', [KosResourceController::class, 'destroy']);
        
        Route::get('/thrift', [ThriftController::class, 'index']);
        Route::get('/thrift/{id}', [ThriftController::class, 'show']);
        Route::post('/thrift', [ThriftProductController::class, 'store']);
        Route::put('/thrift/{id}', [ThriftProductController::class, 'update']);
        Route::delete('/thrift/{id}', [ThriftProductController::class, 'destroy']);
        
        Route::get('/categories', [CategoryController::class, 'index']);
        
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
        Route::get('/owner/bookings', [BookingController::class, 'ownerBookings']);
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);

        Route::prefix('owner/kos/{kosId}')->group(function () {
            Route::get('/monitoring', [KosMonitoringController::class, 'show']);
            Route::post('/rooms', [KosMonitoringController::class, 'storeRoom']);
            Route::patch('/rooms/{roomId}', [KosMonitoringController::class, 'updateRoom']);
            Route::delete('/rooms/{roomId}', [KosMonitoringController::class, 'deleteRoom']);
            Route::patch('/facilities/{facilityId}', [KosMonitoringController::class, 'updateFacility']);
            Route::get('/condition-updates', [KosMonitoringController::class, 'conditionUpdates']);
        });
    });
});
