<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Storefront\CategoryController;
use App\Http\Controllers\Api\Storefront\OrderController;
use App\Http\Controllers\Api\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('storefront')->name('storefront.')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('products.by-category');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{uuid}', [CategoryController::class, 'show'])->name('categories.show');

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
});
