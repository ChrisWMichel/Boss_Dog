<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guestOrVerified'])->group(function (){

    Route::get('/', [ProductController::class, 'index'])->name('home.front');
    Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.view');

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
        Route::post('remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
        Route::post('update-quantity/{product:slug}', [CartController::class, 'updateQuantity'])->name('update-quantity');
    });

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-customer', [ProfileController::class, 'updateCustomer'])->name('profile.update.customer');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/view', [ProfileController::class, 'view'])->name('profile.view');
});

require __DIR__.'/auth.php';

// Catch-all route to handle Vue Router
Route::get('/{any}', function () {
    return view('app'); 
})->where('any', '.*');
