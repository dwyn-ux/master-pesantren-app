<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = [
            [
                'username'    => 'kantin',
                'nama_user'   => 'Kasir Kantin',
                'nama_outlet' => 'Kantin Pesantren',
                'tipe'        => 'kantin',
            ],
            [
                'username'    => 'laundry',
                'nama_user'   => 'Kasir Laundry',
                'nama_outlet' => 'Laundry Pesantren',
                'tipe'        => 'laundry',
            ],
        ];

        $lines = ["=== Kredensial Outlet (generated: " . now() . ") ===\n"];

        foreach ($outlets as $item) {
            $password = Str::random(8);

            $user = User::firstOrCreate(
                ['username' => $item['username']],
                [
                    'name'           => $item['nama_user'],
                    'password'       => Hash::make($password),
                    'must_change_pw' => false,
                    'is_active'      => true,
                ]
            );

            $user->assignRole('outlet');

            DB::table('outlets')->insertOrIgnore([
                'user_id'  => $user->id,
                'nama'     => $item['nama_outlet'],
                'tipe'     => $item['tipe'],
                'is_aktif' => true,
            ]);

            $line = "[{$item['tipe']}] username: {$item['username']} | password: {$password}";
            $lines[] = $line;
            $this->command->info($line);
        }

        Storage::disk('local')->put(
            'credentials-outlet.txt',
            implode("\n", $lines) . "\n"
        );

        $this->command->warn('Kredensial disimpan di: storage/app/private/credentials-outlet.txt');
    }
}
