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

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use Domain\Shop\Order\Models\Order;
use Illuminate\Http\Request;
use Override;

/**
 * @property-read Order $resource
 */
class OrderResource extends BaseJsonApiResource
{
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'receipt_number' => $this->resource->receipt_number,
            'notes' => $this->resource->notes,
            'payment_status' => $this->resource->payment_status->getLabel(),
            'payment_method' => $this->resource->payment_method?->getLabel(),
            'status' => $this->resource->status->getLabel(),
            'total_price' => self::money($this->resource->total_price),
            'claim_type' => $this->resource->claim_type->getLabel(),
            'claim_at' => self::datetimeFormat($this->resource->claim_at),
            'created_at' => self::datetimeFormat($this->resource->created_at),
        ];
    }

    #[Override]
    public function toRelationships(Request $request): array
    {
        return [
            'orderItems' => fn () => OrderItemResource::collection($this->resource->orderItems),
        ];
    }
}
