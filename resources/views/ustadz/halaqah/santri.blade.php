@extends('layouts.app')
@section('title', 'Daftar Santri Halaqah')
@section('page-title', 'Daftar Santri')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h6 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-500"></i> Semua Santri di Halaqah Saya
                </h6>
                <p class="text-sm text-gray-500 mt-1">Total: <strong>{{ $santri->total() }}</strong> santri</p>
            </div>
            <a href="{{ route('ustadz.setoran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-scroll"></i> Catat Setoran
            </a>
        </div>

        <!-- Filter -->
        <div class="p-5 border-b border-gray-100 bg-gray-50/30">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm focus:border-indigo-500 transition-all" placeholder="Cari nama atau NIS...">
                </div>
                <div class="w-full sm:w-52">
                    <select name="halaqah_id" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm appearance-none cursor-pointer">
                        <option value="">Semua Halaqah</option>
                        @foreach($halaqahList as $h)
                            <option value="{{ $h->id }}" @selected(request('halaqah_id') == $h->id)>{{ $h->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gray-800 text-white rounded-xl font-bold shadow-sm">Filter</button>
            </form>
        </div>

        @if($santri->isEmpty())
            <div class="p-16 text-center">
                <i class="fa-solid fa-user-slash text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Tidak ada santri ditemukan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                            <th class="px-6 py-4 font-medium">Santri</th>
                            <th class="px-6 py-4 font-medium">NIS</th>
                            <th class="px-6 py-4 font-medium">Halaqah</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($santri as $s)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-extrabold shrink-0">
                                            {{ strtoupper(substr($s->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $s->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-indigo-700 text-sm">{{ $s->nis }}</td>
                                <td class="px-6 py-4">
                                    @foreach($s->halaqah as $hq)
                                        <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md border border-indigo-100 mr-1">{{ $hq->nama }}</span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">
                                    @if($s->is_aktif)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($santri->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $santri->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
