<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    private $quranBaseUrl = 'https://equran.id/api/v2';
    private $prayerBaseUrl = 'https://api.aladhan.com/v1';

    /**
     * Show public landing page
     */
    public function landingPage()
    {
        // Get featured surah list
        $surahList = Cache::remember('public_quran_surah_list', 3600, function () {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->quranBaseUrl}/surat");
                if ($response->successful()) {
                    $data = $response->json()['data'];
                    // Return hanya 12 surah pertama untuk preview
                    return array_slice($data, 0, 12);
                }
            } catch (\Exception $e) {
                \Log::error('Quran API Error: ' . $e->getMessage());
            }
            return $this->getDefaultSurahList();
        });

        // Get default prayer times (Jakarta)
        $prayerTimes = $this->getPrayerTimes('Jakarta', 'Indonesia');

        return view('public.landing', compact('surahList', 'prayerTimes'));
    }

    /**
     * Show public quran list
     */
    public function quranIndex()
    {
        $surahList = Cache::remember('public_quran_all_surah_list', 3600, function () {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->quranBaseUrl}/surat");
                return $response->successful() ? $response->json()['data'] : $this->getDefaultSurahList();
            } catch (\Exception $e) {
                \Log::error('Quran API Error: ' . $e->getMessage());
                return $this->getDefaultSurahList();
            }
        });

        return view('public.quran.index', compact('surahList'));
    }

    /**
     * Show public surah detail
     */
    public function quranSurah($nomor)
    {
        $surah = Cache::remember("public_quran_surah_{$nomor}", 3600, function () use ($nomor) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->quranBaseUrl}/surat/{$nomor}");
                return $response->successful() ? $response->json()['data'] : null;
            } catch (\Exception $e) {
                \Log::error('Quran API Error: ' . $e->getMessage());
                return null;
            }
        });

        if (!$surah) {
            abort(404, 'Surah tidak ditemukan');
        }

        return view('public.quran.surah', compact('surah'));
    }

    /**
     * Show prayer times (public)
     */
    public function prayerIndex(Request $request)
    {
        $city = $request->get('city', 'Jakarta');
        $country = $request->get('country', 'Indonesia');

        $prayerTimes = $this->getPrayerTimes($city, $country);

        return view('public.prayer.index', compact('prayerTimes', 'city', 'country'));
    }

    /**
     * Get prayer times
     */
    private function getPrayerTimes($city, $country)
    {
        $cacheKey = "public_prayer_times_{$city}_{$country}_" . date('Y-m-d');

        return Cache::remember($cacheKey, 3600, function () use ($city, $country) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->prayerBaseUrl}/timingsByCity", [
                        'city' => $city,
                        'country' => $country,
                        'method' => 2,
                    ]);

                if ($response->successful()) {
                    $data = $response->json()['data'];
                    return [
                        'date' => $data['date'],
                        'timings' => $data['timings'],
                        'meta' => $data['meta'],
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Prayer API Error: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * API: Get next prayer time
     */
    public function apiNextPrayer(Request $request)
    {
        $city = $request->get('city', 'Jakarta');
        $country = $request->get('country', 'Indonesia');

        $prayerTimes = $this->getPrayerTimes($city, $country);

        if (!$prayerTimes) {
            return response()->json(['error' => 'Tidak dapat mengambil waktu sholat']);
        }

        $now = now();
        $prayers = [
            'Fajr' => $prayerTimes['timings']['Fajr'],
            'Dhuhr' => $prayerTimes['timings']['Dhuhr'],
            'Asr' => $prayerTimes['timings']['Asr'],
            'Maghrib' => $prayerTimes['timings']['Maghrib'],
            'Isha' => $prayerTimes['timings']['Isha'],
        ];

        $nextPrayer = null;
        $timeDiff = null;

        foreach ($prayers as $name => $time) {
            $prayerTime = now()->setTimeFromTimeString($time);

            if ($prayerTime->greaterThan($now)) {
                $nextPrayer = $name;
                $timeDiff = $now->diffInMinutes($prayerTime, false);
                break;
            }
        }

        // If no prayer found today, get Fajr tomorrow
        if (!$nextPrayer) {
            $nextPrayer = 'Fajr';
            $fajrTime = now()->setTimeFromTimeString($prayers['Fajr'])->addDay();
            $timeDiff = $now->diffInMinutes($fajrTime, false);
        }

        return response()->json([
            'next_prayer' => $nextPrayer,
            'time_remaining' => $timeDiff,
            'current_time' => $now->format('H:i'),
        ]);
    }

    /**
     * Get default surah list (fallback)
     */
    private function getDefaultSurahList()
    {
        return [
            ['nomor' => 1, 'namaLatin' => 'Al-Fatihah', 'nama' => 'الفاتحة', 'jumlahAyat' => 7, 'arti' => 'Pembukaan'],
            ['nomor' => 2, 'namaLatin' => 'Al-Baqarah', 'nama' => 'البقرة', 'jumlahAyat' => 286, 'arti' => 'Sapi Betina'],
            ['nomor' => 3, 'namaLatin' => 'Ali Imran', 'nama' => 'آل عمران', 'jumlahAyat' => 200, 'arti' => 'Keluarga Imran'],
            ['nomor' => 4, 'namaLatin' => 'An-Nisa', 'nama' => 'النساء', 'jumlahAyat' => 176, 'arti' => 'Wanita'],
            ['nomor' => 5, 'namaLatin' => 'Al-Maidah', 'nama' => 'المائدة', 'jumlahAyat' => 120, 'arti' => 'Hidangan'],
            ['nomor' => 6, 'namaLatin' => 'Al-An\'am', 'nama' => 'الأنعام', 'jumlahAyat' => 165, 'arti' => 'Ternak'],
            ['nomor' => 7, 'namaLatin' => 'Al-A\'raf', 'nama' => 'الأعراف', 'jumlahAyat' => 206, 'arti' => 'Tempat Tertinggi'],
            ['nomor' => 8, 'namaLatin' => 'Al-Anfal', 'nama' => 'الأنفال', 'jumlahAyat' => 75, 'arti' => 'Harta Rampasan'],
            ['nomor' => 9, 'namaLatin' => 'At-Taubah', 'nama' => 'التوبة', 'jumlahAyat' => 129, 'arti' => 'Pertaubatan'],
            ['nomor' => 10, 'namaLatin' => 'Yunus', 'nama' => 'يونس', 'jumlahAyat' => 109, 'arti' => 'Yunus'],
            ['nomor' => 11, 'namaLatin' => 'Hud', 'nama' => 'هود', 'jumlahAyat' => 123, 'arti' => 'Hud'],
            ['nomor' => 12, 'namaLatin' => 'Yusuf', 'nama' => 'يوسف', 'jumlahAyat' => 111, 'arti' => 'Yusuf'],
        ];
    }
}
