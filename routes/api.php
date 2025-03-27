<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;

Route::middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/users', UserController::class);
        Route::apiResource('/customers', CustomerController::class);
        
        Route::get('/orders', [ApiOrderController::class, 'index']);
        Route::get('/orders/statuses', [ApiOrderController::class, 'getStatuses']);
        Route::post('/orders/update-status/{order}/{status}', [ApiOrderController::class, 'updateStatus']);
        Route::get('/orders/{order}', [ApiOrderController::class, 'view']);

        Route::get('/dashboard/active-customers', [DashboardController::class, 'activeCustomers']);
        Route::get('/dashboard/active-products', [DashboardController::class, 'activeProducts']);
        Route::get('/dashboard/paid-orders', [DashboardController::class, 'paidOrders']);
        Route::get('/dashboard/total-sales', [DashboardController::class, 'totalSales']);
        Route::get('/dashboard/orders-by-state', [DashboardController::class, 'ordersByState']);
        Route::get('/dashboard/latest-customers', [DashboardController::class, 'latestCustomers']);
        Route::get('/dashboard/latest-orders', [DashboardController::class, 'latestOrders']);

        Route::get('/report/orders', [ReportController::class, 'orders']);
        Route::get('/report/customers', [ReportController::class, 'customers']);
        
    });

Route::post('/login', [AuthController::class, 'login']);

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{countryCode}/states', [CountryController::class, 'states']);