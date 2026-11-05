<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\BookCatalogController;
use App\Http\Controllers\Api\DeliveryOptionController;
use App\Http\Controllers\Api\MarketplaceController;
use App\Http\Middleware\MarketplaceAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookCatalogController::class, 'index'])->name('api.books.index');
Route::post('/books', [BookCatalogController::class, 'store'])->middleware(['web', 'auth', MarketplaceAdmin::class])->name('api.books.store');
Route::get('/books/{book}', [BookCatalogController::class, 'show'])->name('api.books.show');
Route::put('/books/{book}', [BookCatalogController::class, 'update'])->middleware(['web', 'auth', MarketplaceAdmin::class])->name('api.books.update');
Route::patch('/books/{book}', [BookCatalogController::class, 'update'])->middleware(['web', 'auth', MarketplaceAdmin::class]);
Route::delete('/books/{book}', [BookCatalogController::class, 'destroy'])->middleware(['web', 'auth', MarketplaceAdmin::class])->name('api.books.destroy');
Route::get('/products/{book}', [BookCatalogController::class, 'show'])->name('api.products.show');
Route::get('/products/{book}/related', [BookCatalogController::class, 'related'])->name('api.products.related');
Route::get('/catalog/filters', [BookCatalogController::class, 'filters'])->name('api.catalog.filters');
Route::get('/categories', [BookCatalogController::class, 'categories'])->name('api.categories.index');
Route::get('/delivery-options', [DeliveryOptionController::class, 'index'])->name('api.delivery-options.index');
Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('api.advertisements.index');
// Retire the old checkout so imported book stock cannot be sold twice.
Route::post('/orders', fn () => response()->json(['message' => 'Utilisez le nouveau panier marketplace.', 'endpoint' => '/api/marketplace/orders'], 410))->name('api.orders.store');

Route::prefix('marketplace')->group(function () {
    Route::get('/products', [MarketplaceController::class, 'index']);
    Route::get('/products/{product}', [MarketplaceController::class, 'show']);
    Route::get('/options', [MarketplaceController::class, 'options']);
    Route::post('/orders', [MarketplaceController::class, 'checkout'])->middleware('throttle:10,1');
});
