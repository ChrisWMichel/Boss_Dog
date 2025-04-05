<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;

Route::middleware(['guestOrVerified'])->group(function (){

    Route::get('/', [ProductController::class, 'index'])->name('home.front');
    Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('by-category');
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

    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder'])->name('cart.checkout-order');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/view/{order}', [OrderController::class, 'view'])->name('orders.view');
    //Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

require __DIR__.'/auth.php';

// Catch-all route to handle Vue Router
Route::get('/{any}', function () {
    return view('app'); 
})->where('any', '.*');
