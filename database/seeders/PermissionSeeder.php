<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'assign-roles']);
        Permission::firstOrCreate(['name' => 'view-all-data']);

        Permission::firstOrCreate(['name' => 'create-expense']);
        Permission::firstOrCreate(['name' => 'edit-expense']);
        Permission::firstOrCreate(['name' => 'delete-expense']);

        Permission::firstOrCreate(['name' => 'view-own-data']);
        Permission::firstOrCreate(['name' => 'pay-bills']);
    }
}
