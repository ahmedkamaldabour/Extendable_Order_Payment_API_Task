<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(static function () {

    // Auth routes: /api/v1/auth/*
    Route::prefix('auth')->group(static function () {

        // Public
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Protected
        Route::middleware('auth:api')->group(static function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Protected routes
    Route::middleware('auth:api')->group(static function () {

        // Order routes: /api/v1/orders/*
        Route::prefix('orders')->group(static function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::put('/{order}', [OrderController::class, 'update']);
            Route::delete('/{order}', [OrderController::class, 'destroy']);
            Route::patch('/{order}/status', [OrderController::class, 'updateStatus']);

            // Payment routes: /api/v1/orders/{order}/payments/*
            Route::post('/{order}/payments', [PaymentController::class, 'process']);
            Route::get('/{order}/payments', [PaymentController::class, 'index']);
        });

        // Payment methods: /api/v1/payments/methods
        Route::get('/payments/methods', [PaymentController::class, 'methods']);
    });
});
