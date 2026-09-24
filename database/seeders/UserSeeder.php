<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Account used as the author of the seeded events and news.
     */
    public const AUTHOR_EMAIL = 'gekrafs.belandanl@gmail.com';

    public function run(): void
    {
        $this->admin('Arsus', 'info@arsus.nl', 'arsus@29');
        $this->admin('GEKRAFS Belanda', self::AUTHOR_EMAIL, 'gekrafsnl24');

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
     * Create the admin if missing. Existing accounts keep their current password.
     */
    private function admin(string $name, string $email, string $password): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password],
        );

        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();
    }
}
