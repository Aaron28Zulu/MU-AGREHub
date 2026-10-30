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

        // 2. Define initial seed accounts for development and testing, spread across the schools
        $seedUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@rris.mu.ac.zm',
                'role' => UserRole::ADMIN,
                'department' => 'ICT Directorate',
            ],
            [
                'name' => 'Dr. M. Phiri',
                'email' => 'dean@rris.mu.ac.zm',
                'role' => UserRole::DEAN,
                'department' => 'School of Business Studies',
            ],
            [
                'name' => 'Prof. E. Banda',
                'email' => 'director@rris.mu.ac.zm',
                'role' => UserRole::RESEARCH_DIRECTOR,
                'department' => 'Directorate of Research and Graduate Studies',
            ],
            [
                'name' => 'Dr. C. Mulenga',
                'email' => 'cmulenga@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Agricultural Sciences',
            ],
            [
                'name' => 'Dr. J. Tembo',
                'email' => 'jtembo@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Economics',
            ],
            [
                'name' => 'Dr. N. Kabwe',
                'email' => 'nkabwe@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Education',
            ],
            [
                'name' => 'Dr. S. Mwansa',
                'email' => 'smwansa@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Public Health',
            ],
            [
                'name' => 'Mr. B. Chanda',
                'email' => 'bchanda@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Computer Science',
            ],
            [
                'name' => 'Dr. R. Musonda',
                'email' => 'rmusonda@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Physics and Engineering',
            ],
            [
                'name' => 'Dr. L. Sakala',
                'email' => 'lsakala@rris.mu.ac.zm',
                'role' => UserRole::SUPERVISOR,
                'department' => 'Development Studies',
            ],
            [
                'name' => 'Peter Mwaba',
                'email' => 'peter@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Economics',
            ],
            [
                'name' => 'Lukundo Bwalya',
                'email' => 'lukundo@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Agricultural Sciences',
            ],
            [
                'name' => 'Kelvin Mwape',
                'email' => 'kelvin@rris.mu.ac.zm',
                'role' => UserRole::STUDENT,
                'department' => 'Computer Science',
            ],
            [
                'name' => 'Mutinta Hamoonga',
                'email' => 'mutinta@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Nursing',
            ],
            [
                'name' => 'Chipo Lungu',
                'email' => 'chipo@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Education',
            ],
            [
                'name' => 'Natasha Mbewe',
                'email' => 'natasha@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Development Studies',
            ],
            [
                'name' => 'Joseph Banda',
                'email' => 'joseph@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Accounting and Finance',
            ],
            [
                'name' => 'Mwila Kasonde',
                'email' => 'mwila@rris.mu.ac.zm',
                'role' => UserRole::RESEARCHER,
                'department' => 'Engineering',
            ],
            [
                'name' => 'Thandiwe Mumba',
                'email' => 'thandiwe@rris.mu.ac.zm',
                'role' => UserRole::STUDENT,
                'department' => 'Computer Science',
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
