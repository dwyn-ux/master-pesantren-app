@extends('layouts.app')
@section('title', 'Detail Payroll')
@section('page-title', 'Detail Payroll ' . $run->nomor)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Periode</p>
        <p class="text-lg font-bold">{{ \Carbon\Carbon::create($run->tahun, $run->bulan)->translatedFormat('F Y') }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Gross</p>
        <p class="text-lg font-bold text-green-600">Rp {{ number_format($run->total_gross) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-red-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Potongan</p>
        <p class="text-lg font-bold text-red-600">Rp {{ number_format($run->total_potongan) }}</p>
    </div>
    <div class="glass-panel p-4 rounded-2xl border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Net</p>
        <p class="text-lg font-bold text-purple-600">Rp {{ number_format($run->total_net) }}</p>
    </div>
</div>

<div class="glass-panel rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
        <div>
            <h6 class="text-lg font-bold">Status:
                @if($run->status == 'draft')<span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs">DRAFT</span>
                @elseif($run->status == 'approved')<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">APPROVED</span>
                @elseif($run->status == 'paid')<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">PAID</span>
                @else<span class="px-2 py-1 rounded-full bg-gray-100 text-gray-500 text-xs">VOID</span>@endif
            </h6>
            @if($run->journal)<p class="text-xs text-gray-500 mt-1">Jurnal: <a href="{{ route('finance.transaksi.index') }}" class="text-indigo-600 font-mono">{{ $run->journal->nomor }}</a></p>@endif
        </div>
        <div class="flex gap-2">
            @if($run->status == 'draft')
            <form method="POST" action="{{ route('finance.payroll.approve', $run) }}" onsubmit="return confirm('Approve payroll ini?')">
                @csrf
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold"><i class="fa-solid fa-check mr-1"></i> Approve</button>
            </form>
            @endif
            @if(in_array($run->status, ['draft', 'approved']))
            <button onclick="document.getElementById('payModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">
                <i class="fa-solid fa-money-bill-wave mr-1"></i> Bayar Gaji
            </button>
            @endif
        </div>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Ustadz</th>
                <th class="px-3 py-2 text-right">Pokok</th>
                <th class="px-3 py-2 text-right">Tunjangan</th>
                <th class="px-3 py-2 text-right">Bonus</th>
                <th class="px-3 py-2 text-right">Potongan</th>
                <th class="px-3 py-2 text-right">Gross</th>
                <th class="px-3 py-2 text-right">Net</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($run->slips as $slip)
            <tr>
                <td class="px-3 py-2 font-semibold">{{ $slip->ustadz->nama }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($slip->gaji_pokok) }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($slip->tunjangan_jabatan + $slip->tunjangan_transport + $slip->tunjangan_makan + $slip->tunjangan_lain) }}</td>
                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($slip->bonus) }}</td>
                <td class="px-3 py-2 text-right text-red-600">- Rp {{ number_format($slip->potongan_absensi + $slip->potongan_pph21 + $slip->potongan_lain) }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($slip->gross) }}</td>
                <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($slip->net) }}</td>
                <td class="px-3 py-2 text-right">
                    @if($run->status == 'draft')
                    <button onclick="document.getElementById('slip-{{ $slip->id }}').classList.toggle('hidden')" class="text-indigo-600 text-xs"><i class="fa-solid fa-pen"></i> Edit</button>
                    @endif
                    <a href="{{ route('finance.payroll.slip.print', $slip) }}" target="_blank" class="text-gray-600 text-xs ml-2"><i class="fa-solid fa-print"></i></a>
                </td>
            </tr>
            @if($run->status == 'draft')
            <tr id="slip-{{ $slip->id }}" class="hidden bg-yellow-50">
                <td colspan="8" class="px-3 py-3">
                    <form method="POST" action="{{ route('finance.payroll.slip.update', $slip) }}" class="grid grid-cols-2 md:grid-cols-6 gap-2 text-xs">
                        @csrf @method('PUT')
                        <div><label class="block text-xs font-semibold">Bonus</label><input type="number" step="0.01" min="0" name="bonus" value="{{ $slip->bonus }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div><label class="block text-xs font-semibold">Pot. Absen</label><input type="number" step="0.01" min="0" name="potongan_absensi" value="{{ $slip->potongan_absensi }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div><label class="block text-xs font-semibold">PPh 21</label><input type="number" step="0.01" min="0" name="potongan_pph21" value="{{ $slip->potongan_pph21 }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div><label class="block text-xs font-semibold">Pot. Lain</label><input type="number" step="0.01" min="0" name="potongan_lain" value="{{ $slip->potongan_lain }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div><label class="block text-xs font-semibold">Hadir</label><input type="number" min="0" name="hari_hadir" value="{{ $slip->hari_hadir }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div><label class="block text-xs font-semibold">H.Kerja</label><input type="number" min="0" name="hari_kerja" value="{{ $slip->hari_kerja }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div class="col-span-2 md:col-span-6"><label class="block text-xs font-semibold">Catatan</label><input type="text" name="catatan" value="{{ $slip->catatan }}" class="w-full rounded border-gray-300 text-xs"></div>
                        <div class="col-span-2 md:col-span-6"><button class="px-3 py-1 bg-indigo-600 text-white rounded text-xs">Update Slip</button></div>
                    </form>
                </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="8" class="px-3 py-6 text-center text-gray-400">Belum ada slip. Pastikan ustadz sudah punya master gaji.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="payModal" class="hidden fixed inset-0 z-40 bg-black/40 flex items-center justify-center">
    <form method="POST" action="{{ route('finance.payroll.pay', $run) }}" class="bg-white rounded-2xl p-6 max-w-md w-full m-4">
        @csrf
        <h3 class="text-lg font-bold mb-4">Bayar Gaji</h3>
        <p class="text-sm text-gray-600 mb-4">Total yang akan dibayar: <span class="font-bold text-purple-600">Rp {{ number_format($run->total_net) }}</span></p>
        <div class="mb-3">
            <label class="block text-sm font-semibold mb-1">Bayar dari Kas/Bank *</label>
            <select name="kas_bank_id" required class="w-full rounded-lg border-gray-300">
                <option value="">— Pilih —</option>
                @foreach($kasBanks as $kb)
                    <option value="{{ $kb->id }}">{{ $kb->nama }} (Saldo: Rp {{ number_format($kb->saldo_berjalan) }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold mb-1">Tanggal Bayar *</label>
            <input type="date" name="tanggal_bayar" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('payModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold">Konfirmasi Bayar</button>
        </div>
    </form>
</div>
@endsection
