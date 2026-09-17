<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create all application roles defined in UserRole enum
        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        }

        // 2. Define initial seed accounts for development and testing
        $seedUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@agrihub.edu',
                'role' => UserRole::ADMIN,
                'department' => 'IT & Systems',
            ],
            [
                'name' => 'Dr. M. Phiri',
                'email' => 'dean@agrihub.edu',
                'role' => UserRole::DEAN,
                'department' => 'MUSAGRICA Deanery',
            ],
            [
                'name' => 'Prof. E. Banda',
                'email' => 'director@agrihub.edu',
                'role' => UserRole::RESEARCH_DIRECTOR,
                'department' => 'Directorate of Research',
            ],
            [
                'name' => 'Dr. C. Mulenga',
                'email' => 'cmulenga@agrihub.edu',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Crop Science',
            ],
            [
                'name' => 'Dr. J. Tembo',
                'email' => 'jtembo@agrihub.edu',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Agribusiness',
            ],
            [
                'name' => 'Peter Mwaba',
                'email' => 'peter@agrihub.edu',
                'role' => UserRole::RESEARCHER,
                'department' => 'Agribusiness',
            ],
            [
                'name' => 'Lukundo Bwalya',
                'email' => 'lukundo@agrihub.edu',
                'role' => UserRole::RESEARCHER,
                'department' => 'Crop Science',
            ],
            [
                'name' => 'Kelvin Mwape',
                'email' => 'kelvin@agrihub.edu',
                'role' => UserRole::STUDENT,
                'department' => 'Animal Science',
            ],
        ];

        // 3. Seed users and assign their respective Spatie roles
        foreach ($seedUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                    'department' => $userData['department'],
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$userData['role']->value]);
        }
    }
}
