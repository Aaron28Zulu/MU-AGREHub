<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Office of Research and Innovation staff were seeded as student researchers who also held the office role,
 * so the workspace labelled them "Student Researcher" and opened the student portal for them. Staff of the
 * office keep the office role only. Students who serve on the office (they study in another department) are
 * left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        // A fresh database has no roles yet; the seeders create office staff correctly.
        if (! Schema::hasTable('roles') || ! Role::where('name', UserRole::RESEARCH_OFFICER->value)->exists()) {
            return;
        }

        User::query()
            ->where('department', 'Office of Research and Innovation')
            ->role(UserRole::RESEARCH_OFFICER->value)
            ->each(function (User $user): void {
                $user->forceFill(['role' => UserRole::RESEARCH_OFFICER])->save();

                foreach ([UserRole::RESEARCHER, UserRole::STUDENT] as $studentRole) {
                    if ($user->hasRole($studentRole->value)) {
                        $user->removeRole($studentRole->value);
                    }
                }
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Restoring the student role would bring the bug back, so this migration is not reversed.
    }
};
