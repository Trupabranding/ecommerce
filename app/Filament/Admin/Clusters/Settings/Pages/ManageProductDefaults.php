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
use Domain\Settings\Models\SettingFeature;
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

class ManageProductDefaults extends Page implements HasPermissionPages, HasForms
{
    use PermissionPages;
    use InteractsWithForms;

    protected string $view = 'filament.admin.clusters.settings.pages.product-defaults';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 2;

    #[Override]
    protected static ?string $title = 'Product Defaults';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_status' => $this->getSetting('product_default_status', 'draft'),
            'default_visibility' => $this->getSetting('product_default_visibility', 'public'),
            'auto_publish' => $this->getSetting('product_auto_publish', false),
            'require_sku' => $this->getSetting('product_require_sku', false),
            'reviews_enabled' => $this->getSetting('product_reviews_enabled', true),
            'image_limit_mb' => $this->getSetting('product_image_limit_mb', 10),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Status & Visibility')
                    ->description('Default settings for new products')
                    ->schema([
                        Components\Select::make('default_status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->default('draft'),
                        Components\Select::make('default_visibility')
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private',
                                'hidden' => 'Hidden',
                            ])
                            ->default('public'),
                        Components\Toggle::make('auto_publish')
                            ->label('Auto-Publish on Creation')
                            ->default(false),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Product Requirements')
                    ->description('Validation rules for products')
                    ->schema([
                        Components\Toggle::make('require_sku')
                            ->label('Require SKU')
                            ->default(false),
                        Components\Toggle::make('reviews_enabled')
                            ->label('Enable Customer Reviews')
                            ->default(true),
                        Components\TextInput::make('image_limit_mb')
                            ->label('Maximum Image Size (MB)')
                            ->numeric()
                            ->default(10),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->setSetting('product_default_status', $data['default_status']);
            $this->setSetting('product_default_visibility', $data['default_visibility']);
            $this->setSetting('product_auto_publish', $data['auto_publish']);
            $this->setSetting('product_require_sku', $data['require_sku']);
            $this->setSetting('product_reviews_enabled', $data['reviews_enabled']);
            $this->setSetting('product_image_limit_mb', $data['image_limit_mb']);

            Notification::make()
                ->success()
                ->title('Product defaults saved successfully')
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
