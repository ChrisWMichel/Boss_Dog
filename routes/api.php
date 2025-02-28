<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::middleware(['auth:sanctum', 'admin'])
->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);

    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::post('/login', [AuthController::class, 'login']);
//Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);