@extends('layouts.app')

@section('title', 'Kenaikan Kelas')
@section('page-title', 'Kenaikan Kelas')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Proses Kenaikan Kelas</h2>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Asal</label>
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }} - {{ ucfirst($ta->semester) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('tahun_ajaran_id'))
            <form action="{{ route('admin.akademik.kenaikan.process') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Santri</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kelas Saat Ini</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Keputusan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $santri = \App\Models\Santri::whereHas('kelasSantri', function($q) {
                                    $q->where('tahun_ajaran_id', request('tahun_ajaran_id'));
                                })->with(['kelasSantri.kelas', 'wali'])->get();
                            @endphp
                            @foreach($santri as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $s->nama }}<br>
                                    <span class="text-xs text-gray-500">{{ $s->wali->first()?->nama ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $s->kelasSantri->firstWhere('tahun_ajaran_id', request('tahun_ajaran_id'))?->kelas->nama ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select name="keputusan[{{ $s->id }}]" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                        <option value="naik" {{ old('keputusan.' . $s->id, 'naik') == 'naik' ? 'selected' : '' }}>Naik</option>
                                        <option value="tidak_naik" {{ old('keputusan.' . $s->id) == 'tidak_naik' ? 'selected' : '' }}>Tidak Naik</option>
                                        <option value="naik_bersyarat" {{ old('keputusan.' . $s->id) == 'naik_bersyarat' ? 'selected' : '' }}>Naik Bersyarat</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="catatan[{{ $s->id }}]" placeholder="Catatan (opsional)" 
                                           value="{{ old('catatan.' . $s->id) }}" class="w-full px-3 py-1 border border-gray-300 rounded text-sm">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                        <i class="fa-solid fa-save mr-2"></i>Simpan Keputusan
                    </button>
                </div>
            </form>

            @if($kenaikan->isNotEmpty())
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Eksekusi Kenaikan</h3>
                <p class="text-sm text-gray-600 mb-4">Setelah menyimpan keputusan, jalankan eksekusi untuk memindahkan santri ke kelas baru.</p>
                
                <form action="{{ route('admin.akademik.kenaikan.execute') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Asal</label>
                            <select name="tahun_ajaran_asal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="{{ request('tahun_ajaran_id') }}" selected>{{ request('tahun_ajaran_id') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Tujuan</label>
                            <select name="tahun_ajaran_tujuan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Pilih Tahun Ajaran Baru</option>
                                @foreach($tahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ $ta->id > request('tahun_ajaran_id') ? 'selected' : '' }}>
                                    {{ $ta->nama }} - {{ ucfirst($ta->semester) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            <i class="fa-solid fa-check-circle mr-2"></i>Eksekusi Kenaikan Kelas
                        </button>
                    </div>
                </form>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
