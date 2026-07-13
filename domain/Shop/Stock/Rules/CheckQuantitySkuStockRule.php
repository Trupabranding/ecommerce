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

namespace Domain\Shop\Stock\Rules;

use Closure;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Enums\ProductStatus;
use Domain\Shop\Product\Enums\SkuMinimumType;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Enums\SkuStockType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;

readonly class CheckQuantitySkuStockRule implements ValidationRule
{
    private ?Sku $skuModel;

    public function __construct(
        Branch $branch,
        Sku|string|int $sku,
        string $skuColumn = 'uuid',
    ) {
        $query = $sku instanceof Sku
            ? Sku::whereKey($sku)
            : Sku::where($skuColumn, $sku);

        $this->skuModel = $query
            ->whereRelation('product', 'status', ProductStatus::in_stock)

            ->with([
                'skuStocks' => fn (Relation $query) => $query
                    ->whereBelongsTo($branch),
            ])
            ->first();
    }

    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $quantity = (float) $value;
        unset($value);

        $skuStock = $this->skuModel?->skuStocks[0] ?? null;

        if ($skuStock === null) {
            $fail(trans('Sku stock not ready.'));

            return;
        }

        if ($skuStock->type === SkuStockType::unlimited) {
            return;
        }

        if ($skuStock->type === SkuStockType::unavailable) {
            $fail(trans('Sku stock is not available.'));

            return;
        }

        /** @var Sku $skuModel */
        $skuModel = $this->skuModel;

        if (
            $skuModel->minimum !== null &&
            $skuModel->minimum_type === SkuMinimumType::minimum_quantity &&
            $quantity < $skuModel->minimum
        ) {

            $fail(trans('Allowed minimum quantity is :minimum', ['minimum' => $skuModel->minimum]));

            return;
        }

        if ($quantity > $skuStock->count) {
            /** @var float $count */
            $count = $skuStock->count;
            $fail(trans('Sku Stock is insufficient, available: :count.', ['count' => $count]));

            return;
        }
    }
}
