<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'kitchen' => 'kitchen@example.com',
            'bar' => 'bar@example.com',
            'roti' => 'roti@example.com',
            'manager' => 'manager@example.com',
        ];

        foreach ($roles as $role => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($role),
                    'email' => $email,
                    'password' => Hash::make('password'), // default password
                    'role' => $role,
                ]
            );
        }
    }
}
