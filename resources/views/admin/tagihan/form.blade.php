@extends('layouts.app')
@section('title', $tagihan ? 'Edit Tagihan' : 'Buat Tagihan')
@section('page-title', $tagihan ? 'Edit Tagihan' : 'Buat Tagihan')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden max-w-5xl">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
        <h6 class="text-lg font-bold text-gray-800">{{ $tagihan ? 'Edit Tagihan Santri' : 'Buat Tagihan Baru' }}</h6>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ $tagihan ? route('admin.tagihan.update', $tagihan) : route('admin.tagihan.store') }}">
            @csrf
            @if($tagihan) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jenis Tagihan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Tagihan <span class="text-red-500">*</span></label>
                    <select name="jenis_tagihan_id" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('jenis_tagihan_id') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all bg-white appearance-none" id="jenis-tagihan-select">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($jenis as $item)
                            <option value="{{ $item->id }}" data-is-fixed="{{ $item->is_nominal_tetap ? '1' : '0' }}" data-value="{{ $item->nominal }}"
                                @selected(old('jenis_tagihan_id', $tagihan->jenis_tagihan_id ?? '') == $item->id)>
                                {{ $item->nama }} - {{ $item->is_nominal_tetap ? 'Rp '.number_format($item->nominal) : 'Bebas isi' }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_tagihan_id')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Nominal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Tagihan (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-medium">Rp</span>
                        </div>
                        <input type="number" name="nominal" value="{{ old('nominal', $tagihan->nominal ?? '') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('nominal') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all disabled:bg-gray-100 disabled:text-gray-500"
                               id="nominal-field" placeholder="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="nominal-help">Nominal akan terisi otomatis jika jenis tagihan bersifat tetap.</p>
                    @error('nominal')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Periode --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-calendar-days text-gray-400"></i>
                        </div>
                        <input type="text" name="periode" value="{{ old('periode', $tagihan->periode ?? '') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('periode') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all"
                               placeholder="cth: 2026-04">
                    </div>
                    @error('periode')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Due Date --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($tagihan?->due_date)->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('due_date') ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500' : 'border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500' }} outline-none transition-all">
                    @error('due_date')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>

                @if($tagihan)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                        <option value="belum_bayar" @selected(old('status', $tagihan->status) === 'belum_bayar')>Belum Bayar</option>
                        <option value="sebagian" @selected(old('status', $tagihan->status) === 'sebagian')>Sebagian</option>
                        <option value="lunas" @selected(old('status', $tagihan->status) === 'lunas')>Lunas</option>
                    </select>
                </div>
                @endif
            </div>

            {{-- Santri Selection --}}
            @if(!$tagihan)
            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Pilih Santri <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2 text-sm">
                        <button type="button" onclick="selectAll()" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-medium transition-colors text-xs">Pilih Semua</button>
                        <button type="button" onclick="deselectAll()" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium transition-colors text-xs">Batal Semua</button>
                        <span id="selected-count" class="text-gray-500 ml-1 text-xs">0 dipilih</span>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3 mb-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-filter text-gray-400 text-sm"></i>
                        <span class="text-sm text-gray-600 font-medium">Filter:</span>
                    </div>
                    <input type="text" id="filter-nama" placeholder="Cari nama..." onkeyup="filterSantri()"
                           class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-indigo-400 w-40">
                    <select id="filter-kelas" onchange="filterSantri()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">Semua Kelas</option>
                        @foreach($santri->pluck('kelas')->filter()->unique()->sort() as $k)
                            <option value="{{ $k }}">Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                    <select id="filter-jk" onchange="filterSantri()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">L & P</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <button type="button" onclick="resetFilters()" class="px-3 py-1.5 rounded-lg bg-gray-200 text-gray-600 hover:bg-gray-300 text-xs font-medium transition-colors">Reset</button>
                </div>

                @error('santri_ids')<div class="text-red-500 text-sm mb-3">{{ $message }}</div>@enderror

                <div id="santri-list" class="border border-gray-200 rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                    @foreach($santri as $item)
                    <label class="santri-item flex items-center gap-3 px-4 py-3 hover:bg-indigo-50/50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors"
                           data-nama="{{ strtolower($item->nama) }}"
                           data-kelas="{{ $item->kelas }}"
                           data-jk="{{ $item->jenis_kelamin }}">
                        <input type="checkbox" name="santri_ids[]" value="{{ $item->id }}"
                               class="santri-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               {{ in_array($item->id, old('santri_ids', [])) ? 'checked' : '' }}>
                        <div class="flex-1 min-w-0">
                            <span class="block font-medium text-gray-800 truncate">{{ $item->nama }}</span>
                            <span class="block text-xs text-gray-500 font-mono">{{ $item->nis }}
                                @if($item->kelas) &bull; Kelas {{ $item->kelas }}@endif
                                @if($item->jenis_kelamin) &bull; {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}@endif
                            </span>
                        </div>
                    </label>
                    @endforeach
                    <div id="no-results" class="hidden py-8 text-center text-gray-500 text-sm">Tidak ada santri yang sesuai filter.</div>
                </div>
            </div>
            @else
            {{-- Edit mode: single santri (read-only display) --}}
            <input type="hidden" name="santri_ids[]" value="{{ $tagihan->santri_id }}">
            <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                    {{ substr($tagihan->santri->nama, 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $tagihan->santri->nama }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $tagihan->santri->nis }}</p>
                </div>
            </div>
            @endif

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100 justify-end">
                <a href="{{ route('admin.tagihan.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-medium transition-colors text-center">Batal</a>
                <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> {{ $tagihan ? 'Simpan Perubahan' : 'Simpan Tagihan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const jenisSelect = document.getElementById('jenis-tagihan-select');
    const nominalField = document.getElementById('nominal-field');

    if (jenisSelect && nominalField) {
        updateNominalField();
        jenisSelect.addEventListener('change', updateNominalField);

        function updateNominalField() {
            const option = jenisSelect.selectedOptions[0];
            if (!option || !option.value) {
                nominalField.readOnly = false;
                return;
            }
            const isFixed = option.dataset.isFixed === '1';
            if (isFixed) {
                nominalField.readOnly = true;
                nominalField.classList.add('bg-gray-100', 'text-gray-600');
                nominalField.value = option.dataset.value;
            } else {
                nominalField.readOnly = false;
                nominalField.classList.remove('bg-gray-100', 'text-gray-600');
                if (!@json((bool)$tagihan)) {
                    nominalField.value = '';
                }
            }
        }
    }

    // Multi-select santri
    function updateCount() {
        const checked = document.querySelectorAll('.santri-checkbox:checked').length;
        const el = document.getElementById('selected-count');
        if (el) el.textContent = checked + ' dipilih';
    }

    function selectAll() {
        document.querySelectorAll('.santri-item:not(.hidden) .santri-checkbox').forEach(cb => cb.checked = true);
        updateCount();
    }

    function deselectAll() {
        document.querySelectorAll('.santri-checkbox').forEach(cb => cb.checked = false);
        updateCount();
    }

    function filterSantri() {
        const nama  = document.getElementById('filter-nama')?.value.toLowerCase() ?? '';
        const kelas = document.getElementById('filter-kelas')?.value ?? '';
        const jk    = document.getElementById('filter-jk')?.value ?? '';
        const items = document.querySelectorAll('.santri-item');
        let visible = 0;

        items.forEach(item => {
            const matchNama  = !nama  || item.dataset.nama.includes(nama);
            const matchKelas = !kelas || item.dataset.kelas === kelas;
            const matchJk    = !jk    || item.dataset.jk === jk;
            const show = matchNama && matchKelas && matchJk;
            item.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        const noResults = document.getElementById('no-results');
        if (noResults) noResults.classList.toggle('hidden', visible > 0);
    }

    function resetFilters() {
        document.getElementById('filter-nama').value = '';
        document.getElementById('filter-kelas').value = '';
        document.getElementById('filter-jk').value = '';
        filterSantri();
    }

    document.querySelectorAll('.santri-checkbox').forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();
</script>
@endsection
