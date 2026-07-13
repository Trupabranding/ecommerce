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

namespace App\Http\Resources\Shop;

use App\Http\Resources\BaseJsonApiResource;
use App\Http\Resources\MediaResource;
use Domain\Shop\Product\Models\Product;
use Illuminate\Http\Request;
use Override;

/**
 * @property-read Product $resource
 */
class ProductResource extends BaseJsonApiResource
{
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'parent_sku' => $this->resource->parent_sku,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => $this->resource->status,
        ];
    }

    #[Override]
    public function toRelationships(Request $request): array
    {
        return [
            'category' => fn () => CategoryResource::make($this->resource->category),
            'media' => fn () => MediaResource::collection($this->resource->media),
            'skus' => fn () => SkuResource::collection($this->resource->skus),
            'brand' => fn () => BrandResource::make($this->resource->brand),
            'tags' => fn () => TagResource::collection($this->resource->tags),
        ];
    }
}
