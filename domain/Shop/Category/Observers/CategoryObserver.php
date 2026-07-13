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

namespace Domain\Shop\Category\Observers;

use App\Filament\Resources\Caching\CategoryCache;
use App\Observers\LogAttemptDeleteResource;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Category\Models\EloquentBuilder\CategoryEloquentBuilder;
use Domain\Shop\Product\Models\EloquentBuilder\ProductEloquentBuilder;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class CategoryObserver
{
    use LogAttemptDeleteResource;

    /**
     * @throws Halt
     */
    public function deleting(Category $category): void
    {
        $category->loadCount([
            'products' => function (ProductEloquentBuilder $query) {
                $query->withTrashed();
            },
            'children' => function (CategoryEloquentBuilder $query) {
                $query->withTrashed();
            },
        ]);

        if ($category->products_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $category,
                trans('Can not delete category with associated products.'),
                'products',
                $category->products_count
            );

            abort(403);
        }
        if ($category->children_count > 0) {

            self::abortThenLogAttemptDeleteRelationCount(
                $category,
                trans('Can not delete category with associated children.'),
                'children',
                $category->children_count
            );

        }
    }

    public function updating(Category $category): void
    {
        $category->loadProductCountWithTrashed();

        if ($category->parent_uuid === null && $category->products_count > 0) {

            $message = trans('Can not remove parent category with associated products.');

            if (Filament::isServing()) {

                Notification::make()
                    ->title($message)
                    ->warning()
                    ->send();

                throw (new Halt)->rollBackDatabaseTransaction();
            } else {

                abort(403, $message);
            }

        }
    }

    public function created(Category $category): void
    {
        CategoryCache::invalidate();
    }

    public function updated(Category $category): void
    {
        CategoryCache::invalidate();
    }

    public function deleted(Category $category): void
    {
        CategoryCache::invalidate();
    }

    public function restored(Category $category): void
    {
        CategoryCache::invalidate();
    }
}
