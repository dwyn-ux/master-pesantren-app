<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class QuranController extends Controller
{
    private $baseUrl = "https://equran.id/api/v2";

    public function index()
    {
        // Get surah list
        $surahList = Cache::remember("quran_surah_list", 3600, function () {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/surat");
                return $response->successful() ? $response->json()["data"] : [];
            } catch (\Exception $e) {
                \Log::error("Quran API Error: " . $e->getMessage());
                return [];
            }
        });

        return view("quran.index", compact("surahList"));
    }

    public function surah($nomor)
    {
        // Get surah detail with ayat
        $surah = Cache::remember("quran_surah_{$nomor}", 3600, function () use (
            $nomor,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/surat/{$nomor}");
                return $response->successful()
                    ? $response->json()["data"]
                    : null;
            } catch (\Exception $e) {
                \Log::error("Quran API Error: " . $e->getMessage());
                return null;
            }
        });

        if (!$surah) {
            abort(404, "Surah tidak ditemukan");
        }

        return view("quran.surah", compact("surah"));
    }

    public function ayat($surahNomor, $ayatNomor)
    {
        // Get specific ayat
        $ayat = Cache::remember(
            "quran_ayat_{$surahNomor}_{$ayatNomor}",
            3600,
            function () use ($surahNomor, $ayatNomor) {
                try {
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->get("{$this->baseUrl}/surat/{$surahNomor}");
                    if ($response->successful()) {
                        $data = $response->json()["data"];
                        $ayat = collect($data["ayat"])->first(
                            fn($item) => (int) ($item["nomorAyat"] ??
                                ($item["nomor"] ?? 0)) === (int) $ayatNomor,
                        );
                        if ($ayat) {
                            $ayat["nomor"] =
                                $ayat["nomorAyat"] ??
                                ($ayat["nomor"] ?? $ayatNomor);
                            $ayat["ar"] =
                                $ayat["teksArab"] ?? ($ayat["ar"] ?? "");
                            $ayat["tr"] =
                                $ayat["teksLatin"] ?? ($ayat["tr"] ?? "");
                            $ayat["idn"] =
                                $ayat["teksIndonesia"] ?? ($ayat["idn"] ?? "");
                            $ayat["surah"] = [
                                "nomor" => $data["nomor"],
                                "nama" => $data["nama"],
                                "namaLatin" => $data["namaLatin"],
                                "jumlahAyat" => $data["jumlahAyat"],
                            ];
                            return $ayat;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Quran API Error: " . $e->getMessage());
                }
                return null;
            },
        );

        if (!$ayat) {
            abort(404, "Ayat tidak ditemukan");
        }

        return view("quran.ayat", compact("ayat"));
    }

    public function search(Request $request)
    {
        $query = $request->get("q");

        if (!$query) {
            return redirect()->route("quran.index");
        }

        // Search functionality (basic implementation)
        $results = Cache::remember(
            "quran_search_{$query}",
            1800,
            function () use ($query) {
                $results = [];

                try {
                    // Get all surah and search in ayat
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->get("{$this->baseUrl}/surat");
                    if ($response->successful()) {
                        $surahList = $response->json()["data"];

                        foreach ($surahList as $surah) {
                            try {
                                $surahDetail = Http::timeout(10)
                                    ->withoutVerifying()
                                    ->get(
                                        $this->baseUrl .
                                            "/surat/" .
                                            $surah["nomor"],
                                    )
                                    ->json()["data"];

                                foreach ($surahDetail["ayat"] as $ayat) {
                                    $teksArab =
                                        $ayat["teksArab"] ??
                                        ($ayat["ar"] ?? "");
                                    $teksLatin =
                                        $ayat["teksLatin"] ??
                                        ($ayat["tr"] ?? "");
                                    $teksIndonesia =
                                        $ayat["teksIndonesia"] ??
                                        ($ayat["idn"] ?? "");

                                    if (
                                        stripos($teksArab, $query) !== false ||
                                        stripos($teksLatin, $query) !== false ||
                                        stripos($teksIndonesia, $query) !==
                                            false
                                    ) {
                                        $ayat["nomor"] =
                                            $ayat["nomorAyat"] ??
                                            ($ayat["nomor"] ?? null);
                                        $ayat["ar"] = $teksArab;
                                        $ayat["tr"] = $teksLatin;
                                        $ayat["idn"] = $teksIndonesia;

                                        $results[] = [
                                            "surah" => $surahDetail,
                                            "ayat" => $ayat,
                                            "match_type" => "text",
                                        ];

                                        // Limit results
                                        if (count($results) >= 50) {
                                            break 2;
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error(
                                    "Quran API Error: " . $e->getMessage(),
                                );
                                continue;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Quran API Error: " . $e->getMessage());
                }

                return $results;
            },
        );

        return view("quran.search", compact("query", "results"));
    }

    public function tafsir($surahNomor, $ayatNomor)
    {
        // Get tafsir for specific ayat
        $tafsir = Cache::remember(
            "quran_tafsir_{$surahNomor}_{$ayatNomor}",
            3600,
            function () use ($surahNomor, $ayatNomor) {
                try {
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->get("{$this->baseUrl}/tafsir/{$surahNomor}");
                    if ($response->successful()) {
                        $data = $response->json()["data"];
                        $tafsir = collect($data["tafsir"])->firstWhere(
                            "ayat",
                            $ayatNomor,
                        );
                        if ($tafsir) {
                            $tafsir["surah"] = [
                                "nomor" => $data["nomor"],
                                "nama" => $data["nama"],
                                "namaLatin" => $data["namaLatin"],
                            ];
                            return $tafsir;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Quran API Error: " . $e->getMessage());
                }
                return null;
            },
        );

        if (!$tafsir) {
            abort(404, "Tafsir tidak ditemukan");
        }

        return view("quran.tafsir", compact("tafsir"));
    }

    // API endpoints for AJAX requests
    public function apiSurahList()
    {
        $surahList = Cache::remember("quran_surah_list", 3600, function () {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/surat");
                return $response->successful() ? $response->json()["data"] : [];
            } catch (\Exception $e) {
                \Log::error("Quran API Error: " . $e->getMessage());
                return [];
            }
        });

        return response()->json($surahList);
    }

    public function apiSurah($nomor)
    {
        $surah = Cache::remember("quran_surah_{$nomor}", 3600, function () use (
            $nomor,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/surat/{$nomor}");
                return $response->successful()
                    ? $response->json()["data"]
                    : null;
            } catch (\Exception $e) {
                \Log::error("Quran API Error: " . $e->getMessage());
                return null;
            }
        });

        return response()->json($surah);
    }
}
