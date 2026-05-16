<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['username' => 'admin',        'name' => 'Administrator',  'role' => 'admin'],
            ['username' => 'bendahara',     'name' => 'Bendahara',       'role' => 'bendahara'],
            ['username' => 'kepalapondok',  'name' => 'Kepala Pondok',   'role' => 'kepala_pondok'],
        ];

        $lines = ["=== Kredensial Staff (generated: " . now() . ") ===\n"];

        foreach ($accounts as $item) {
            $password = match($item['username']) {
                'admin' => 'Admin@1234',  // default — wajib diganti setelah login pertama
                default => \Illuminate\Support\Str::random(10),
            };

            $user = User::firstOrCreate(
                ['username' => $item['username']],
                [
                    'name'           => $item['name'],
                    'password'       => Hash::make($password),
                    'must_change_pw' => true,
                    'is_active'      => true,
                ]
            );

            $user->assignRole($item['role']);

            $line = "[{$item['role']}] username: {$item['username']} | password: {$password}";
            $lines[] = $line;
            $this->command->info($line);
        }

        Storage::disk('local')->put(
            'credentials-staff.txt',
            implode("\n", $lines) . "\n"
        );

        $this->command->warn('Kredensial disimpan di: storage/app/private/credentials-staff.txt');
    }
}
