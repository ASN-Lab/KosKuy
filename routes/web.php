<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlyerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ThriftController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Web\OwnerController;
use App\Http\Controllers\Web\SellerController;
use App\Http\Controllers\Web\StudentController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kos', [KosController::class, 'index']);
Route::get('/kos/{id}', [KosController::class, 'show']);
Route::get('/thrift', [ThriftController::class, 'index']);
Route::get('/thrift/{id}', [ThriftController::class, 'show']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('student')->group(function () {
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
    Route::get('/bookings', [StudentController::class, 'bookings'])->name('my-bookings');
    Route::get('/purchases', [StudentController::class, 'purchases'])->name('my-purchases');
    
    // Booking Web Route
    Route::post('/bookings', [App\Http\Controllers\Api\V1\BookingController::class, 'store'])->name('web.bookings.store');
});

Route::prefix('owner')->middleware(['auth', 'role:owner'])->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/kos/{kos}/monitoring', [OwnerController::class, 'monitoring'])->name('kos.monitoring');
    Route::post('/rooms', [OwnerController::class, 'storeRoom'])->name('rooms.store');
    Route::patch('/rooms/{room}', [OwnerController::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room}', [OwnerController::class, 'deleteRoom'])->name('rooms.delete');
    Route::patch('/facilities/{facility}', [OwnerController::class, 'updateFacility'])->name('facilities.update');
    Route::patch('/bookings/{booking}', [OwnerController::class, 'updateBookingStatus'])->name('bookings.status');
});

Route::prefix('seller')->middleware(['auth', 'role:seller'])->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::post('/products', [SellerController::class, 'store'])->name('products.store');
    Route::delete('/products/{product}', [SellerController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}', [SellerController::class, 'toggleSold'])->name('products.toggle');
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