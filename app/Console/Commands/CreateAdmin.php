<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Create or reset the admin user account for Sazara Global CMS';

    public function handle(): int
    {
        $this->info('== Sazara Global — Create Admin ==');

        $name     = $this->ask('Admin name', 'Sazara Admin');
        $email    = $this->ask('Admin email', 'admin@sazaraglobal.com');
        $password = $this->secret('Admin password (min 8 chars)');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make($password),
            ]
        );

        $action = $user->wasRecentlyCreated ? 'created' : 'updated';
        $this->info("Admin account {$action}: {$email}");
        $this->info('Login at: /login-sazara');

        return self::SUCCESS;
    }
}
