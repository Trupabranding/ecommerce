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

class ManageShippingDefaults extends Page implements HasPermissionPages, HasForms
{
    use PermissionPages;
    use InteractsWithForms;

    protected string $view = 'filament.admin.clusters.settings.pages.shipping-defaults';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 6;

    #[Override]
    protected static ?string $title = 'Shipping Defaults';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_method' => $this->getSetting('shipping_default_method', 'standard'),
            'providers' => $this->getSetting('shipping_providers', ['USPS', 'FedEx', 'UPS']),
            'free_shipping_threshold' => $this->getSetting('shipping_free_threshold', 100),
            'pickup_available' => $this->getSetting('shipping_pickup_available', false),
            'standard_delivery_days' => $this->getSetting('shipping_standard_days', 5),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Shipping Methods')
                    ->description('Configure available shipping providers')
                    ->schema([
                        Components\Select::make('default_method')
                            ->label('Default Shipping Method')
                            ->options([
                                'standard' => 'Standard (3-5 days)',
                                'express' => 'Express (1-2 days)',
                                'overnight' => 'Overnight',
                                'pickup' => 'In-Store Pickup',
                            ])
                            ->default('standard'),
                        Components\TagsInput::make('providers')
                            ->label('Shipping Providers')
                            ->suggestions(['USPS', 'FedEx', 'UPS', 'DHL'])
                            ->default(['USPS', 'FedEx', 'UPS']),
                        Components\TextInput::make('standard_delivery_days')
                            ->label('Standard Delivery (Days)')
                            ->numeric()
                            ->default(5),
                    ]),

                SchemaComponents\Section::make('Shipping Policies')
                    ->description('Define shipping rules and costs')
                    ->schema([
                        Components\TextInput::make('free_shipping_threshold')
                            ->label('Free Shipping Threshold ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(100)
                            ->minValue(0),
                        Components\Toggle::make('pickup_available')
                            ->label('Enable In-Store Pickup')
                            ->default(false),
                        Components\TextInput::make('handling_fee')
                            ->label('Default Handling Fee ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->setSetting('shipping_default_method', $data['default_method']);
            $this->setSetting('shipping_providers', $data['providers']);
            $this->setSetting('shipping_free_threshold', $data['free_shipping_threshold']);
            $this->setSetting('shipping_pickup_available', $data['pickup_available']);
            $this->setSetting('shipping_standard_days', $data['standard_delivery_days']);

            Notification::make()
                ->success()
                ->title('Shipping defaults saved successfully')
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
