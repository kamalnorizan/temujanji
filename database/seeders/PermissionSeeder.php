<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'web';

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'appointments.create',
            'appointments.view.own',
            'appointments.view.all',
            'appointments.update',
            'appointments.calendar.view',
            'appointments.available-time.check',
            'appointments.chatbot.access',
            'profile.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, $guardName);
        }

        $rolePermissions = [
            'admin' => $permissions,
            'pegawai' => [
                'appointments.view.all',
                'appointments.update',
                'appointments.calendar.view',
                'appointments.available-time.check',
                'appointments.chatbot.access',
                'profile.manage',
            ],
            'pengguna' => [
                'appointments.create',
                'appointments.view.own',
                'profile.manage',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::findOrCreate($roleName, $guardName);
            $role->syncPermissions($permissionNames);
        }

        User::query()
            ->whereIn('role', array_keys($rolePermissions))
            ->get()
            ->each(function (User $user): void {
                $user->syncRoles([$user->role]);
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
