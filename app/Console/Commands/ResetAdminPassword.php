<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset {email}';
    protected $description = 'Reset an admin account password';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user || ! $user->isAdmin()) {
            $this->error('No admin account found with that email.');
            return self::FAILURE;
        }

        $password = $this->secret('New password');
        $confirm  = $this->secret('Confirm password');

        if ($password !== $confirm || strlen($password) < 12) {
            $this->error('Passwords must match and be at least 12 characters.');
            return self::FAILURE;
        }

        $user->forceFill(['password' => Hash::make($password)])->save();
        $this->info('Admin password updated.');

        return self::SUCCESS;
    }
}