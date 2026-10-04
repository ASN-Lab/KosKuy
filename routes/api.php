<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\ThriftController;
use App\Http\Controllers\Api\V1\CategoryController;

use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        Route::get('/kos', [KosController::class, 'index']);
        Route::get('/kos/{id}', [KosController::class, 'show']);
        
        Route::get('/thrift', [ThriftController::class, 'index']);
        Route::get('/thrift/{id}', [ThriftProductController::class, 'show']);
        
        Route::get('/categories', [CategoryController::class, 'index']);
    });
});
