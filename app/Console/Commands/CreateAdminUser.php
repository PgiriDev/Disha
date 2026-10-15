<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateAdminUser extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:create-admin
                            {email? : The administrator email address}
                            {--name= : The administrator full name}
                            {--password= : The administrator password (will be prompted if not provided)}';

    /**
     * @var string
     */
    protected $description = 'Create or update an administrative user account without hardcoding production credentials.';

    public function handle(): int
    {
        $email = $this->argument('email');

        if ($email === null || $email === '') {
            $email = $this->ask('Administrator email address');
        }

        if ($email === null) {
            $this->error('An email address is required to create an administrator.');

            return self::FAILURE;
        }

        $email = strtolower(trim((string) $email));

        try {
            validator(['email' => $email], [
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            ])->validate();
        } catch (ValidationException $exception) {
            foreach ($exception->errors()['email'] ?? [] as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $name = $this->option('name');

        if ($name === null || $name === '') {
            $name = $this->ask('Administrator full name', 'Administrator');
        }

        $password = $this->option('password');

        if ($password === null || $password === '') {
            $password = $this->secret('Administrator password');
        }

        if ($password === null || $password === '') {
            $this->error('A password is required to create an administrator.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = trim((string) $name) !== '' ? trim((string) $name) : Str::title(Str::before($email, '@'));
        $user->password = Hash::make($password);
        $user->role = User::ROLE_ADMIN;
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->save();

        if ($user->wasRecentlyCreated) {
            $this->info("Administrator account created successfully: {$user->email}");
        } else {
            $this->info("Administrator account updated successfully: {$user->email}");
        }

        return self::SUCCESS;
    }
}
