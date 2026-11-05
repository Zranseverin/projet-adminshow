<?php

use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\auth\AuthentificationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryOptionController;
use App\Http\Controllers\Marketplace\ManagementController;
use App\Http\Controllers\Marketplace\ProductController;
use App\Http\Controllers\Marketplace\SellerRegistrationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PublicMediaController;
use App\Http\Controllers\StockMovementController;
use App\Http\Middleware\MarketplaceAdmin;
use App\Http\Middleware\MarketplaceSeller;
use Illuminate\Support\Facades\Route;

Route::get('/media', PublicMediaController::class)->name('media.show');

Route::get('/', fn () => redirect(auth()->user()?->account_type === 'seller' ? route('seller.products.index') : route('marketplace.products.index')));

Route::get('/dashboard', DashboardController::class)->middleware(['auth', MarketplaceAdmin::class])->name('dashboard');

Route::get('/login', [AuthentificationController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthentificationController::class, 'login'])->name('login.submit');
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthentificationController::class, 'logout'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profil/modifier', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', MarketplaceAdmin::class])->prefix('admin')->name('admin.')->group(function () {
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

Route::middleware('guest')->group(function () {
    Route::get('/devenir-vendeur', [SellerRegistrationController::class, 'create'])->name('seller.register');
    Route::post('/devenir-vendeur', [SellerRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('seller.register.store');
});
Route::middleware(['auth', MarketplaceAdmin::class])->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    Route::get('/settings', [ManagementController::class, 'settings'])->name('settings');
    Route::put('/settings', [ManagementController::class, 'updateSettings'])->name('settings.update');
    Route::get('/sellers', [ManagementController::class, 'sellers'])->name('sellers');
    Route::put('/sellers/{seller}', [ManagementController::class, 'updateSeller'])->name('sellers.update');
    Route::get('/orders', [ManagementController::class, 'orders'])->name('orders');
    Route::put('/orders/{sellerOrder}', [ManagementController::class, 'updateOrder'])->name('orders.update');
});
Route::middleware(['auth', MarketplaceSeller::class])->prefix('vendeur')->name('seller.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    Route::get('/boutique', [ManagementController::class, 'shop'])->name('shop');
    Route::put('/boutique', [ManagementController::class, 'updateShop'])->name('shop.update');
    Route::get('/commandes', [ManagementController::class, 'orders'])->name('orders');
    Route::put('/commandes/{sellerOrder}', [ManagementController::class, 'updateOrder'])->name('orders.update');
});
