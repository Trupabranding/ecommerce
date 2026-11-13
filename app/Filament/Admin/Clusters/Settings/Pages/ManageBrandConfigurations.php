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

class ManageBrandConfigurations extends Page implements HasPermissionPages, HasForms
{
    use PermissionPages;
    use InteractsWithForms;

    protected string $view = 'filament.admin.clusters.settings.pages.brand-configurations';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 5;

    #[Override]
    protected static ?string $title = 'Brand Configurations';

    #[Override]
    protected static ?string $navigationLabel = 'Brands';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'display_color_enabled' => $this->getSetting('brand_display_color_enabled', true),
            'pricing_rules_enabled' => $this->getSetting('brand_pricing_rules_enabled', true),
            'discount_max_percentage' => $this->getSetting('brand_discount_max_percentage', 30),
            'compliance_required' => $this->getSetting('brand_compliance_required', true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Display Settings')
                    ->description('Control brand appearance and identity')
                    ->schema([
                        Components\Toggle::make('display_color_enabled')
                            ->label('Enable Brand Colors')
                            ->default(true),
                        Components\Toggle::make('logo_customization')
                            ->label('Allow Logo Customization')
                            ->default(true),
                        Components\Textarea::make('brand_story')
                            ->label('Brand Story Template')
                            ->rows(3),
                    ]),

                SchemaComponents\Section::make('Pricing Rules')
                    ->description('Define pricing constraints per brand')
                    ->schema([
                        Components\Toggle::make('pricing_rules_enabled')
                            ->label('Enable Pricing Rules')
                            ->default(true),
                        Components\TextInput::make('discount_max_percentage')
                            ->label('Maximum Discount (%)')
                            ->numeric()
                            ->default(30)
                            ->minValue(0)
                            ->maxValue(100),
                        Components\TextInput::make('minimum_margin')
                            ->label('Minimum Margin (%)')
                            ->numeric()
                            ->default(15)
                            ->minValue(0),
                    ]),

                SchemaComponents\Section::make('Compliance Requirements')
                    ->description('Quality and certification requirements')
                    ->schema([
                        Components\Toggle::make('compliance_required')
                            ->label('Require Compliance Verification')
                            ->default(true),
                        Components\Select::make('quality_tier')
                            ->options([
                                'standard' => 'Standard',
                                'premium' => 'Premium',
                                'luxury' => 'Luxury',
                            ]),
                        Components\Toggle::make('variants_required')
                            ->label('Require Variant Configuration')
                            ->default(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->setSetting('brand_display_color_enabled', $data['display_color_enabled']);
            $this->setSetting('brand_pricing_rules_enabled', $data['pricing_rules_enabled']);
            $this->setSetting('brand_discount_max_percentage', $data['discount_max_percentage']);
            $this->setSetting('brand_compliance_required', $data['compliance_required']);

            Notification::make()
                ->success()
                ->title('Brand configurations saved successfully')
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
