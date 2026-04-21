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
        // SUPER ADMIN
        $majid = User::firstOrCreate(
            ['email' => 'majidalishar2@gmail.com'],
            [
                'name' => 'Majid Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );
        $majid->roles()->attach(Role::where('name', 'super_admin')->first());

        // MANAGER
        $nadeem = User::firstOrCreate(
            ['email' => 'nadeem@gmail.com'],
            [
                'name' => 'Nadeem Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );
        $nadeem->roles()->attach(Role::where('name', 'manager')->first());

        // STAFF
        $shahid = User::firstOrCreate(
            ['email' => 'shahid@gmail.com'],
            [
                'name' => 'Shahid Hussain',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );
        $shahid->roles()->attach(Role::where('name', 'staff')->first());

        // MEMBER 1
        $naseer = User::firstOrCreate(
            ['email' => 'naseer@gmail.com'],
            [
                'name' => 'Naseer Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );
        $naseer->roles()->attach(Role::where('name', 'member')->first());

        // MEMBER 2
        $qamber = User::firstOrCreate(
            ['email' => 'qamber@gmail.com'],
            [
                'name' => 'Qamber Ali',
                'phone' => '096545567',
                'password' => Hash::make('12345'),
            ]
        );
        $qamber->roles()->attach(Role::where('name', 'member')->first());
    }
}
