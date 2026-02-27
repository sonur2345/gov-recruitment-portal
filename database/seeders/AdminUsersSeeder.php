<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = env('DEMO_DEFAULT_PASSWORD', 'Admin@12345');

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@gov.in', 'role' => 'SuperAdmin'],
            ['name' => 'Portal Admin', 'email' => 'admin@gov.in', 'role' => 'Admin'],
            ['name' => 'DEO Officer', 'email' => 'deo@gov.in', 'role' => 'DEO'],
            ['name' => 'Scrutiny Officer', 'email' => 'scrutiny@gov.in', 'role' => 'ScrutinyOfficer'],
            ['name' => 'Merit Admin', 'email' => 'merit@gov.in', 'role' => 'MeritAdmin'],
            ['name' => 'DV Committee', 'email' => 'dvcommittee@gov.in', 'role' => 'DVCommittee'],
            ['name' => 'Evaluator', 'email' => 'evaluator@gov.in', 'role' => 'Evaluator'],
            ['name' => 'Auditor', 'email' => 'auditor@gov.in', 'role' => 'Auditor'],
            ['name' => 'Candidate User', 'email' => 'candidate@gov.in', 'role' => 'Candidate'],
        ];

        foreach ($users as $seedUser) {
            $user = User::query()->firstOrCreate(
                ['email' => $seedUser['email']],
                [
                    'name' => $seedUser['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($defaultPassword),
                ]
            );

            if (!$user->hasRole($seedUser['role'])) {
                $user->assignRole($seedUser['role']);
            }
        }
    }
}
