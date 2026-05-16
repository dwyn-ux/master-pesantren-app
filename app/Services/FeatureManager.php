<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class FeatureManager
{
    protected const KEY_PREFIX = 'feature.';
    protected const CACHE_KEY  = 'features.all';
    protected const CACHE_TTL  = 3600;

    /**
     * Cek apakah fitur aktif. Fitur "core" selalu return true.
     */
    public function enabled(string $feature): bool
    {
        $registry = $this->registry();
        $meta = $registry[$feature] ?? null;

        if ($meta === null) {
            return false;
        }

        if (!empty($meta['core'])) {
            return true;
        }

        return (bool) AppSetting::get(self::KEY_PREFIX . $feature, $meta['default'] ?? false);
    }

    public function disabled(string $feature): bool
    {
        return !$this->enabled($feature);
    }

    /**
     * Aktifkan / nonaktifkan fitur tunggal.
     */
    public function set(string $feature, bool $enabled): void
    {
        $registry = $this->registry();
        $meta = $registry[$feature] ?? null;

        if ($meta === null) {
            return;
        }

        AppSetting::set(self::KEY_PREFIX . $feature, $enabled ? '1' : '0', 'boolean', [
            'group'       => 'feature',
            'label'       => $meta['label']       ?? $feature,
            'description' => $meta['description'] ?? null,
        ]);

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Apply paket preset (set semua fitur sesuai paket).
     */
    public function applyPackage(string $packageKey): bool
    {
        $packages = config('features.packages', []);
        if (!isset($packages[$packageKey])) {
            return false;
        }

        $enabled = array_flip($packages[$packageKey]['features']);

        foreach (array_keys($this->registry()) as $feature) {
            $meta = $this->registry()[$feature];
            if (!empty($meta['core'])) {
                continue;
            }
            $this->set($feature, isset($enabled[$feature]));
        }

        return true;
    }

    /**
     * Map fitur => status aktif (untuk view).
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $out = [];
            foreach ($this->registry() as $key => $meta) {
                $out[$key] = [
                    'meta'    => $meta,
                    'enabled' => $this->enabled($key),
                ];
            }
            return $out;
        });
    }

    public function grouped(): array
    {
        $grouped = [];
        foreach ($this->all() as $key => $row) {
            $group = $row['meta']['group'] ?? 'Lainnya';
            $grouped[$group][$key] = $row;
        }
        return $grouped;
    }

    public function registry(): array
    {
        return config('features.features', []);
    }

    public function packages(): array
    {
        return config('features.packages', []);
    }
}
