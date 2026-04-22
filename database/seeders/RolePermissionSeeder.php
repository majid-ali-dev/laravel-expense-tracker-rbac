<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔴 SUPER ADMIN (ALL PERMISSIONS)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));

        // 🟢 MANAGER
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->permissions()->sync(
            Permission::whereIn('name', [
                'manage-users',
                'assign-roles',
                'view-all-data',
                'view-expense',
                'create-expense',
                'edit-expense',
                'delete-expense',
                'download-expense',
            ])->pluck('id')
        );

        // 🔵 STAFF
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->permissions()->sync(
            Permission::whereIn('name', [
                'create-expense',
                'edit-expense',
                'view-expense',
            ])->pluck('id')
        );

        // 🟡 MEMBER
        $member = Role::firstOrCreate(['name' => 'member']);
        $member->permissions()->sync(
            Permission::whereIn('name', [
                'view-own-data',
                'pay-bills',
            ])->pluck('id')
        );
    }
}
