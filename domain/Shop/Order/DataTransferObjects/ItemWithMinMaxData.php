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

namespace Domain\Shop\Order\DataTransferObjects;

use Brick\Money\Money;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Enums\SkuMinimumType;

final readonly class ItemWithMinMaxData
{
    public function __construct(
        public Money $price,
        public float $quantity,
        public ?SkuMinimumType $minimumType,
        public ?float $minimum,
        public ?float $maximum,
    ) {}

    public static function fromOrderItem(OrderItem $orderItem): self
    {
        return new self(
            price: money($orderItem->sku->price),
            quantity: $orderItem->quantity,
            minimumType: $orderItem->sku->minimum_type,
            minimum: $orderItem->sku->minimum,
            maximum: $orderItem->sku->maximum,
        );
    }
}
