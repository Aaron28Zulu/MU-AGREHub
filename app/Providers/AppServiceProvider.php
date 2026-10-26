<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Services\Ai\AiTextAnalysisService;
use App\Services\Ai\Contracts\AiTextAnalysisServiceInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );

        $this->app->bind(
            AiTextAnalysisServiceInterface::class,
            AiTextAnalysisService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(
            Project::class,
            ProjectPolicy::class
        );

        // Unread supervisor updates badge in the public/student masthead
        View::composer('components.shell', function (ViewInstance $view): void {
            $view->with('unreadCount', auth()->user()?->unreadNotifications()->count() ?? 0);
        });
    }
}
