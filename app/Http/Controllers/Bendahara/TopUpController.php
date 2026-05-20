<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\TopUpRequest;
use Illuminate\Http\Request;

class TopUpController extends Controller
{
    public function index(Request $request)
    {
        $requests = TopUpRequest::with(['santri', 'wali'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->whereHas('santri', fn($s) => $s
                        ->where('nama', 'ilike', "%{$request->search}%")
                        ->orWhere('nis', 'ilike', "%{$request->search}%")
                    )->orWhereHas('wali', fn($w) => $w
                        ->where('nama', 'ilike', "%{$request->search}%")
                    );
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = TopUpRequest::unpaid()->count();

        return view('bendahara.topup.index', compact('requests', 'pendingCount'));
    }
}
