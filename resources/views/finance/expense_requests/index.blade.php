@extends('layouts.app')
@section('title', 'Permintaan Pengeluaran')
@section('page-title', 'Permintaan Pengeluaran (Expense Request)')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-amber-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Menunggu Kepala Pondok</p>
        <p class="text-2xl font-bold text-amber-600">{{ $summary['pending_kepala'] }}</p>
    </div>
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Menunggu Yayasan</p>
        <p class="text-2xl font-bold text-purple-600">{{ $summary['pending_yayasan'] }}</p>
    </div>
    <div class="glass-panel rounded-2xl p-4 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase font-bold">Approved (Siap Dieksekusi)</p>
        <p class="text-2xl font-bold text-green-600">{{ $summary['approved'] }}</p>
    </div>
</div>

<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    <form method="GET" class="flex gap-2">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Finance\ExpenseRequest::statusLabels() as $k => $v)
                <option value="{{ $k }}" @selected(request('status')==$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
    </form>
    <a href="{{ route('finance.expense-requests.create') }}" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow">
        <i class="fa-solid fa-plus mr-1"></i> Permintaan Baru
    </a>
</div>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Nomor</th>
                <th class="px-3 py-2 text-left">Judul</th>
                <th class="px-3 py-2 text-left">Pengaju</th>
                <th class="px-3 py-2 text-right">Nominal</th>
                <th class="px-3 py-2 text-center">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $r)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-3 py-2 text-xs">{{ $r->tanggal->format('d/m/Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $r->nomor }}</td>
                <td class="px-3 py-2 font-semibold">{{ $r->judul }}</td>
                <td class="px-3 py-2 text-xs">{{ $r->creator?->name }}</td>
                <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($r->nominal) }}</td>
                <td class="px-3 py-2 text-center">
                    @php $color = match($r->status) {
                        'draft' => 'gray', 'pending_kepala' => 'amber', 'pending_yayasan' => 'purple',
                        'approved' => 'blue', 'paid' => 'green', 'rejected' => 'red', 'void' => 'gray',
                    }; @endphp
                    <span class="px-2 py-1 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 text-xs font-semibold">
                        {{ \App\Models\Finance\ExpenseRequest::statusLabels()[$r->status] ?? $r->status }}
                    </span>
                </td>
                <td class="px-3 py-2 text-right">
                    <a href="{{ route('finance.expense-requests.show', $r) }}" class="text-indigo-600"><i class="fa-solid fa-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-3 py-8 text-center text-gray-400">Belum ada permintaan pengeluaran.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
