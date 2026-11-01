<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\ThriftController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Web\OwnerController;
use App\Http\Controllers\Web\SellerController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\MessageController;

// Public Storefront
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kos', [KosController::class, 'index'])->name('kos.index');
Route::get('/kos/{id}', [KosController::class, 'show'])->whereNumber('id')->name('kos.show');
Route::get('/thrift', [ThriftController::class, 'index'])->name('thrift.index');
Route::get('/thrift/{id}', [ThriftController::class, 'show'])->whereNumber('id')->name('thrift.show');

Route::post('/newsletter', NewsletterController::class)->name('subscribe');

// Guest Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('login.store');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') return redirect('/admin');
        
        if ($user->role === 'student') {
            return view('auth.dashboard-student', compact('user'));
        }

        if ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        }

        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        $myKos = $user->kosListings()->latest()->get();
        $myThrift = $user->thriftProducts()->latest()->get();
        $stats = [
            'kos' => $myKos->count(),
            'kos_available' => $myKos->where('is_available', true)->count(),
            'thrift' => $myThrift->count(),
            'thrift_active' => $myThrift->where('is_sold', false)->count(),
        ];

        return view('auth.dashboard', [
            'kosListings' => $myKos,
            'thriftProducts' => $myThrift,
            'stats' => $stats,
            'user' => $user
        ]);
    })->name('dashboard');

    // Student specific
    Route::prefix('student')->group(function () {
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::get('/bookings', [StudentController::class, 'bookings'])->name('my-bookings');
        Route::get('/purchases', [StudentController::class, 'purchases'])->name('my-purchases');
        Route::post('/bookings', [App\Http\Controllers\Api\V1\BookingController::class, 'store'])->name('web.bookings.store');
    });

    // Checkout (manual payment: transfer bank / COD)
    Route::get('/thrift/{id}/checkout', [CheckoutController::class, 'show'])->whereNumber('id')->name('thrift.checkout');
    Route::post('/thrift/{id}/checkout', [CheckoutController::class, 'store'])->whereNumber('id')->name('thrift.checkout.store');
    Route::post('/thrift/{id}/checkout/proof', [CheckoutController::class, 'storeProof'])->whereNumber('id')->name('thrift.checkout.proof');

    // Messages (Student & Owner share the same inbox; participant check inside)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/start', [MessageController::class, 'store'])->name('messages.start');
    Route::post('/messages/{conversation}', [MessageController::class, 'sendMessage'])->name('messages.send');

    // Owner specific
    Route::prefix('owner')->middleware('role:owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/kos/create', [OwnerController::class, 'createKos'])->name('kos.create');
        Route::post('/kos', [OwnerController::class, 'storeKos'])->name('kos.store');
        Route::get('/kos/{kos}/edit', [OwnerController::class, 'editKos'])->name('kos.edit');
        Route::put('/kos/{kos}', [OwnerController::class, 'updateKos'])->name('kos.update');
        Route::get('/kos/{kos}/monitoring', [OwnerController::class, 'monitoring'])->name('kos.monitoring');
        Route::post('/rooms', [OwnerController::class, 'storeRoom'])->name('rooms.store');
        Route::patch('/rooms/{room}', [OwnerController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{room}', [OwnerController::class, 'deleteRoom'])->name('rooms.delete');
        Route::patch('/facilities/{facility}', [OwnerController::class, 'updateFacility'])->name('facilities.update');
        Route::patch('/bookings/{booking}', [OwnerController::class, 'updateBookingStatus'])->name('bookings.status');
    });

    // Seller specific
    Route::prefix('seller')->middleware('role:seller')->name('seller.')->group(function () {
        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
        Route::post('/products', [SellerController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [SellerController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [SellerController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [SellerController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}', [SellerController::class, 'toggleSold'])->name('products.toggle');
    });
});
