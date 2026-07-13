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

namespace Domain\Shop\Order\Actions;

use App\Settings\OrderSettings;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Str;

readonly class GenerateReceiptNumberAction
{
    public function __construct(
        private OrderSettings $orderSettings,
    ) {}

    public function execute(Branch $branch): string
    {
        $prefix = sprintf(
            '%s%s%s',
            $this->orderSettings->prefix,
            $branch->code,
            now()->format('ymd')
        );

        /** @var string|null $latestReceiptNumber */
        $latestReceiptNumber = Order::withTrashed()
            ->where(
                'receipt_number',
                'like',
                $prefix.'%'
            )
            ->latest()
            ->value('receipt_number');

        if ($latestReceiptNumber === null) {
            return $prefix.'0001';
        }

        $incrementNumber = (string) Str::of($latestReceiptNumber)
            ->substr(Str::length($prefix));

        $incrementNumberPlusOne = ((int) $incrementNumber) + 1;

        return $prefix.Str::of((string) $incrementNumberPlusOne)
            ->padLeft(4, '0');
    }
}
