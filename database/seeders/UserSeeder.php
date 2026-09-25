<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Account used as the author of the seeded events and news.
     */
    public const AUTHOR_EMAIL = 'gekrafs.belandanl@gmail.com';

    public function run(): void
    {
        $this->admin('Arsus', 'info@arsus.nl', env('SEED_ADMIN_ARSUS_PASSWORD'));
        $this->admin('GEKRAFS Belanda', self::AUTHOR_EMAIL, env('SEED_ADMIN_GEKRAFS_PASSWORD'));

        // Demo accounts for local development only.
        if (app()->environment('local', 'testing')) {
            $this->admin('Admin', 'admin@example.com', 'password');

            User::firstOrCreate(
                ['email' => 'user@example.com'],
                ['name' => 'Test User', 'password' => 'password'],
            );
        }
    }

    /**
     * Create the admin if missing. Existing accounts keep their password and role.
     *
     * Passwords never live in the code: they come from .env, or a random one is
     * generated and shown once in the console.
     */
    private function admin(string $name, string $email, ?string $password): void
    {
        if (User::where('email', $email)->exists()) {
            return;
        }

        if (blank($password)) {
            $password = Str::password(16);
            $this->command?->warn("Created {$email} with password: {$password}  (change it after logging in)");
        }

        User::create(['name' => $name, 'email' => $email, 'password' => $password])
            ->forceFill(['is_admin' => true, 'email_verified_at' => now()])
            ->save();
    }
}
