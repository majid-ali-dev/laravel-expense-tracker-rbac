<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $managerRoleId = Role::where('name', 'manager')->value('id');

        $nadeem = User::firstOrCreate(
            ['email' => 'nadeem@gmail.com'],
            [
                'name' => 'Nadeem Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );

        if ($managerRoleId) {
            $nadeem->roles()->syncWithoutDetaching([$managerRoleId]);
        }
    }
}
