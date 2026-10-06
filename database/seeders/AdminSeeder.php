<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['username' => 'admin',        'name' => 'Administrator',  'role' => 'admin'],
            ['username' => 'bendahara',     'name' => 'Bendahara',       'role' => 'bendahara'],
            ['username' => 'kepalapondok',  'name' => 'Kepala Pondok',   'role' => 'kepala_pondok'],
        ];

        $lines = [];

        foreach ($accounts as $item) {
            $user = User::where('username', $item['username'])->first();

            if (! $user) {
                $password = Str::random(16);
                $user = User::create([
                    'username'       => $item['username'],
                    'name'           => $item['name'],
                    'password'       => Hash::make($password),
                    'must_change_pw' => true,
                    'is_active'      => true,
                ]);

                $lines[] = "[{$item['role']}] username: {$item['username']} | password: {$password}";
            }

            $user->assignRole($item['role']);

        }

        if ($lines) {
            Storage::disk('local')->put(
                'credentials-staff.txt',
                "=== Kredensial Staff (generated: " . now() . ") ===\n" . implode("\n", $lines) . "\n"
            );

            $this->command->warn('Kredensial staff baru disimpan di: storage/app/private/credentials-staff.txt');

            return;
        }

        $this->command->info('Akun staff sudah ada; password dan file kredensial tidak diubah.');
    }
}
