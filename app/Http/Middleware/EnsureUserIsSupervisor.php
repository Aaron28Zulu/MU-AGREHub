<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class EnsureUserIsSupervisor extends FilamentAuthenticate
{
    /**
     * Authenticate and authorize that the user is an active supervisor or academic overseer.
     * Redirects unauthorized users to '/' with a clean flash notification.
     *
     * @param  Request  $request
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        $user = $guard->user();

        // Enforce supervisor role clearance
        if (! $user->hasRole(UserRole::SUPERVISOR->value) && ! $user->hasAnyRole([
            UserRole::ADMIN->value,
            UserRole::DEAN->value,
            UserRole::RESEARCH_DIRECTOR->value,
        ])) {
            throw new HttpResponseException(
                redirect('/')->with('error', 'Access restricted to authorized academic supervisors.')
            );
        }
    }

    /**
     * Redirect unauthenticated guests to the main login portal.
     *
     * @param  Request  $request
     */
    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
