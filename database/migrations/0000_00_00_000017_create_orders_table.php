<?php

/*
 * Copyright (c) 2023 Lloric Mayuga Garcia
 * All rights reserved.
 *
 * 1. Usage Permissions
 *    This software is licensed exclusively to Lloric Mayuga Garcia. The following restrictions apply:
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
 * 📩 For inquiries, contact: lloricode@gmail.com
 * 🌐 Official Website: https://github.com/lloricode
 * 🛒 Purchase Here: https://lloricode.gumroad.com/l/laravel-filament-point-of-sale
 */

declare(strict_types=1);

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Branch::class)
                ->constrained(column: 'uuid');
            $table->foreignIdFor(Customer::class)
                ->nullable()
                ->constrained(column: 'uuid');

            $table->string('receipt_number')->unique();
            $table->money('delivery_price');
            $table->money('total_price');

            $table->text('notes')->nullable();

            $table->phpEnum('payment_method')->nullable();
            $table->phpEnum('payment_status')->default(OrderPaymentStatus::pending->value);
            $table->phpEnum('status')->default(OrderStatus::pending->value);
            $table->phpEnum('claim_type');

            $table->dateTime('claim_at')->nullable();
            $table->dateTime('purchased_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid()->primary()->unique();

            $table->foreignIdFor(Order::class)
                ->constrained(column: 'uuid')
                ->cascadeOnDelete();
            $table->foreignIdFor(Sku::class)
                ->constrained(column: 'uuid');

            $table->string('sku_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->money('price');
            $table->money('discount_price');
            $table->money('total_price');
            $table->float('quantity')
                ->unsigned()
                ->comment('customer actual quantity');
            $table->float('paid_quantity')
                ->unsigned();
            $table->float('minimum')
                ->unsigned()
                ->nullable();
            $table->float('maximum')
                ->unsigned()
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'order_uuid',
                'sku_uuid',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
