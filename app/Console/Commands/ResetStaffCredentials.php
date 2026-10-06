<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResetStaffCredentials extends Command
{
    protected $signature = 'staff:reset-credentials {--activate : Aktifkan kembali akun staff yang direset}';

    protected $description = 'Reset kredensial admin, bendahara, dan kepala pondok secara aman';

    public function handle(): int
    {
        $accounts = [
            ['username' => 'admin', 'role' => 'admin'],
            ['username' => 'bendahara', 'role' => 'bendahara'],
            ['username' => 'kepalapondok', 'role' => 'kepala_pondok'],
        ];

        $lines = ["=== Kredensial Staff (reset: " . now() . ") ===\n"];
        $reset = 0;

        foreach ($accounts as $account) {
            $user = User::where('username', $account['username'])->first();

            if (! $user) {
                $this->warn("Akun {$account['username']} tidak ditemukan; dilewati.");

                continue;
            }

            $password = Str::random(16);
            $data = [
                'password' => Hash::make($password),
                'must_change_pw' => true,
            ];

            if ($this->option('activate')) {
                $data['is_active'] = true;
            }

            $user->update($data);
            $user->assignRole($account['role']);
            $lines[] = "[{$account['role']}] username: {$account['username']} | password: {$password}";
            $reset++;
        }

        if ($reset === 0) {
            $this->error('Tidak ada akun staff yang berhasil direset.');

            return self::FAILURE;
        }

        Storage::disk('local')->put('credentials-staff.txt', implode("\n", $lines) . "\n");
        $this->info("{$reset} akun staff sudah direset. Kredensial tersimpan di storage/app/private/credentials-staff.txt.");

        return self::SUCCESS;
    }
}
