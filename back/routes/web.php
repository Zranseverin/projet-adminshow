<?php

use App\Http\Controllers\auth\AuthentificationController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryOptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicMediaController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::get('/media', PublicMediaController::class)->name('media.show');

Route::redirect('/', '/dashboard');

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::get('/login', [AuthentificationController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthentificationController::class, 'login'])->name('login.submit');
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthentificationController::class, 'logout'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profil/modifier', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('books', BookController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('promotions', PromotionController::class);
    Route::resource('stocks', StockMovementController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('delivery-options', [DeliveryOptionController::class, 'index'])->name('delivery-options.index');
    Route::put('delivery-options', [DeliveryOptionController::class, 'update'])->name('delivery-options.update');
    Route::get('advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
    Route::put('advertisements', [AdvertisementController::class, 'update'])->name('advertisements.update');
});
