<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage_posts',
            'log_postal_intake',
            'verify_dd',
            'scrutinize_applications',
            'shortlist_candidates',
            'evaluate_skill_test',
            'generate_merit',
            'verify_documents',
            'generate_appointment',
            'view_reports',
            'view_audit_logs',
            'manage_grievances',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $rolePermissions = [
            'SuperAdmin' => $permissions,
            'Admin' => [
                'manage_posts',
                'log_postal_intake',
                'verify_dd',
                'scrutinize_applications',
                'shortlist_candidates',
                'evaluate_skill_test',
                'generate_merit',
                'verify_documents',
                'generate_appointment',
                'view_reports',
                'view_audit_logs',
                'manage_grievances',
            ],
            'DEO' => [
                'log_postal_intake',
            ],
            'ScrutinyOfficer' => [
                'scrutinize_applications',
            ],
            'MeritAdmin' => [
                'generate_merit',
                'view_reports',
            ],
            'DVCommittee' => [
                'verify_documents',
                'view_reports',
            ],
            'Evaluator' => [
                'evaluate_skill_test',
                'view_reports',
            ],
            'Auditor' => [
                'view_reports',
                'view_audit_logs',
            ],
            'Candidate' => [],
        ];

        foreach ($rolePermissions as $roleName => $grantedPermissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($grantedPermissions);
        }

        $bootstrapEmail = env('RBAC_BOOTSTRAP_EMAIL');
        $bootstrapUser = null;

        if ($bootstrapEmail) {
            $bootstrapUser = User::query()->where('email', $bootstrapEmail)->first();
        }

        if (!$bootstrapUser) {
            $bootstrapUser = User::query()->oldest('id')->first();
        }

        if ($bootstrapUser) {
            $bootstrapUser->syncRoles(['SuperAdmin']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
