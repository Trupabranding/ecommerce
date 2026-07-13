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

namespace Domain\Shop\Stock\Database\Factories;

use Domain\Shop\Stock\Enums\SkuStockType;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Stock\Models\SkuStock>
 */
class SkuStockFactory extends Factory
{
    #[\Override]
    protected $model = SkuStock::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'type' => Arr::random(SkuStockType::cases()),
            'count' => fn (array $attributes) => match ($attributes['type']) {
                SkuStockType::base_on_stock => $this->faker->numberBetween(15, 30),
                default => null,
            },
            'warning' => fn (array $attributes) => match ($attributes['type']) {
                SkuStockType::base_on_stock => $this->faker->numberBetween(5, 15),
                default => null,
            },
        ];
    }

    public function unlimited(): self
    {
        return $this->state([
            'type' => SkuStockType::unlimited,
        ]);
    }

    public function baseOnStock(float $stockCount): self
    {
        return $this->state([
            'type' => SkuStockType::base_on_stock,
            'count' => $stockCount,
        ]);
    }
}
