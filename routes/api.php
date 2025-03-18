<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\ProductController;

Route::middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/products', ProductController::class);
        Route::get('/orders', [ApiOrderController::class, 'index']);
        Route::get('/orders/statuses', [ApiOrderController::class, 'getStatuses']);
        Route::post('/orders/update-status/{order}/{status}', [ApiOrderController::class, 'updateStatus']);
        Route::get('/orders/{order}', [ApiOrderController::class, 'view']);
        
    });

Route::post('/login', [AuthController::class, 'login']);