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

namespace App\Filament\Admin\Resources\Shop\OrderResource;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Actions\GetOpeningHoursByBranchAction;
use Domain\Shop\Order\Actions\CalculateOrderTotalPriceAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Enums\ClaimType;
use Spatie\OpeningHours\OpeningHours;

class Support
{
    private function __construct() {}

    public static function callCalculatorForTotalPrice(array $orderItems): float
    {
        return moneyAmountToFloat(app(CalculateOrderTotalPriceAction::class)
            ->execute(
                collect($orderItems)
                    ->reject(fn (array $data): bool => blank($data['sku_uuid']))
                    ->map(
                        fn (array $data): ItemWithMinMaxData => new ItemWithMinMaxData(
                            price: money($data['price']),
                            quantity: (float) $data['quantity'],
                            minimumType: $data['minimum_type'],
                            minimum: $data['minimum'],
                            maximum: $data['maximum']
                        )
                    )
                    ->toArray()
            ));
    }

    public static function openingHours(Branch $branch, ClaimType $claimType): OpeningHours
    {
        match ($claimType) {
            ClaimType::delivery => $branch->load('operationHoursOnline'),
            ClaimType::pickup => $branch->load('operationHoursInStore'),
        };

        return app(GetOpeningHoursByBranchAction::class)
            ->execute($branch, $claimType->operationHourType());
    }
}
