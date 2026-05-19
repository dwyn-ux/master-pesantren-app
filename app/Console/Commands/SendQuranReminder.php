<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendQuranReminder extends Command
{
    protected $signature = 'quran:reminder {waktu : subuh atau maghrib}';
    protected $description = 'Kirim notifikasi pengingat baca Al-Quran ke semua user aktif';

    public function __construct(protected NotificationService $notifier)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $waktu = $this->argument('waktu');

        [$title, $body] = match ($waktu) {
            'subuh'   => [
                '🌅 Waktunya Tilawah Subuh',
                'Awali hari dengan membaca Al-Quran. Semoga hari ini penuh berkah.',
            ],
            'maghrib' => [
                '🌙 Waktunya Tilawah Maghrib',
                'Tutup sore ini dengan membaca Al-Quran. Raih ketenangan hati.',
            ],
            default   => [
                '📖 Pengingat Tilawah',
                'Jangan lupa membaca Al-Quran hari ini.',
            ],
        };

        // Kirim ke semua user aktif yang punya role wali, ustadz, admin
        $users = User::where('is_active', true)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['wali', 'ustadz', 'admin', 'kepala_pondok']))
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            $this->notifier->send(
                user: $user,
                type: 'quran_reminder',
                title: $title,
                body: $body,
                data: ['type' => 'quran_reminder', 'waktu' => $waktu],
                actionUrl: route('quran.index'),
            );
            $sent++;
        }

        $this->info("Quran reminder ({$waktu}) terkirim ke {$sent} user.");
        return self::SUCCESS;
    }
}
