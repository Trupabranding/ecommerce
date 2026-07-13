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

namespace App\Providers\Macros;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

/**
 * @mixin Blueprint
 *
 * @codeCoverageIgnore
 */
class BluePrintMixin
{
    public function phpEnum(): Closure
    {
        return function (string $column, ?string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'PHP backed enum'
                : $comment.' (PHP backed enum)';

            return $this->string($column, 100)->comment($comment)->index();
        };
    }

    public function eloquentSortable(): Closure
    {
        return function (?string $column = null, ?string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'manage by spatie/eloquent-sortable'
                : $comment.' (manage by spatie/eloquent-sortable)';

            return $this->unsignedBigInteger(
                $column ?? config()->string('eloquent-sortable.order_column_name')
            )->comment($comment);
        };
    }

    public function money(): Closure
    {
        return function (string $column, ?string $comment = null): ColumnDefinition {
            $comment = is_null($comment)
                ? 'for money'
                : $comment.' (for money)';

            return $this->unsignedInteger($column)->comment($comment);
        };
    }
}
