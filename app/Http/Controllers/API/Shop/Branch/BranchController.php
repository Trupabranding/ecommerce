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

namespace App\Http\Controllers\API\Shop\Branch;

use App\Http\Resources\Shop\BranchResource;
use Domain\Shop\Branch\Enums\BranchStatus;
use Domain\Shop\Branch\Models\Branch;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\RouteAttributes\Attributes\Resource;

#[Resource('branches', only: ['index', 'show'])]
class BranchController
{
    /**
     * @unauthenticated
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<BranchResource>>
     */
    public function index(): mixed
    {
        return BranchResource::collection(
            QueryBuilder::for(
                Branch::where('status', BranchStatus::enabled)
                    ->with([
                        'operationHoursOnline',
                        'operationHoursInStore',
                    ])
            )
                ->allowedIncludes(['media'])
                ->allowedSorts([
                    'name',
                    'updated_at',
                    config()->string('eloquent-sortable.order_column_name'),
                ])
                ->defaultSort(config()->string('eloquent-sortable.order_column_name'))
                ->jsonPaginate()
        );
    }

    /**
     * @unauthenticated
     */
    public function show(string $branch): mixed
    {
        return BranchResource::make(
            QueryBuilder::for(
                Branch::whereStatus(BranchStatus::enabled)
                    ->where((new Branch)->getRouteKeyName(), $branch)
                    ->with([
                        'operationHoursOnline',
                        'operationHoursInStore',
                    ])
            )
                ->allowedIncludes(['media'])
                ->firstOrFail()
        );
    }
}
