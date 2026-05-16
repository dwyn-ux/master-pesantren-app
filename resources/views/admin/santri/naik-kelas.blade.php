@extends('layouts.app')
@section('title', 'Naik Kelas')
@section('page-title', 'Naik Kelas Santri')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-2xl">

    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
            <h5 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-dots text-indigo-500"></i>
                Konfirmasi Naik Kelas Massal
            </h5>
        </div>

        <div class="p-6">
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-3 mb-6">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                <div>
                    <strong>Perhatian:</strong> Aksi ini akan memperbarui kelas semua santri aktif secara serentak (7A → 8A, dst.).
                    Santri yang sudah di kelas 12 tidak akan diubah.
                </div>
            </div>

            @if($preview->isEmpty())
                <div class="py-8 text-center text-gray-500">
                    <i class="fa-solid fa-graduation-cap text-4xl text-gray-300 mb-3"></i>
                    <p>Tidak ada santri aktif yang memiliki data kelas.</p>
                </div>
            @else
                <p class="text-sm text-gray-600 font-medium mb-4">Perubahan yang akan terjadi:</p>

                <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-500">
                                <th class="px-5 py-3 text-left font-medium">Kelas Sekarang</th>
                                <th class="px-5 py-3 text-left font-medium">Kelas Baru</th>
                                <th class="px-5 py-3 text-right font-medium">Jumlah Santri</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($preview as $row)
                            <tr class="{{ $row['dari'] === $row['ke'] ? 'bg-gray-50/50 text-gray-400' : 'hover:bg-indigo-50/30' }}">
                                <td class="px-5 py-3 font-mono font-semibold">{{ $row['dari'] }}</td>
                                <td class="px-5 py-3">
                                    @if($row['dari'] === $row['ke'])
                                        <span class="text-gray-400 italic">Tidak berubah (kelas 12)</span>
                                    @else
                                        <span class="inline-flex items-center gap-2">
                                            <span class="font-mono font-bold text-indigo-700">{{ $row['ke'] }}</span>
                                            <i class="fa-solid fa-arrow-left text-indigo-400 text-xs rotate-180"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium">{{ $row['count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t border-gray-100 font-semibold text-gray-700">
                                <td class="px-5 py-3" colspan="2">Total</td>
                                <td class="px-5 py-3 text-right">{{ $preview->sum('count') }} santri</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <form method="POST" action="{{ route('admin.santri.do-naik-kelas') }}">
                    @csrf
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('admin.santri.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            onclick="return confirm('Yakin ingin menaikkan kelas semua santri aktif?')"
                            class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-arrow-up-right-dots"></i> Naikkan Kelas Sekarang
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
