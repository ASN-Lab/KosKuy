<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\ThriftController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kos', [KosController::class, 'index']);
Route::get('/kos/{id}', [KosController::class, 'show']);
Route::get('/thrift', [ThriftController::class, 'index']);
Route::get('/thrift/{id}', [ThriftController::class, 'show']);