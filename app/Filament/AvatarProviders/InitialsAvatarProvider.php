<?php

declare(strict_types=1);

namespace App\Filament\AvatarProviders;

use App\Models\User;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Initials on a pale forest disc, matching the avatar in the public archive masthead.
 * Rendered as an inline SVG so the portal does not depend on a third-party avatar service.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $initials = $record instanceof User ? $record->initials() : '';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" fill="#e1ece4"/>'
            .'<text x="32" y="32" dy=".35em" text-anchor="middle" fill="#224232" '
            .'font-family="Google Sans, Segoe UI, Roboto, Helvetica, Arial, sans-serif" font-size="24" font-weight="700">'
            .e($initials)
            .'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
