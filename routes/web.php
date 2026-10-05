<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlyerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ThriftController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/kos', [KosController::class, 'index'])->name('kos.index');
Route::get('/kos/{id}', [KosController::class, 'show'])
    ->whereNumber('id')
    ->name('kos.show');

Route::get('/thrift', [ThriftController::class, 'index'])->name('thrift.index');
Route::get('/thrift/{id}', [ThriftController::class, 'show'])
    ->whereNumber('id')
    ->name('thrift.show');

Route::get('/flyer', FlyerController::class)->name('flyer');

Route::post('/newsletter', NewsletterController::class)->name('subscribe');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
| Session-based, single-page forms. Registration and logout stay
| intentionally small; listing ownership is handled separately.
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});