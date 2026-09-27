<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\KosController;
use App\Http\Controllers\Api\V1\ThriftController;
use App\Http\Controllers\Api\V1\CategoryController;

Route::prefix('v1')->group(function () {
    Route::get('/kos', [KosController::class, 'index']);
    Route::get('/kos/{id}', [KosController::class, 'show']);
    
    Route::get('/thrift', [ThriftController::class, 'index']);
    Route::get('/thrift/{id}', [ThriftController::class, 'show']);
    
    Route::get('/categories', [CategoryController::class, 'index']);
});
