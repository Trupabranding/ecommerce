<?php

/*
 * Copyright (c) 2026 Trupa Technologies
 * All rights reserved.
 *
 * Developed by Boncanca Collins
 * GitHub: @iamtomc, @boncanca
 * Organization: trupabranding
 *
 * 1. Usage Permissions
 *    This software is proprietary to Trupa Technologies. The following restrictions apply:
 *    ✅ Allowed:
 *
 *     - Private use within the authorized organization.
 *     - Internal modifications.
 *     🚫 Not Allowed:
 *
 *     - Redistribution, sublicensing, or public sharing.
 *     - Commercial use outside of the authorized organization.
 * 2. Disclaimer of Warranty
 *    This software is provided "as is", without any warranty of any kind, express or implied, including but not limited to:
 *
 *     - Merchantability
 *     - Fitness for a particular purpose
 *     - Non-infringement
 * 3. Liability Limitation
 *    Under no circumstances shall the author(s) or copyright holders be liable for any claims, damages, or other liabilities arising from the use of this software.
 *
 * 4. Legal Enforcement
 *    Unauthorized use, distribution, or modification is strictly prohibited and may result in legal consequences.
 *
 * 📩 For inquiries, contact: hello@trupabranding.com
 * 🌐 Official Website: https://trupabranding.com
 * 📱 GitHub Organization: https://github.com/trupabranding
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public storefront routes
Route::get('/', function () {
    return Inertia::render('Home', [
        'cart' => [],
        'products' => [],
    ]);
})->name('home');

Route::get('/shop', function () {
    return Inertia::render('Shop', [
        'cart' => [],
        'products' => [],
        'category' => request()->query('category'),
    ]);
})->name('shop.index');

Route::get('/category/{category}', function ($category) {
    return Inertia::render('Category', [
        'category' => ucfirst($category),
    ]);
})->name('category.show');

Route::get('/products/{id}', function ($id) {
    return Inertia::render('Product', [
        'cart' => [],
        'product' => ['id' => $id],
    ]);
})->name('products.show');

Route::get('/checkout', function () {
    return Inertia::render('Checkout', [
        'cart' => [],
        'subtotal' => 0,
        'tax' => 0,
        'shipping' => 0,
        'total' => 0,
    ]);
})->name('checkout.index');

Route::get('/order/{id}', function ($id) {
    return Inertia::render('Order', [
        'order' => ['id' => $id],
    ]);
})->name('orders.show');

Route::supportBubble();
