<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('finance.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->input('settings', []);
        foreach ($payload as $key => $value) {
            $row = Setting::where('key', $key)->first();
            if (!$row) continue;

            if ($row->type === 'boolean') {
                $row->value = $value ? '1' : '0';
            } else {
                $row->value = (string) $value;
            }
            $row->save();
            Cache::forget("finance_setting:{$key}");
        }
        return back()->with('success', 'Pengaturan disimpan.');
    }
}
