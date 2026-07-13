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
use Domain\Shop\Category\Models\Category;
use Illuminate\Http\Request;
use Override;

/**
 * @property-read Category $resource
 */
class CategoryResource extends BaseJsonApiResource
{
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'description' => $this->resource->description,
        ];
    }

    #[Override]
    public function toRelationships(Request $request): array
    {
        return [
            'parent' => fn () => $this->when(
                $this->resource !== null,
                fn () => self::make(
                    $this->resource->parent
                )
            ),
            'media' => fn () => $this->when(
                $this->resource !== null,
                fn () => MediaResource::collection(
                    $this->resource->media
                )
            ),
            'products' => fn () => ProductResource::collection($this->resource->products),
        ];
    }
}
