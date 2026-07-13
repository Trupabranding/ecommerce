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

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\EditProfile;
use App\Filament\Admin\Pages\Backups;
use App\Filament\Admin\Pages\Dashboard\MainDashboard;
use App\Http\Middleware\SentryUserContext;
use App\Jobs\QueueName;
use App\Settings\SiteSettings;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionPlugin;
use MartinPetricko\FilamentSentryFeedback\Entities\SentryUser;
use MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin;
use Override;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    #[Override]
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName(fn () => app(SiteSettings::class)->name)
            ->favicon(fn () => app(SiteSettings::class)->getSiteFaviconUrl())
            ->brandLogo(fn () => app(SiteSettings::class)->getSiteLogoUrl())
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login()
            ->profile(EditProfile::class)
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable(),
            ])
            ->emailVerification()
            ->passwordReset()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->pages([
                MainDashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                SentryUserContext::class,
            ], true)
            ->authMiddleware([
                Authenticate::class,
                //                SetTheme::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn () => trans('Shop')),
                NavigationGroup::make()
                    ->label(fn () => trans('Access')),
                NavigationGroup::make()
                    ->label(fn () => trans('Configurations')),
                NavigationGroup::make()
                    ->label(fn () => trans('Documentation')),
                NavigationGroup::make()
                    ->label(fn () => trans('System')),
            ])
            ->navigationItems([
                NavigationItem::make('API Documentation v1')
                    ->url(fn () => route('api.docs.v1'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->group(fn () => trans('Documentation'))
                    ->sort(1)
                    ->visible(fn () => filament_admin()->can('viewApiDocs')),
                NavigationItem::make('Log Viewer')
                    ->url(fn () => route('log-viewer.index'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedFire)
                    ->group(fn () => trans('System'))
                    ->sort(2)
                    ->visible(fn () => filament_admin()->can('viewLogViewer')),
                NavigationItem::make('Horizon')
                    ->url(fn () => route('horizon.index'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedGlobeAmericas)
                    ->group(fn () => trans('System'))
                    ->sort(3)
                    ->visible(fn () => filament_admin()->can('viewHorizon')),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->navigationGroup(fn () => trans('System'))
                    ->authorize(fn () => filament_admin()->isSuperAdmin()),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class)
                    ->usingQueue(QueueName::DB_BACKUPS->value)
                    ->authorize(fn () => filament_admin()->isSuperAdmin()),
                //                ThemesPlugin::make(),
                FilamentPermissionPlugin::make(),
                FilamentSentryFeedbackPlugin::make()
                    ->showBranding(true)
                    ->sentryUser(function (): ?SentryUser {
                        $admin = Auth::guard('admin')->user();

                        if ($admin === null) {
                            return null;
                        }

                        return new SentryUser($admin->name, $admin->email);
                    }),
                EnvironmentIndicatorPlugin::make()
                    ->visible(true)
                    ->showDebugModeWarning()
                    ->color(fn () => match ($this->app->environment()) {
                        'local' => Color::Green,
                        'staging' => Color::Orange,
                        'testing' => Color::Gray,
                        default => Color::Blue,
                    }),
                GlobalSearchModalPlugin::make(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->spa(hasPrefetching: true)
            ->unsavedChangesAlerts(! $this->app->environment('local'))
            ->databaseNotifications()
            ->databaseTransactions()
            ->defaultThemeMode(ThemeMode::Dark)
            ->strictAuthorization($this->app->runningUnitTests()) //  conflict of global search
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function (): string {

                    $key = config('app-sentry.session_replay');

                    if ($key === null) {
                        return '';
                    }

                    $url = "https://js.sentry-cdn.com/$key.min.js";

                    return Blade::render("<script src=\"$url\" crossorigin=\"anonymous\"></script>");
                },
            )
//            ->renderHook(
//                PanelsRenderHook::STYLES_AFTER,
//                fn (): string => Blade::render('<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">'),
//            )
//            ->renderHook(
//                PanelsRenderHook::BODY_END,
//                fn (): string => Blade::render('<x-support-bubble />'),
//            )
            ->renderHook(
                PanelsRenderHook::PAGE_END,
                fn () => new HtmlString('
                        <p>
                            Powered by
                            <a
                                href="https://github.com/lloricode"
                                target="_blank"
                            >
                                lloricode
                            </a>
                        </p>
                    '),
            );
    }
}
