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

namespace Domain\Shop\Cart\Actions;

use Domain\Shop\Cart\DataTransferObjects\CreateCartData;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Models\Sku;

final class CreateCartAction
{
    public function execute(CreateCartData $data): Cart
    {
        /** @var \Domain\Shop\Product\Models\Sku $sku */
        $sku = Sku::where((new Sku)->getRouteKeyName(), $data->sku_uuid)
            ->first();

        return Cart::create([
            'branch_uuid' => $data->branch->getKey(),
            'customer_uuid' => $data->customer->getKey(),
            'product_uuid' => $sku->product->getKey(),
            'sku_uuid' => $sku->getKey(),
            'sku_code' => $sku->code,
            'product_name' => $sku->product->name,
            'price' => $sku->price,
            'minimum' => $sku->minimum,
            'maximum' => $sku->maximum,
            'quantity' => $data->quantity,
        ]);
    }
}
