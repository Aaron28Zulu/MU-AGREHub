<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Lets the administrator sign in as another user to see exactly what they see, then return to their own account.
 * The administrator starts it from the Users screen; the banner on every page posts back here to end it.
 */
class ImpersonationController extends Controller
{
    public const SESSION_KEY = 'impersonator_id';

    /**
     * Sign in as the target user, remembering the administrator who started it. Returns where to send them.
     */
    public static function start(User $admin, User $target): string
    {
        // Nested impersonation would lose track of the real administrator.
        abort_if(session()->has(self::SESSION_KEY), 403, 'Return to your own account first.');
        abort_unless($admin->canImpersonate($target), 403);

        Log::info('User impersonation started', ['admin_id' => $admin->id, 'user_id' => $target->id]);

        self::switchTo($target);
        session()->put(self::SESSION_KEY, $admin->id);
        session()->flash('status', "You are signed in as {$target->name}. Use the banner to return to your account.");

        return AuthController::homeFor($target);
    }

    /**
     * End the impersonation and return to the administrator's own account.
     */
    public function leave(Request $request): RedirectResponse
    {
        $adminId = session()->pull(self::SESSION_KEY);
        $admin = $adminId ? User::find($adminId) : null;

        abort_unless($admin?->canManageUsers(), 403);

        Log::info('User impersonation ended', ['admin_id' => $admin->id, 'user_id' => $request->user()?->id]);

        self::switchTo($admin);

        return redirect('/supervisor/users')->with('status', 'Welcome back to your own account.');
    }

    /**
     * Swap the signed-in account. The stored password hash belongs to the previous account, so it is
     * dropped; otherwise the panel's session check would sign the new account straight out.
     */
    private static function switchTo(User $user): void
    {
        Auth::login($user);
        session()->regenerate();
        session()->forget('password_hash_'.Auth::getDefaultDriver());
    }
}
