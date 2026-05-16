<?php

namespace App\Http\Middleware;

use App\Services\FeatureManager;
use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    public function __construct(protected FeatureManager $features)
    {
    }

    public function handle(Request $request, Closure $next, string $feature)
    {
        if ($this->features->disabled($feature)) {
            $label = config("features.features.{$feature}.label", $feature);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Fitur \"{$label}\" sedang dinonaktifkan.",
                ], 403);
            }

            abort(403, "Fitur \"{$label}\" sedang dinonaktifkan oleh administrator.");
        }

        return $next($request);
    }
}
