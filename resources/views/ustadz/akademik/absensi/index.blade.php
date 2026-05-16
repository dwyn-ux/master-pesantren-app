@extends('layouts.app')

@section('title', 'Absensi & Jurnal')
@section('page-title', 'Absensi & Jurnal Mengajar')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Absensi Santri</h2>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Pelajaran</label>
                <select name="jadwal_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Jadwal</option>
                    @foreach($jadwal as $j)
                    <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->hari }} - {{ $j->jam_mulai }} - {{ $j->mataPelajaran->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" onchange="this.form.submit()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
        </form>

        @if(request('jadwal_id'))
            <form action="{{ route('ustadz.akademik.absensi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
                <input type="hidden" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $taActive = \App\Models\Akademik\TahunAjaran::active();
                                $santri = $taActive
                                    ? \App\Models\Santri::whereHas('kelasSantri', function($q) use ($taActive) {
                                        $q->where('tahun_ajaran_id', $taActive->id);
                                    })->with('kelasSantri.kelas')->get()
                                    : collect();
                            @endphp
                            @foreach($santri as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $s->nama }}<br>
                                    <span class="text-xs text-gray-500">{{ $s->kelasSantri->first()?->kelas->nama ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select name="status[{{ $s->id }}]" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                        <option value="hadir" {{ old('status.' . $s->id, 'hadir') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="sakit" {{ old('status.' . $s->id) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="izin" {{ old('status.' . $s->id) == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="alpa" {{ old('status.' . $s->id) == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                        <i class="fa-solid fa-save mr-2"></i>Simpan Absensi
                    </button>
                </div>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Jurnal Mengajar</h2>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Pelajaran</label>
                <select name="jadwal_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Jadwal</option>
                    @foreach($jadwal as $j)
                    <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->hari }} - {{ $j->jam_mulai }} - {{ $j->mataPelajaran->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" onchange="this.form.submit()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
        </form>

        @if(request('jadwal_id'))
            <form action="{{ route('ustadz.akademik.absensi.store-jurnal') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
                <input type="hidden" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Materi yang Dibahas</label>
                        <textarea name="materi" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('materi', $jurnal?->materi ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Halaman Kitab</label>
                            <input type="text" name="halaman_kitab" placeholder="Hal 45-50" value="{{ old('halaman_kitab', $jurnal?->halaman_kitab ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tugas</label>
                            <input type="text" name="pr" placeholder="Baca halaman 51-55" value="{{ old('pr', $jurnal?->pr ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('catatan', $jurnal?->catatan ?? '') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                        <i class="fa-solid fa-save mr-2"></i>Simpan Jurnal
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
