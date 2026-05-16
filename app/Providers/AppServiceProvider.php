<?php

namespace App\Providers;

use App\Models\LaundryOrder;
use App\Models\Pembayaran;
use App\Models\TopUpRequest;
use App\Models\TransaksiKasir;
use App\Observers\LaundryOrderObserver;
use App\Observers\PembayaranObserver;
use App\Observers\TopUpRequestObserver;
use App\Observers\TransaksiKasirObserver;
use App\Services\FeatureManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FeatureManager::class, fn () => new FeatureManager());
    }

    public function boot(): void
    {
        TransaksiKasir::observe(TransaksiKasirObserver::class);
        LaundryOrder::observe(LaundryOrderObserver::class);
        Pembayaran::observe(PembayaranObserver::class);
        TopUpRequest::observe(TopUpRequestObserver::class);

        // ── Blade directive: @feature('quran') ... @endfeature ─────────────
        Blade::if('feature', function (string $feature) {
            return app(FeatureManager::class)->enabled($feature);
        });

        // ── @featureany('quran','prayer') — true bila salah satu aktif ─────
        Blade::if('featureany', function (...$features) {
            $manager = app(FeatureManager::class);
            foreach ($features as $f) {
                if ($manager->enabled($f)) return true;
            }
            return false;
        });
    }
}
