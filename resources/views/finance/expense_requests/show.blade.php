@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('page-title', 'Permintaan ' . $req->nomor)

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
        <div class="flex justify-between items-start mb-4 pb-4 border-b">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Nomor</p>
                <h2 class="text-xl font-bold font-mono">{{ $req->nomor }}</h2>
            </div>
            @php $color = match($req->status) {
                'draft' => 'gray', 'pending_kepala' => 'amber', 'pending_yayasan' => 'purple',
                'approved' => 'blue', 'paid' => 'green', 'rejected' => 'red', 'void' => 'gray',
            }; @endphp
            <span class="px-3 py-1 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 text-sm font-bold">
                {{ \App\Models\Finance\ExpenseRequest::statusLabels()[$req->status] ?? $req->status }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div><p class="text-xs text-gray-500 font-bold">Tanggal</p><p class="font-bold">{{ $req->tanggal->format('d F Y') }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Pengaju</p><p class="font-bold">{{ $req->creator?->name }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Kategori</p><p class="font-bold">{{ $req->kategori?->nama }}</p></div>
            <div><p class="text-xs text-gray-500 font-bold">Kas/Bank</p><p class="font-bold">{{ $req->kasBank?->nama }}</p></div>
            @if($req->vendor)<div><p class="text-xs text-gray-500 font-bold">Vendor</p><p class="font-bold">{{ $req->vendor->nama }}</p></div>@endif
        </div>

        <div class="mb-4">
            <p class="text-xs text-gray-500 font-bold mb-1">Judul</p>
            <p class="text-lg font-bold">{{ $req->judul }}</p>
        </div>
        <div class="mb-4">
            <p class="text-xs text-gray-500 font-bold mb-1">Deskripsi</p>
            <p class="text-sm bg-gray-50 p-3 rounded-lg">{{ $req->deskripsi }}</p>
        </div>

        @if($req->bukti)
        <a href="{{ asset('storage/' . $req->bukti) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm">
            <i class="fa-solid fa-paperclip"></i> Lihat Bukti
        </a>
        @endif

        <hr class="my-4">

        <h6 class="font-bold mb-3 text-sm">Riwayat Approval</h6>
        <div class="space-y-2 text-sm">
            <div class="flex items-center gap-3 p-3 rounded-lg {{ $req->approved_kepala_at ? 'bg-green-50' : 'bg-gray-50' }}">
                <i class="fa-solid {{ $req->approved_kepala_at ? 'fa-circle-check text-green-600' : 'fa-clock text-gray-400' }}"></i>
                <div>
                    <p class="font-bold">Kepala Pondok</p>
                    <p class="text-xs">{{ $req->approved_kepala_at?->format('d/m/Y H:i') ?: 'Menunggu' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg {{ $req->approved_yayasan_at ? 'bg-green-50' : 'bg-gray-50' }}">
                <i class="fa-solid {{ $req->approved_yayasan_at ? 'fa-circle-check text-green-600' : 'fa-clock text-gray-400' }}"></i>
                <div>
                    <p class="font-bold">Yayasan</p>
                    <p class="text-xs">{{ $req->approved_yayasan_at?->format('d/m/Y H:i') ?: ((float)$req->nominal >= (float)\App\Models\Finance\Setting::get('approval.threshold_yayasan',50000000) ? 'Menunggu' : 'Tidak diperlukan') }}</p>
                </div>
            </div>
            @if($req->status == 'rejected')
            <div class="p-3 rounded-lg bg-red-50 border-l-4 border-red-500">
                <p class="font-bold text-red-700"><i class="fa-solid fa-circle-xmark"></i> DITOLAK</p>
                <p class="text-xs">{{ $req->reject_reason }}</p>
            </div>
            @endif
            @if($req->status == 'paid' && $req->transaksi)
            <div class="p-3 rounded-lg bg-green-50 border-l-4 border-green-500">
                <p class="font-bold text-green-700"><i class="fa-solid fa-circle-check"></i> SUDAH DIBAYAR</p>
                <p class="text-xs">Transaksi: <a href="{{ route('finance.transaksi.show', $req->transaksi) }}" class="font-mono text-indigo-600">{{ $req->transaksi->nomor }}</a></p>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-3">
        <div class="glass-panel rounded-2xl p-5 text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Nominal</p>
            <p class="text-3xl font-extrabold text-indigo-600">Rp {{ number_format($req->nominal) }}</p>
        </div>

        @if($req->status == 'pending_kepala' && auth()->user()->hasAnyRole(['kepala_pondok', 'admin']))
        <form method="POST" action="{{ route('finance.expense-requests.approve', [$req, 'kepala']) }}" class="glass-panel rounded-2xl p-4">
            @csrf
            <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm">
                <i class="fa-solid fa-check mr-1"></i> Approve (Kepala Pondok)
            </button>
        </form>
        @endif

        @if($req->status == 'pending_yayasan' && auth()->user()->hasAnyRole(['admin']))
        <form method="POST" action="{{ route('finance.expense-requests.approve', [$req, 'yayasan']) }}" class="glass-panel rounded-2xl p-4">
            @csrf
            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg font-bold text-sm">
                <i class="fa-solid fa-check-double mr-1"></i> Approve (Yayasan)
            </button>
        </form>
        @endif

        @if(in_array($req->status, ['pending_kepala', 'pending_yayasan']) && auth()->user()->hasAnyRole(['admin', 'kepala_pondok']))
        <form method="POST" action="{{ route('finance.expense-requests.reject', $req) }}" class="glass-panel rounded-2xl p-4" onsubmit="return confirm('Tolak permintaan?')">
            @csrf
            <input type="text" name="reason" required placeholder="Alasan penolakan" class="w-full rounded-lg border-gray-300 text-sm mb-2">
            <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-bold text-sm">
                <i class="fa-solid fa-xmark mr-1"></i> Tolak
            </button>
        </form>
        @endif

        @if($req->status == 'approved')
        <form method="POST" action="{{ route('finance.expense-requests.execute', $req) }}" class="glass-panel rounded-2xl p-4" onsubmit="return confirm('Eksekusi pengeluaran sekarang? Saldo kas/bank akan berkurang.')">
            @csrf
            <button class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-bold">
                <i class="fa-solid fa-money-bill-transfer mr-1"></i> Eksekusi & Bayar
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
