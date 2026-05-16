<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FeatureManager;

class DashboardController extends Controller
{
    public function index(FeatureManager $features)
    {
        $registry = $features->registry();
        $totalFitur     = count($registry);
        $totalAktif     = 0;
        $totalNonaktif  = 0;

        foreach ($registry as $key => $meta) {
            if (!empty($meta['core']) || $features->enabled($key)) {
                $totalAktif++;
            } else {
                $totalNonaktif++;
            }
        }

        $stats = [
            'total_fitur'    => $totalFitur,
            'fitur_aktif'    => $totalAktif,
            'fitur_nonaktif' => $totalNonaktif,
            'total_user'     => User::count(),
        ];

        return view('superadmin.dashboard', compact('stats'));
    }
}
