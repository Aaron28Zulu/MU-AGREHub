<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Supervisor\Resources\SupervisorProjectResource;
use App\Filament\Supervisor\Widgets\SupervisorStatsOverviewWidget;
use App\Http\Middleware\EnsureUserIsSupervisor;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SupervisorPanelProvider extends PanelProvider
{
    /**
     * Configure the dedicated Academic Supervisor Portal panel.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('supervisor')
            ->path('supervisor')
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->brandName('AgriHub | Supervisor Portal')
            ->favicon(asset('favicon.ico'))
            ->resources([
                SupervisorProjectResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                SupervisorStatsOverviewWidget::class,
                Widgets\AccountWidget::class,
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
            ->authMiddleware([
                EnsureUserIsSupervisor::class,
            ]);
    }
}
