<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = config('app.admin_email');
        $password = config('app.admin_password');

        if (!$email || !$password) {
            $this->command->error('Set ADMIN_EMAIL and ADMIN_PASSWORD in your .env first.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Admin',
                'password' => Hash::make($password),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin account is ready: ' . $email);
    }
}