<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStudent
{
    /**
     * Handle an incoming request.
     * Restricts access to students and student researchers.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->hasAnyRole([UserRole::STUDENT->value, UserRole::RESEARCHER->value, UserRole::ADMIN->value])) {
            abort(Response::HTTP_FORBIDDEN, 'Access restricted to registered student researchers.');
        }

        return $next($request);
    }
}
