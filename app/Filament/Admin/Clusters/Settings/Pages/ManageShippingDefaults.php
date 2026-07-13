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

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Override;

class ManageShippingDefaults extends Page implements HasPermissionPages
{
    use PermissionPages;

    protected string $view = 'filament.admin.clusters.settings.pages.shipping-defaults';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 6;

    #[Override]
    protected static ?string $title = 'Shipping Defaults';
}
