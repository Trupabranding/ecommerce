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
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\OperationHour\Enums\OperationHourType;
use Illuminate\Http\Request;
use Override;

/**
 * @property-read Branch $resource
 */
class BranchResource extends BaseJsonApiResource
{
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'address' => $this->resource->address,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'website' => $this->resource->website,
            'operation_hours' => $this->when(
                $this->resource->is_operation_hours_enabled,
                [
                    'online' => $this->resource
                        ->operationHoursHumanReadable(OperationHourType::online),
                    'in_store' => $this->resource
                        ->operationHoursHumanReadable(OperationHourType::in_store),
                ],
            ),
        ];
    }

    #[Override]
    public function toRelationships(Request $request): array
    {
        return [
            'media' => fn () => MediaResource::collection($this->resource->media),
        ];
    }
}
