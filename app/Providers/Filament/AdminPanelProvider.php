<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // ── Branding ─────────────────────────────────────────────
            ->brandName('GoBazaar Admin')
            ->brandLogo(fn () => view('filament.brand'))
            ->favicon(asset('favicon.ico'))

            // ── Colors — GoBazaar teal/green primary ─────────────────
            ->colors([
                'primary'   => Color::hex('#00897b'),   // teal-700
                'gray'      => Color::Slate,
                'info'      => Color::Sky,
                'success'   => Color::Emerald,
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
            ])

            // ── Layout ────────────────────────────────────────────────
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->darkMode(true)

            // ── Navigation groups (controls ordering and icons) ───────
            ->navigationGroups([
                NavigationGroup::make('Content')
                    ->icon('heroicon-o-document-text')
                    ->collapsed(false),
                NavigationGroup::make('Directory')
                    ->icon('heroicon-o-building-storefront')
                    ->collapsed(false),
                NavigationGroup::make('Finance')
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(false),
                NavigationGroup::make('Advertising')
                    ->icon('heroicon-o-megaphone')
                    ->collapsed(true),
                NavigationGroup::make('Moderation')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed(true),
                NavigationGroup::make('System')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])

            // ── Resources / Pages / Widgets ───────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverviewWidget::class,
                \App\Filament\Widgets\RecentActivityWidget::class,
            ])

            // ── Middleware ────────────────────────────────────────────
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
            ]);
    }
}
