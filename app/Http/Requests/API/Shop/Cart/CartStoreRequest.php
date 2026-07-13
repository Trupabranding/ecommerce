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

namespace App\Http\Requests\API\Shop\Cart;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Override;

class CartStoreRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Branch $branch */
        $branch = $this->route('enabledBranch');

        return [
            'sku_uuid' => [
                'required',
                Rule::exists(Sku::class, (new Sku)->getRouteKeyName()),
                Rule::unique(Cart::class, 'sku_uuid')
                    ->where('customer_uuid', Auth::id()),
                'required_with:quantity',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:1',
                new CheckQuantitySkuStockRule(
                    branch: $branch,
                    sku: (string) $this->string('sku_uuid'),
                    skuColumn: (new Sku)->getRouteKeyName(),
                ),
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'sku_uuid.unique' => trans('The :attribute has already in you\'re cart.'),
        ];
    }
}
