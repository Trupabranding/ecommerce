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
use App\Filament\Branch\Pages\Dashboard\MainDashboard;
use App\Http\Middleware\ApplyBranchTenantScopes;
use App\Http\Middleware\SentryUserContext;
use App\Settings\SiteSettings;
use Domain\Shop\Branch\Models\Branch;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
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
use MartinPetricko\FilamentSentryFeedback\Entities\SentryUser;
use MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin;
use Override;

class BranchPanelProvider extends PanelProvider
{
    #[Override]
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('branch')
            ->brandName(fn () => app(SiteSettings::class)->name)
            ->favicon(fn () => app(SiteSettings::class)->getSiteFaviconUrl())
            ->brandLogo(fn () => app(SiteSettings::class)->getSiteLogoUrl())
            ->path('admin/branch')
            ->authGuard('admin')
            ->tenant(Branch::class)
            ->login()
            ->profile(EditProfile::class)
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable(),
            ])
            ->emailVerification()
            ->passwordReset()
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: app_path('Filament/Branch/Resources'), for: 'App\\Filament\\Branch\\Resources')
            ->discoverPages(in: app_path('Filament/Branch/Pages'), for: 'App\\Filament\\Branch\\Pages')
            ->pages([
                MainDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Branch/Widgets'), for: 'App\\Filament\\Branch\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->spa()
//            ->unsavedChangesAlerts(! $this->app->isLocal())
            ->databaseNotifications()
            ->databaseTransactions()
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
            ->authMiddleware([
                Authenticate::class,
                SentryUserContext::class,
            ])
            ->tenantMiddleware([
                //                SetTheme::class,
            ])
            ->tenantMiddleware([
                ApplyBranchTenantScopes::class,
            ], isPersistent: true)
            ->plugins([
                //                ThemesPlugin::make(),
                FilamentSentryFeedbackPlugin::make()
                    ->showBranding(true)
                    ->sentryUser(function (): ?SentryUser {
                        $admin = Auth::guard('admin')->user();

                        if ($admin === null) {
                            return null;
                        }

                        return new SentryUser($admin->name, $admin->email);
                    }),
            ])
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
