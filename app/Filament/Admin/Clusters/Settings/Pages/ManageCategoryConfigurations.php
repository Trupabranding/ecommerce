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
use Domain\Settings\Models\AdminSetting;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Forms\Components;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;
use Override;

class ManageCategoryConfigurations extends Page implements HasPermissionPages, HasForms
{
    use PermissionPages;
    use InteractsWithForms;

    protected string $view = 'filament.admin.clusters.settings.pages.category-configurations';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 4;

    #[Override]
    protected static ?string $title = 'Category Configurations';

    #[Override]
    protected static ?string $navigationLabel = 'Categories';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'display_template_required' => $this->getSetting('category_display_template_required', true),
            'product_images_required' => $this->getSetting('category_product_images_required', true),
            'tax_rate_default' => $this->getSetting('category_tax_rate_default', 0),
            'discount_enabled' => $this->getSetting('category_discount_enabled', true),
            'inventory_tracking' => $this->getSetting('category_inventory_tracking', true),
            'sku_format' => $this->getSetting('category_sku_format', 'AUTO'),
            'seo_required' => $this->getSetting('category_seo_required', false),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Display Rules')
                    ->description('Control how categories appear')
                    ->schema([
                        Components\Toggle::make('display_template_required')
                            ->label('Require Display Template')
                            ->default(true),
                        Components\Textarea::make('description_template')
                            ->label('Description Template')
                            ->rows(2),
                    ]),

                SchemaComponents\Section::make('Product Rules')
                    ->description('Requirements for products in this category')
                    ->schema([
                        Components\Toggle::make('product_images_required')
                            ->label('Require Product Images')
                            ->default(true),
                        Components\TextInput::make('min_images')
                            ->label('Minimum Images Required')
                            ->numeric()
                            ->default(1),
                    ]),

                SchemaComponents\Section::make('Pricing Rules')
                    ->description('Pricing and tax configuration')
                    ->schema([
                        Components\TextInput::make('tax_rate_default')
                            ->label('Default Tax Rate (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Components\Toggle::make('discount_enabled')
                            ->label('Allow Discounts')
                            ->default(true),
                    ]),

                SchemaComponents\Section::make('Inventory Rules')
                    ->description('Stock and SKU tracking')
                    ->schema([
                        Components\Toggle::make('inventory_tracking')
                            ->label('Enable Inventory Tracking')
                            ->default(true),
                        Components\Select::make('sku_format')
                            ->label('SKU Format')
                            ->options([
                                'AUTO' => 'Auto-Generated',
                                'MANUAL' => 'Manual Entry',
                                'PREFIX' => 'Category Prefix',
                            ])
                            ->default('AUTO'),
                    ]),

                SchemaComponents\Section::make('SEO Rules')
                    ->description('Search engine optimization settings')
                    ->schema([
                        Components\Toggle::make('seo_required')
                            ->label('Require SEO Configuration')
                            ->default(false),
                        Components\Textarea::make('meta_description_template')
                            ->label('Meta Description Template')
                            ->rows(2),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->setSetting('category_display_template_required', $data['display_template_required']);
            $this->setSetting('category_product_images_required', $data['product_images_required']);
            $this->setSetting('category_tax_rate_default', $data['tax_rate_default']);
            $this->setSetting('category_discount_enabled', $data['discount_enabled']);
            $this->setSetting('category_inventory_tracking', $data['inventory_tracking']);
            $this->setSetting('category_sku_format', $data['sku_format']);
            $this->setSetting('category_seo_required', $data['seo_required']);

            Notification::make()
                ->success()
                ->title('Category configurations saved successfully')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->send();
        }
    }

    private function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = AdminSetting::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    private function setSetting(string $key, mixed $value): void
    {
        AdminSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'is_global' => true]
        );
    }
}
