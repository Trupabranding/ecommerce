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

namespace App\Http\Controllers\API\Shop\Product;

use App\Http\Resources\Shop\ProductResource;
use Domain\Shop\Product\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Resource;

#[Resource('products', only: ['index', 'show'])]
class ProductController
{
    /**
     * @unauthenticated
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<ProductResource>>
     */
    public function index(): mixed
    {
        return ProductResource::collection(
            QueryBuilder::for(
                Product::class
            )
                ->allowedIncludes([
                    'brand.media',
                    'media',
                    'skus.attributeOptions.attribute',
                    'skus.media',
                    'skus.skuStocks',
                    'category.parent',
                    'tags',
                ])
                ->allowedSorts([
                    'name', 'status', 'updated_at',
                    config()->string('eloquent-sortable.order_column_name'),
                ])
                ->allowedFilters([
                    'skus.skuStocks.branch.slug',
                ])
                ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
                ->jsonPaginate()
        );
    }

    /**
     * @unauthenticated
     */
    public function show(string $product): ProductResource
    {
        return ProductResource::make(
            QueryBuilder::for(
                Product::where((new Product)->getRouteKeyName(), $product)
            )
                ->allowedIncludes([
                    'brand.media',
                    'media',
                    'skus.attributeOptions.attribute',
                    'skus.media',
                    'skus.skuStocks',
                    'category.parent',
                    'tags',
                ])
                ->firstOrFail()
        );
    }
}
