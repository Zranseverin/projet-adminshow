<?php

use App\Http\Controllers\Api\BookCatalogController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\DeliveryOptionController;
use App\Http\Controllers\Api\OrderCheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/books', [BookCatalogController::class, 'index'])->name('api.books.index');
Route::post('/books', [BookCatalogController::class, 'store'])->name('api.books.store');
Route::get('/books/{book}', [BookCatalogController::class, 'show'])->name('api.books.show');
Route::put('/books/{book}', [BookCatalogController::class, 'update'])->name('api.books.update');
Route::patch('/books/{book}', [BookCatalogController::class, 'update']);
Route::delete('/books/{book}', [BookCatalogController::class, 'destroy'])->name('api.books.destroy');
Route::get('/products/{book}', [BookCatalogController::class, 'show'])->name('api.products.show');
Route::get('/products/{book}/related', [BookCatalogController::class, 'related'])->name('api.products.related');
Route::get('/catalog/filters', [BookCatalogController::class, 'filters'])->name('api.catalog.filters');
Route::get('/categories', [BookCatalogController::class, 'categories'])->name('api.categories.index');
Route::get('/delivery-options', [DeliveryOptionController::class, 'index'])->name('api.delivery-options.index');
Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('api.advertisements.index');
Route::post('/orders', [OrderCheckoutController::class, 'store'])->name('api.orders.store');
