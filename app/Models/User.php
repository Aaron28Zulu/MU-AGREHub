<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Projects where the user is the primary student author.
     *
     * @return HasMany<Project, $this>
     */
    public function projectsAsStudent(): HasMany
    {
        return $this->hasMany(Project::class, 'student_id');
    }

    /**
     * Projects where the user is the assigned supervisor.
     *
     * @return HasMany<Project, $this>
     */
    public function projectsAsSupervisor(): HasMany
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    /**
     * Evaluations submitted by this supervisor.
     *
     * @return HasMany<SupervisorEvaluation, $this>
     */
    public function supervisorEvaluations(): HasMany
    {
        return $this->hasMany(SupervisorEvaluation::class, 'supervisor_id');
    }

    /**
     * Determine whether the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'supervisor') {
            return $this->hasRole(UserRole::SUPERVISOR->value) || $this->hasAnyRole([
                UserRole::ADMIN->value,
                UserRole::DEAN->value,
                UserRole::RESEARCH_DIRECTOR->value,
            ]);
        }

        return false;
    }
}
