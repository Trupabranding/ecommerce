<?php

/*
 * Copyright (c) 2026 Trupa Technologies
 * All rights reserved.
 *
 * Developed by Boncanca Collins
 * GitHub: @iamtomc, @boncanca
 * Organization: trupabranding
 */

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Product\Models\Product;
use Illuminate\Database\Seeder;

class EcommerceStorefrontSeeder extends Seeder
{
    public function run(): void
    {
        // This seeder prepares products from ProductSeeder for storefront exposure.
        // The actual storefront data (images, descriptions, pricing) comes from
        // the ProductSeeder and existing domain models.
        //
        // When products are created via ProductSeeder, they automatically have:
        // - Categories and brands set via relationships
        // - SKUs with prices and attributes
        // - Media attached via hasRandomMedia()
        // - Status set via inStockStatus()
        //
        // The frontend API will format these for the storefront.
        // No additional products needed here.
    }
}
