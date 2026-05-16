<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\KasBank;
use App\Models\Finance\PayrollRun;
use App\Models\Finance\PayrollSlip;
use App\Models\Finance\UstadzSalary;
use App\Models\Ustadz;
use App\Services\Finance\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(protected PayrollService $service) {}

    public function index(): View
    {
        $items = PayrollRun::withCount('slips')->latest('tahun')->latest('bulan')->paginate(25);
        return view('finance.payroll.index', compact('items'));
    }

    public function masterSalary(Request $request): View
    {
        $query = Ustadz::with('latestSalary')->orderBy('nama');
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $items = $query->paginate(50)->withQueryString();
        return view('finance.payroll.master_salary', compact('items'));
    }

    public function editSalary(Ustadz $ustadz): View
    {
        $salary = UstadzSalary::firstOrNew(['ustadz_id' => $ustadz->id, 'is_active' => true]);
        return view('finance.payroll.salary_form', compact('ustadz', 'salary'));
    }

    public function updateSalary(Request $request, Ustadz $ustadz): RedirectResponse
    {
        $data = $request->validate([
            'gaji_pokok'          => 'required|numeric|min:0',
            'tunjangan_jabatan'   => 'required|numeric|min:0',
            'tunjangan_transport' => 'required|numeric|min:0',
            'tunjangan_makan'     => 'required|numeric|min:0',
            'tunjangan_lain'      => 'required|numeric|min:0',
            'potongan_tetap'      => 'required|numeric|min:0',
            'berlaku_sejak'       => 'required|date',
        ]);

        UstadzSalary::where('ustadz_id', $ustadz->id)->update(['is_active' => false]);
        UstadzSalary::create(array_merge($data, [
            'ustadz_id' => $ustadz->id,
            'is_active' => true,
        ]));

        return redirect()->route('finance.payroll.master-salary')->with('success', 'Master gaji ' . $ustadz->nama . ' diperbarui.');
    }

    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        try {
            $run = $this->service->generate($data['tahun'], $data['bulan']);
            return redirect()->route('finance.payroll.show', $run)->with('success', 'Payroll generated: ' . $run->nomor);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(PayrollRun $payroll): View
    {
        $payroll->load(['slips.ustadz', 'journal.lines.account']);
        $kasBanks = KasBank::aktif()->where('tipe', '!=', '')->get();
        return view('finance.payroll.show', ['run' => $payroll, 'kasBanks' => $kasBanks]);
    }

    public function approve(PayrollRun $payroll): RedirectResponse
    {
        try {
            $this->service->approve($payroll);
            return back()->with('success', 'Payroll di-approve.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pay(Request $request, PayrollRun $payroll): RedirectResponse
    {
        $data = $request->validate([
            'kas_bank_id'   => 'required|exists:finance_kas_banks,id',
            'tanggal_bayar' => 'required|date',
        ]);

        try {
            $this->service->pay($payroll, $data['kas_bank_id'], $data['tanggal_bayar']);
            return back()->with('success', 'Payroll dibayar & jurnal otomatis dibuat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateSlip(Request $request, PayrollSlip $slip): RedirectResponse
    {
        if ($slip->payrollRun->status !== 'draft') {
            return back()->with('error', 'Slip hanya bisa diedit saat payroll status draft.');
        }

        $data = $request->validate([
            'bonus'             => 'required|numeric|min:0',
            'potongan_absensi'  => 'required|numeric|min:0',
            'potongan_pph21'    => 'required|numeric|min:0',
            'potongan_lain'     => 'required|numeric|min:0',
            'hari_hadir'        => 'nullable|integer|min:0',
            'hari_kerja'        => 'nullable|integer|min:0',
            'catatan'           => 'nullable|string',
        ]);

        $gross = (float) $slip->gaji_pokok + (float) $slip->tunjangan_jabatan
               + (float) $slip->tunjangan_transport + (float) $slip->tunjangan_makan
               + (float) $slip->tunjangan_lain + (float) $data['bonus'];

        $totalPotongan = (float) $data['potongan_absensi'] + (float) $data['potongan_pph21'] + (float) $data['potongan_lain'];

        $slip->update(array_merge($data, [
            'gross' => $gross,
            'net'   => $gross - $totalPotongan,
        ]));

        // Recalc payroll run total
        $run = $slip->payrollRun;
        $run->update([
            'total_gross'    => (float) $run->slips()->sum('gross'),
            'total_potongan' => (float) ($run->slips()->sum('potongan_absensi') + $run->slips()->sum('potongan_pph21') + $run->slips()->sum('potongan_lain')),
            'total_net'      => (float) $run->slips()->sum('net'),
        ]);

        return back()->with('success', 'Slip diperbarui.');
    }

    public function slipPdf(PayrollSlip $slip): View
    {
        $slip->load(['ustadz', 'payrollRun']);
        return view('finance.payroll.slip_print', compact('slip'));
    }
}
