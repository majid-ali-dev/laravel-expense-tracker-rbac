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
        $superAdminRoleId = Role::where('name', 'super_admin')->value('id');
        $managerRoleId = Role::where('name', 'manager')->value('id');
        $staffRoleId = Role::where('name', 'staff')->value('id');
        $memberRoleId = Role::where('name', 'member')->value('id');

        $majid = User::firstOrCreate(
            ['email' => 'majidalishar2@gmail.com'],
            [
                'name' => 'Majid Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );

        if ($superAdminRoleId) {
            $majid->roles()->syncWithoutDetaching([$superAdminRoleId]);
        }

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

        $shahid = User::firstOrCreate(
            ['email' => 'shahid@gmail.com'],
            [
                'name' => 'Shahid Hussain',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );

        if ($staffRoleId) {
            $shahid->roles()->syncWithoutDetaching([$staffRoleId]);
        }

        $naseer = User::firstOrCreate(
            ['email' => 'naseer@gmail.com'],
            [
                'name' => 'Naseer Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );

        if ($memberRoleId) {
            $naseer->roles()->syncWithoutDetaching([$memberRoleId]);
        }

        $qamber = User::firstOrCreate(
            ['email' => 'qamber@gmail.com'],
            [
                'name' => 'Qamber Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );

        if ($memberRoleId) {
            $qamber->roles()->syncWithoutDetaching([$memberRoleId]);
        }
    }
}
