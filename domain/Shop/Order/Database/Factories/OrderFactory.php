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

namespace Domain\Shop\Order\Database\Factories;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\OrderPaymentStatus;
use Domain\Shop\Order\Enums\OrderStatus;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Order\Models\Order>
 */
class OrderFactory extends Factory
{
    #[\Override]
    protected $model = Order::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'customer_uuid' => Customer::factory(),
            'branch_uuid' => Branch::factory(),
            'notes' => $this->faker->sentence(10),
            'payment_status' => Arr::random(OrderPaymentStatus::cases()),
            'status' => fn (array $attributes) => match ($attributes['payment_status']) {
                OrderPaymentStatus::paid => OrderStatus::completed,
                OrderPaymentStatus::pending, OrderPaymentStatus::unpaid => Arr::random(Arr::except(OrderStatus::cases(), [OrderStatus::completed->value])),
                default => OrderStatus::failed,
            },
            'delivery_price' => 0,
            'total_price' => 0,
            'claim_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'claim_type' => ClaimType::delivery,
            'purchased_at' => now(),
        ];
    }

    /** @param  \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Sku>  $SKUs */
    public function hasOrderItems(Collection $SKUs): self
    {
        $SKUs->ensure(Sku::class);

        $self = $this;

        $SKUs->each(
            function (Sku $sku) use (&$self): self {
                return $self = $self
                    ->has(
                        OrderItem::factory()
                            ->forSku($sku)
                    );
            }
        );

        return $self;
    }
}
