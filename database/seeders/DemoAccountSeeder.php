<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) config('demo.name'));
        $username = trim((string) config('demo.username'));
        $password = (string) config('demo.password');

        if ($name === '' || $username === '') {
            throw new InvalidArgumentException(
                'DEMO_ACCOUNT_NAME dan DEMO_ACCOUNT_USERNAME wajib diisi.'
            );
        }

        if (strlen($password) < 12) {
            throw new InvalidArgumentException(
                'DEMO_ACCOUNT_PASSWORD wajib diisi di .env dengan minimal 12 karakter.'
            );
        }

        $this->call(RoleSeeder::class);

        $user = User::updateOrCreate(
            ['username' => $username],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'must_change_pw' => false,
                'is_active' => true,
            ]
        );

        $user->syncRoles(['admin']);

        $this->command?->info("Akun demo siap — username: {$username}");
        $this->command?->warn('Password dibaca dari DEMO_ACCOUNT_PASSWORD dan tidak disimpan di source code.');
    }
}
