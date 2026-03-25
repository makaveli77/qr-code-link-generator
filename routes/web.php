<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\WebAuthController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::middleware(['web', 'auth'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/profile', [WebAuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [WebAuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [WebAuthController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/{short_code}', RedirectController::class)
    ->where('short_code', '[a-zA-Z0-9\-\_]{3,20}')
    ->name('redirect');
