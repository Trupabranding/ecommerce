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

namespace Database\Factories\Support;

use Database\Factories\StaticSupport;
use Exception;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

trait HasMediaFactory
{
    public function hasRandomMedia(?int $maxCount = null, string $collectionName = 'image'): static
    {
        return $this
            ->afterCreating(
                fn (HasMedia $model) => self::seedRandomMedia(
                    $model,
                    collectionName: $collectionName,
                    maximum: $maxCount ?? 3
                )
            );
    }

    public function hasSpecificMedia(): static
    {
        return $this
            ->afterCreating(
                fn (HasMedia $model) => self::seedSpecificMedia($model)
            );
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws Exception
     */
    private static function seedRandomMedia(
        HasMedia $model,
        string $collectionName = 'image',
        int $minimum = 1,
        int $maximum = 3,
    ): void {

        if (app()->runningUnitTests() || app()->isLocal()) {
            self::seedSpecificMedia(model: $model, collectionName: $collectionName);

            return;
        }

        /** @var \Spatie\MediaLibrary\MediaCollections\MediaCollection $mediaCollection */
        /** @phpstan-ignore method.notFound (Call to an undefined method Spatie\MediaLibrary\HasMedia::getRegisteredMediaCollections().) */
        $mediaCollection = $model->getRegisteredMediaCollections()
            ->where('name', $collectionName)
            ->first();

        if ($mediaCollection->singleFile) {

            self::upload($model, $collectionName);

        } else {

            collect()
                ->range(
                    $minimum,
                    collect()
                        ->range($minimum, $maximum)
                        ->random()
                )
                ->map(fn () => self::upload($model, $collectionName));
        }
    }

    /** @throws Exception */
    private static function upload(
        HasMedia $model,
        string $collectionName = 'image',
    ): void {

        if (! StaticSupport::$hasNetworkAccess) {
            self::seedSpecificMedia($model, $collectionName);

            return;
        }

        try {
            /** @phpstan-ignore method.notFound (Call to an undefined method Spatie\MediaLibrary\HasMedia::addMediaFromUrl().) */
            $model
                ->addMediaFromUrl(self::imageUrl())
                ->toMediaCollection($collectionName);
        } catch (UnreachableUrl) {

            StaticSupport::$hasNetworkAccess = false;

        }
    }

    private static function imageUrl(): string
    {
        $faker = Factory::create();
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        return $faker->imageUrl();
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     */
    private static function seedSpecificMedia(
        HasMedia $model,
        string $collectionName = 'image'
    ): void {

        $model
            ->copyMedia(base_path('test_files/1-800x600.jpg'))
            ->toMediaCollection($collectionName);
    }
}
