<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportSetting;
use Illuminate\Http\Request;

class ReportSettingController extends Controller
{
    public function index()
    {
        $weeklyReport = ReportSetting::get('weekly_report', [
            'enabled' => true,
            'day_of_week' => 0,
            'hour' => 8,
            'minute' => 0,
        ]);

        $monthlyReport = ReportSetting::get('monthly_report', [
            'enabled' => true,
            'day_of_month' => 1,
            'hour' => 8,
            'minute' => 0,
        ]);

        return view('admin.report-settings.index', compact('weeklyReport', 'monthlyReport'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'weekly_enabled' => 'required|boolean',
            'weekly_day' => 'required|integer|min:0|max:6',
            'weekly_hour' => 'required|integer|min:0|max:23',
            'weekly_minute' => 'required|integer|min:0|max:59',
            'monthly_enabled' => 'required|boolean',
            'monthly_day' => 'required|integer|min:1|max:28',
            'monthly_hour' => 'required|integer|min:0|max:23',
            'monthly_minute' => 'required|integer|min:0|max:59',
        ]);

        ReportSetting::set('weekly_report', [
            'enabled' => (bool) $validated['weekly_enabled'],
            'day_of_week' => (int) $validated['weekly_day'],
            'hour' => (int) $validated['weekly_hour'],
            'minute' => (int) $validated['weekly_minute'],
        ]);

        ReportSetting::set('monthly_report', [
            'enabled' => (bool) $validated['monthly_enabled'],
            'day_of_month' => (int) $validated['monthly_day'],
            'hour' => (int) $validated['monthly_hour'],
            'minute' => (int) $validated['monthly_minute'],
        ]);

        return back()->with('success', 'Pengaturan laporan otomatis berhasil disimpan.');
    }
}
