<?php

namespace App\Http\Middleware;

use App\Models\Finance\Setting;
use Closure;
use Illuminate\Http\Request;

class CheckFinanceFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        if (!Setting::isEnabled($feature)) {
            return back()->with('error', "Fitur \"{$feature}\" sedang dinonaktifkan. Aktifkan dulu di Pengaturan Finance.");
        }
        return $next($request);
    }
}
