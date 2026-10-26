<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\AssociationEventResource;
use App\Filament\Resources\ChallengeEntryResource;
use App\Filament\Resources\CropPlotResource;
use App\Filament\Resources\InnovationChallengeResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Supervisor\Pages\Dashboard;
use App\Filament\Supervisor\Resources\SupervisorProjectResource;
use App\Filament\Supervisor\Widgets\ReviewQueueWidget;
use App\Filament\Supervisor\Widgets\SupervisorStatsOverviewWidget;
use App\Filament\Widgets\EmergingGapsTable;
use App\Filament\Widgets\ResearchOfficeStatsWidget;
use App\Filament\Widgets\SaturatedDomainsChart;
use App\Http\Middleware\EnsureUserIsSupervisor;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SupervisorPanelProvider extends PanelProvider
{
    /**
     * Configure the lecturer portal (supervisors, deans, research directors and admins).
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('supervisor')
            ->path('supervisor')
            ->brandName('AgriHub Lecturer Portal')
            ->brandLogo(fn () => view('filament.brand.logo'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::hex('#2a523c'),
                'gray' => Color::Stone,
                'info' => Color::Sky,
                'success' => Color::hex('#35674a'),
                'warning' => Color::hex('#b07a24'),
                'danger' => Color::Red,
            ])
            ->font('Instrument Sans')
            ->maxContentWidth(MaxWidth::SevenExtraLarge)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->navigationGroups([
                NavigationGroup::make('Supervision'),
                NavigationGroup::make('Research & Innovation Office'),
                NavigationGroup::make('Research intelligence'),
                NavigationGroup::make('School oversight'),
                NavigationGroup::make('Archive'),
            ])
            ->navigationItems([
                NavigationItem::make('Public research archive')
                    ->url(fn (): string => route('repository'))
                    ->icon('heroicon-o-building-library')
                    ->group('Archive')
                    ->sort(1),
                NavigationItem::make('Research gaps index')
                    ->url(fn (): string => route('gaps'))
                    ->icon('heroicon-o-light-bulb')
                    ->group('Archive')
                    ->sort(2),
                NavigationItem::make('Innovation pages')
                    ->url(fn (): string => route('innovation'))
                    ->icon('heroicon-o-sparkles')
                    ->group('Archive')
                    ->sort(3),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Back to the archive')
                    ->url(fn (): string => route('home'))
                    ->icon('heroicon-o-arrow-uturn-left'),
            ])
            // Layer the AgriHub skin over Filament's own stylesheet (a viteTheme() would replace it).
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))
                    ? Blade::render("@vite('resources/css/filament/lecturer.css')")
                    : '',
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('<span class="agrihub-role-pill">{{ auth()->user()?->role?->label() }}</span>'),
            )
            ->resources([
                SupervisorProjectResource::class,
                ProjectResource::class,
                InnovationChallengeResource::class,
                ChallengeEntryResource::class,
                AssociationEventResource::class,
                CropPlotResource::class,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                ResearchOfficeStatsWidget::class,
                SupervisorStatsOverviewWidget::class,
                ReviewQueueWidget::class,
                SaturatedDomainsChart::class,
                EmergingGapsTable::class,
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
