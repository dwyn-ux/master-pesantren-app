<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\KasBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankReconController extends Controller
{
    public function index(Request $request): View
    {
        $kasBankId = $request->integer('kas_bank_id');
        $tanggal = $request->input('tanggal', now()->endOfMonth()->toDateString());

        $kasBanks = KasBank::aktif()->where('tipe', 'bank')->orderBy('nama')->get();

        $kasBank = null;
        $saldoBuku = 0;

        if ($kasBankId) {
            $kasBank = KasBank::with('account')->find($kasBankId);
            if ($kasBank) {
                $masuk  = (float) DB::table('finance_transaksi')->where('kas_bank_id', $kasBank->id)->where('tipe', 'masuk')->where('status', 'posted')->where('tanggal', '<=', $tanggal)->sum('nominal');
                $keluar = (float) DB::table('finance_transaksi')->where('kas_bank_id', $kasBank->id)->where('tipe', 'keluar')->where('status', 'posted')->where('tanggal', '<=', $tanggal)->sum('nominal');
                $trIn   = (float) DB::table('finance_transaksi')->where('kas_bank_tujuan_id', $kasBank->id)->where('tipe', 'transfer')->where('status', 'posted')->where('tanggal', '<=', $tanggal)->sum('nominal');
                $trOut  = (float) DB::table('finance_transaksi')->where('kas_bank_id', $kasBank->id)->where('tipe', 'transfer')->where('status', 'posted')->where('tanggal', '<=', $tanggal)->sum('nominal');
                $saldoBuku = (float) $kasBank->saldo_awal + $masuk - $keluar + $trIn - $trOut;
            }
        }

        $history = DB::table('finance_bank_reconciliations as r')
            ->join('finance_kas_banks as kb', 'kb.id', '=', 'r.kas_bank_id')
            ->select('r.*', 'kb.nama as kas_bank_nama')
            ->orderByDesc('r.tanggal')->limit(20)->get();

        return view('finance.bank_recon.index', compact('kasBanks', 'kasBank', 'tanggal', 'saldoBuku', 'history'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kas_bank_id'           => 'required|exists:finance_kas_banks,id',
            'tanggal'               => 'required|date',
            'saldo_buku'            => 'required|numeric',
            'saldo_rekening_koran'  => 'required|numeric',
            'catatan'               => 'nullable|string',
        ]);
        $data['selisih'] = $data['saldo_buku'] - $data['saldo_rekening_koran'];
        $data['created_by'] = auth()->id();

        DB::table('finance_bank_reconciliations')->insert(array_merge($data, [
            'created_at' => now(), 'updated_at' => now()
        ]));

        return back()->with('success', 'Rekonsiliasi disimpan. Selisih: Rp ' . number_format($data['selisih']));
    }
}
