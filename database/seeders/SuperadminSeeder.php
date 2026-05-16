<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $username = 'superadmin';
        $password = 'Super@1234'; // wajib diganti setelah login pertama

        $user = User::firstOrCreate(
            ['username' => $username],
            [
                'name'           => 'Super Administrator',
                'password'       => Hash::make($password),
                'must_change_pw' => true,
                'is_active'      => true,
            ]
        );

        $user->syncRoles(['superadmin', 'admin']);

        $line = "[superadmin] username: {$username} | password: {$password}";
        $this->command->info($line);

        Storage::disk('local')->put(
            'credentials-superadmin.txt',
            "=== Kredensial Superadmin (generated: " . now() . ") ===\n{$line}\n"
        );

        $this->command->warn('Kredensial superadmin disimpan di: storage/app/private/credentials-superadmin.txt');
    }
}
