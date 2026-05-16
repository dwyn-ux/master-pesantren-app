<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = config('features.features', []);

        foreach ($features as $key => $meta) {
            if (!empty($meta['core'])) {
                continue;
            }

            AppSetting::firstOrCreate(
                ['key' => "feature.{$key}"],
                [
                    'value'       => !empty($meta['default']) ? '1' : '0',
                    'type'        => 'boolean',
                    'group'       => 'feature',
                    'label'       => $meta['label']       ?? $key,
                    'description' => $meta['description'] ?? null,
                ]
            );
        }

        $this->command->info('Default fitur aplikasi berhasil di-seed.');
    }
}
