<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PrayerController extends Controller
{
    private $baseUrl = "https://api.aladhan.com/v1";

    public function index(Request $request)
    {
        $city = $request->get("city", "Jakarta");
        $country = $request->get("country", "Indonesia");
        $latitude = $request->get("lat");
        $longitude = $request->get("lng");

        $prayerTimes =
            $latitude && $longitude
                ? $this->getPrayerTimesByCoordinates($latitude, $longitude)
                : $this->getPrayerTimes($city, $country);

        $locationLabel =
            $latitude && $longitude ? "Lokasi Anda" : $city . ", " . $country;

        return view(
            "prayer.index",
            compact(
                "prayerTimes",
                "city",
                "country",
                "latitude",
                "longitude",
                "locationLabel",
            ),
        );
    }

    public function qibla()
    {
        $latitude = request("lat", -6.2088); // Jakarta default
        $longitude = request("lng", 106.8456);

        $qiblaDirection = $this->getQiblaDirection($latitude, $longitude);

        return view(
            "prayer.qibla",
            compact("qiblaDirection", "latitude", "longitude"),
        );
    }

    public function calendar(Request $request)
    {
        $city = $request->get("city", "Jakarta");
        $country = $request->get("country", "Indonesia");
        $month = $request->get("month", date("m"));
        $year = $request->get("year", date("Y"));

        $calendar = $this->getMonthlyCalendar($city, $country, $month, $year);

        return view(
            "prayer.calendar",
            compact("calendar", "city", "country", "month", "year"),
        );
    }

    private function getPrayerTimes($city, $country)
    {
        $cacheKey = "prayer_times_{$city}_{$country}_" . date("Y-m-d");

        return Cache::remember($cacheKey, 3600, function () use (
            $city,
            $country,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/timingsByCity", [
                        "city" => $city,
                        "country" => $country,
                        "method" => 2,
                    ]);

                if ($response->successful()) {
                    $data = $response->json()["data"];
                    return [
                        "date" => $data["date"],
                        "timings" => $data["timings"],
                        "meta" => $data["meta"],
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Prayer API Error: " . $e->getMessage());
            }

            return null;
        });
    }

    private function getPrayerTimesByCoordinates($latitude, $longitude)
    {
        $cacheKey =
            "prayer_times_coords_{$latitude}_{$longitude}_" . date("Y-m-d");

        return Cache::remember($cacheKey, 3600, function () use (
            $latitude,
            $longitude,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/timings", [
                        "latitude" => $latitude,
                        "longitude" => $longitude,
                        "method" => 2,
                    ]);

                if ($response->successful()) {
                    $data = $response->json()["data"];
                    return [
                        "date" => $data["date"],
                        "timings" => $data["timings"],
                        "meta" => $data["meta"],
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Prayer API Error: " . $e->getMessage());
            }

            return null;
        });
    }

    private function getQiblaDirection($latitude, $longitude)
    {
        $cacheKey = "qibla_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 86400, function () use (
            $latitude,
            $longitude,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/qibla/{$latitude}/{$longitude}");

                if ($response->successful()) {
                    return $response->json()["data"];
                }
            } catch (\Exception $e) {
                \Log::error("Prayer API Error: " . $e->getMessage());
            }

            return null;
        });
    }

    private function getMonthlyCalendar($city, $country, $month, $year)
    {
        $cacheKey = "prayer_calendar_{$city}_{$country}_{$year}_{$month}";

        return Cache::remember($cacheKey, 86400, function () use (
            $city,
            $country,
            $month,
            $year,
        ) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/calendarByCity", [
                        "city" => $city,
                        "country" => $country,
                        "month" => $month,
                        "year" => $year,
                        "method" => 2,
                    ]);

                if ($response->successful()) {
                    return $response->json()["data"];
                }
            } catch (\Exception $e) {
                \Log::error("Prayer API Error: " . $e->getMessage());
            }

            return [];
        });
    }

    // API endpoints for AJAX
    public function apiPrayerTimes(Request $request)
    {
        $city = $request->get("city", "Jakarta");
        $country = $request->get("country", "Indonesia");

        $prayerTimes = $this->getPrayerTimes($city, $country);

        return response()->json($prayerTimes);
    }

    public function apiQibla(Request $request)
    {
        $latitude = $request->get("lat", -6.2088);
        $longitude = $request->get("lng", 106.8456);

        $qiblaDirection = $this->getQiblaDirection($latitude, $longitude);

        return response()->json($qiblaDirection);
    }

    // Get next prayer time
    public function nextPrayer(Request $request)
    {
        $city = $request->get("city", "Jakarta");
        $country = $request->get("country", "Indonesia");
        $latitude = $request->get("lat");
        $longitude = $request->get("lng");

        $prayerTimes =
            $latitude && $longitude
                ? $this->getPrayerTimesByCoordinates($latitude, $longitude)
                : $this->getPrayerTimes($city, $country);

        if (!$prayerTimes) {
            return response()->json([
                "error" => "Tidak dapat mengambil waktu sholat",
            ]);
        }

        $now = Carbon::now();
        $prayers = [
            "Fajr" => $prayerTimes["timings"]["Fajr"],
            "Dhuhr" => $prayerTimes["timings"]["Dhuhr"],
            "Asr" => $prayerTimes["timings"]["Asr"],
            "Maghrib" => $prayerTimes["timings"]["Maghrib"],
            "Isha" => $prayerTimes["timings"]["Isha"],
        ];

        $nextPrayer = null;
        $timeDiff = null;

        foreach ($prayers as $name => $time) {
            $prayerTime = Carbon::createFromFormat("H:i", $time);

            if ($prayerTime->greaterThan($now)) {
                $nextPrayer = $name;
                $timeDiff = $now->diffInMinutes($prayerTime, false);
                break;
            }
        }

        // If no prayer found today, get Fajr tomorrow
        if (!$nextPrayer) {
            $nextPrayer = "Fajr";
            $fajrTime = Carbon::createFromFormat(
                "H:i",
                $prayers["Fajr"],
            )->addDay();
            $timeDiff = $now->diffInMinutes($fajrTime, false);
        }

        return response()->json([
            "next_prayer" => $nextPrayer,
            "time_remaining" => $timeDiff,
            "current_time" => $now->format("H:i"),
        ]);
    }
}
