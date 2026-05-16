<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\FeatureManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeatureController extends Controller
{
    public function __construct(protected FeatureManager $features)
    {
    }

    public function index()
    {
        return view('superadmin.features.index', [
            'grouped'  => $this->features->grouped(),
            'packages' => $this->features->packages(),
        ]);
    }

    public function update(Request $request)
    {
        $registry = $this->features->registry();
        $enabled  = $request->input('features', []);

        foreach ($registry as $key => $meta) {
            if (!empty($meta['core'])) {
                continue;
            }
            $this->features->set($key, isset($enabled[$key]));
        }

        Cache::forget('features.all');

        return back()->with('success', 'Status fitur berhasil diperbarui.');
    }

    public function toggle(Request $request, string $feature)
    {
        $registry = $this->features->registry();

        if (!isset($registry[$feature])) {
            return back()->with('error', 'Fitur tidak dikenal.');
        }

        if (!empty($registry[$feature]['core'])) {
            return back()->with('error', 'Fitur core tidak dapat dinonaktifkan.');
        }

        $current = $this->features->enabled($feature);
        $this->features->set($feature, !$current);

        Cache::forget('features.all');

        $label  = $registry[$feature]['label'] ?? $feature;
        $status = !$current ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Fitur \"{$label}\" berhasil {$status}.");
    }

    public function applyPackage(Request $request, string $package)
    {
        $packages = $this->features->packages();

        if (!isset($packages[$package])) {
            return back()->with('error', 'Paket tidak dikenal.');
        }

        $this->features->applyPackage($package);

        return back()->with('success', "Paket \"{$packages[$package]['name']}\" berhasil diterapkan.");
    }
}
