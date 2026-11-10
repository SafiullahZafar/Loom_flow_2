<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateOperator extends Command
{
    protected $signature = 'loomflow:create-operator {--name=} {--email=}';
    protected $description = 'Create the factory operator securely without demo credentials';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Operator name')));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Operator email'))));
        if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A name and valid email are required.');
            return self::FAILURE;
        }
        if (User::where('email', $email)->exists()) {
            $this->error('Operator with this email already exists.');
            return self::FAILURE;
        }
        $password = (string) $this->secret('Operator password (12 characters minimum)');
        if (strlen($password) < 12) {
            $this->error('Use a password at least 12 characters long.');
            return self::FAILURE;
        }
        User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        $this->info('Operator account created.');
        return self::SUCCESS;
    }
}
