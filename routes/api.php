<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\EmailVerificationController;

$middleware = 'throttle:api';
if (app()->environment('testing')) {
    $middleware = [];
}

$apiRoutes = function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::post('/tokens', [AuthController::class, 'createToken']);
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    });

    // Link & Analytics Group
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/links', [App\Http\Controllers\Api\LinkController::class, 'index']);
        Route::get('/links/{link}', [App\Http\Controllers\Api\LinkController::class, 'show']);
        Route::put('/links/{link}', [App\Http\Controllers\Api\LinkController::class, 'update']);
        Route::delete('/links/{link}', [App\Http\Controllers\Api\LinkController::class, 'destroy']);
        Route::put('/links/{link}/qr-branding', [App\Http\Controllers\Api\LinkController::class, 'updateQrBranding']);
    });

    Route::post('/links', [App\Http\Controllers\Api\LinkController::class, 'store']);
    Route::get('/links/{shortCode}/download-qr', [App\Http\Controllers\Api\LinkController::class, 'downloadQr'])->name('api.links.qr_download');
    Route::get('/analytics/{shortCode}', [App\Http\Controllers\Api\AnalyticsController::class, 'show']);

    // Registration/Auth auxiliary routes (Email/Password)
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])->middleware('auth:sanctum');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);
};

Route::middleware($middleware)->group($apiRoutes);

// API Version 1
Route::prefix('v1')->middleware($middleware)->group($apiRoutes);



