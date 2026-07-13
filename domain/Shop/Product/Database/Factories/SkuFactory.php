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

namespace Domain\Shop\Product\Database\Factories;

use Database\Factories\Support\HasMediaFactory;
use Database\Seeders\Faker\MoneyFakerData;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Product\Database\AttributeOptionForProductSku;
use Domain\Shop\Product\Enums\SkuMinimumType;
use Domain\Shop\Product\Models\Attribute;
use Domain\Shop\Product\Models\AttributeOption;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Product\Models\Sku>
 */
class SkuFactory extends Factory
{
    use HasMediaFactory;

    #[\Override]
    protected $model = Sku::class;

    #[\Override]
    public function definition(): array
    {
        $this->faker->addProvider(new MoneyFakerData($this->faker));

        return [
            'product_uuid' => Product::factory(),
            'code' => $this->faker->unique()->uuid(),
            /** @phpstan-ignore method.notFound */
            'price' => $this->faker->money(),
            'minimum_type' => SkuMinimumType::minimum_quantity,
            'minimum' => $this->faker->boolean() ? Arr::random([1, 2, 3, 4]) : null,
            'maximum' => function (array $attributes) {
                if (isset($attributes['minimum'])) {
                    return $this->faker->boolean()
                        ? Arr::random(range($attributes['minimum'], $attributes['minimum'] + 4))
                        : null;
                }

                return $this->faker->boolean() ? Arr::random([1, 2, 3, 4]) : null;
            },
        ];
    }

    public function withDefaultData(array|Branch|BranchFactory|Collection|null $branches = null): self
    {
        $self = $this;

        $branches ??= Branch::factory();

        if ($branches instanceof Branch || $branches instanceof BranchFactory) {
            $branches = [$branches];
        }

        foreach ($branches as $branch) {

            if (! ($branch instanceof Branch) && ! ($branch instanceof BranchFactory)) {
                throw new \Exception('Invalid');
            }

            $self = $self->has(SkuStock::factory()->unlimited()->for($branch));
        }

        return $self
            ->hasRandomMedia()
            ->regenerateCode();
    }

    /**
     * @param  array<int, AttributeOptionForProductSku|AttributeOptionFactory>  $attributeOptions
     *
     * @throws \Exception
     */
    public static function forProduct(
        Product $product,
        float|SkuFactory $priceOrSkuFactory,
        array $attributeOptions,
        array|Branch|BranchFactory|Collection|null $branches = null,
    ): Sku {

        $self = $priceOrSkuFactory instanceof self
            ? $priceOrSkuFactory
            : self::new(['price' => $priceOrSkuFactory])->withDefaultData($branches);

        foreach (
            collect($attributeOptions)
                ->ensure([AttributeOptionForProductSku::class, AttributeOptionFactory::class]) as $attributeOption
        ) {

            if ($attributeOption instanceof AttributeOptionFactory) {
                $self = $self->has($attributeOption);

                continue;
            }

            /** @var AttributeOptionForProductSku $attributeOption */
            $attribute = Attribute::whereBelongsTo($product)
                ->whereName($attributeOption->attributeName)
                ->first();

            if ($attribute === null) {
                $attribute = Attribute::factory([
                    'name' => $attributeOption->attributeName,
                    'type' => $attributeOption->attributeFieldType,
                    'prefix' => $attributeOption->attributeFieldPrefix,
                    'suffix' => $attributeOption->attributeFieldSuffix,
                ])
                    ->for($product);
            }

            $attributeOptionFactory = AttributeOption::factory(['value' => $attributeOption->attributeOptionValue])
                ->for($attribute);

            $self = $self->has($attributeOptionFactory);
        }

        return $self
            ->for($product)
            ->createOne();
    }

    public function regenerateCode(): self
    {
        return $this
            ->afterCreating(function (Sku $sku) {
                $output = $sku->product->name.' ';

                foreach ($sku->loadMissing('attributeOptions.attribute')->attributeOptions as $attributeOption) {
                    $output .= $attributeOption->attribute->name.' '.$attributeOption->value.' ';
                }

                $sku->update([
                    'code' => Str::slug($output),
                ]);
            });
    }
}
