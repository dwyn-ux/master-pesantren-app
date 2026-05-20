<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest('created_at');

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('action')) $query->where('action', 'ilike', '%' . $request->action . '%');
        if ($request->filled('subject_type')) $query->where('subject_type', $request->subject_type);
        if ($request->filled('dari')) $query->whereDate('created_at', '>=', $request->dari);
        if ($request->filled('sampai')) $query->whereDate('created_at', '<=', $request->sampai);

        $items = $query->paginate(50)->withQueryString();

        return view('finance.audit.index', compact('items'));
    }
}
