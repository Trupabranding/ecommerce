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

class ManageIntegrations extends Page implements HasPermissionPages, HasForms
{
    use PermissionPages;
    use InteractsWithForms;

    protected string $view = 'filament.admin.clusters.settings.pages.integrations';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    #[Override]
    protected static ?string $cluster = Settings::class;

    #[Override]
    protected static ?int $navigationSort = 7;

    #[Override]
    protected static ?string $title = 'Integrations';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'stripe_enabled' => $this->getSetting('integration_stripe_enabled', true),
            'stripe_api_key' => $this->getSetting('integration_stripe_api_key', ''),
            'mailchimp_enabled' => $this->getSetting('integration_mailchimp_enabled', false),
            'sendgrid_enabled' => $this->getSetting('integration_sendgrid_enabled', false),
            'slack_enabled' => $this->getSetting('integration_slack_enabled', false),
            'slack_webhook' => $this->getSetting('integration_slack_webhook', ''),
            'api_rate_limit' => $this->getSetting('integration_api_rate_limit', 60),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Payment Processing')
                    ->description('Configure payment gateway integrations')
                    ->schema([
                        Components\Toggle::make('stripe_enabled')
                            ->label('Enable Stripe')
                            ->default(true),
                        Components\TextInput::make('stripe_api_key')
                            ->label('Stripe API Key')
                            ->password()
                            ->revealable(),
                    ]),

                SchemaComponents\Section::make('Email & Marketing')
                    ->description('Email service and marketing automation')
                    ->schema([
                        Components\Toggle::make('mailchimp_enabled')
                            ->label('Enable Mailchimp')
                            ->default(false),
                        Components\TextInput::make('mailchimp_api_key')
                            ->label('Mailchimp API Key')
                            ->password()
                            ->revealable(),
                        Components\Toggle::make('sendgrid_enabled')
                            ->label('Enable SendGrid')
                            ->default(false),
                        Components\TextInput::make('sendgrid_api_key')
                            ->label('SendGrid API Key')
                            ->password()
                            ->revealable(),
                    ]),

                SchemaComponents\Section::make('Notifications & Chat')
                    ->description('Real-time alerts and team communication')
                    ->schema([
                        Components\Toggle::make('slack_enabled')
                            ->label('Enable Slack Integration')
                            ->default(false),
                        Components\TextInput::make('slack_webhook')
                            ->label('Slack Webhook URL')
                            ->url(),
                    ]),

                SchemaComponents\Section::make('API Configuration')
                    ->description('API rate limiting and access settings')
                    ->schema([
                        Components\TextInput::make('api_rate_limit')
                            ->label('Rate Limit (Requests/min)')
                            ->numeric()
                            ->default(60)
                            ->minValue(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $this->setSetting('integration_stripe_enabled', $data['stripe_enabled']);
            $this->setSetting('integration_stripe_api_key', $data['stripe_api_key']);
            $this->setSetting('integration_mailchimp_enabled', $data['mailchimp_enabled']);
            $this->setSetting('integration_sendgrid_enabled', $data['sendgrid_enabled']);
            $this->setSetting('integration_slack_enabled', $data['slack_enabled']);
            $this->setSetting('integration_slack_webhook', $data['slack_webhook']);
            $this->setSetting('integration_api_rate_limit', $data['api_rate_limit']);

            Notification::make()
                ->success()
                ->title('Integration settings saved successfully')
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
