<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()['cache']->forget('spatie.permission.cache');

        $permissions = [
            'view-tickets',
            'create-tickets',
            'edit-tickets',
            'delete-tickets',
            'view-all-tickets',
            'forward-to-technician',
            'verify-completion',
            'reopen-tickets',
            'rate-tickets',
            'assign-technicians',
            'schedule-tickets',
            'view-reports',
            'input-diagnosis',
            'submit-daily-reports',
            'mark-ticket-finished',
            'manage-users',
            'manage-categories',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $cs = Role::firstOrCreate(['name' => 'cs', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $cs->syncPermissions([
            'view-tickets',
            'create-tickets',
            'edit-tickets',
            'forward-to-technician',
            'verify-completion',
            'reopen-tickets',
            'rate-tickets',
        ]);

        $manager->syncPermissions([
            'view-tickets',
            'view-all-tickets',
            'edit-tickets',
            'assign-technicians',
            'schedule-tickets',
            'view-reports',
        ]);

        $teknisi->syncPermissions([
            'view-tickets',
            'input-diagnosis',
            'submit-daily-reports',
            'mark-ticket-finished',
        ]);

        $admin->syncPermissions(Permission::all());
    }
}
