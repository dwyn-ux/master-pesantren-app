@extends('layouts.app')
@section('title', 'Import Data')
@section('page-title', 'Import Data')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
        <span class="font-medium text-sm">Dashboard</span>
    </a>
    @include('partials.sidebar-admin')
@endsection

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 shadow-sm">
    <i class="fa-solid fa-circle-check text-green-500 mt-0.5 text-lg"></i>
    <p class="text-sm font-medium">{{ session('success') }}</p>
</div>
@endif
@if(session('warning'))
<div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-4 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 text-lg"></i>
    <p class="text-sm font-medium">{{ session('warning') }}</p>
</div>
@endif
@if(session('error'))
<div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 shadow-sm">
    <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5 text-lg"></i>
    <p class="text-sm font-medium">{{ session('error') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Kolom kiri: Form upload & download template --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
                <h6 class="text-lg font-bold text-gray-800">Upload Excel</h6>
                <p class="text-xs text-gray-500 mt-0.5">Format file: .xlsx atau .xls</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data" id="upload-form">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Import</label>
                        <select name="tipe" id="import-type"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none cursor-pointer">
                            <option value="wali" @selected(old('tipe') === 'wali')>Data Wali Santri</option>
                            <option value="santri" @selected(old('tipe') === 'santri')>Data Santri Baru</option>
                        </select>
                        @error('tipe')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx / .xls)</label>
                        <label for="file-upload"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer bg-gray-50 hover:bg-indigo-50 hover:border-indigo-300 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 group-hover:text-indigo-400 transition-colors mb-2"></i>
                                <p class="text-sm text-gray-500" id="file-label">Klik untuk pilih file</p>
                                <p class="text-xs text-gray-400 mt-1">.xlsx atau .xls</p>
                            </div>
                            <input type="file" name="file" id="file-upload" accept=".xlsx,.xls" class="hidden" required>
                        </label>
                        @error('file')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" id="submit-btn"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="submit-text">Mulai Import</span>
                    </button>
                </form>

                <div class="relative flex items-center py-5">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Atau</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                {{-- Download template --}}
                <form method="POST" action="{{ route('admin.import.download-template') }}" id="template-form">
                    @csrf
                    <input type="hidden" name="tipe" id="template-tipe" value="{{ old('tipe', 'wali') }}">
                    <button type="submit"
                        class="w-full py-2.5 bg-white border-2 border-emerald-100 hover:border-emerald-300 hover:bg-emerald-50 text-emerald-700 rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        Download Template XLSX
                    </button>
                </form>

                <p class="text-xs text-center text-gray-400 mt-3">Template menyesuaikan tipe import yang dipilih</p>
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Contoh format & riwayat --}}
    <div class="lg:col-span-8 space-y-6">
        {{-- Panduan format --}}
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
                <h6 class="text-lg font-bold text-gray-800">Panduan Format File</h6>
            </div>
            <div class="p-6">
                {{-- Wali --}}
                <p class="text-xs font-bold text-indigo-600 mb-3 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-users"></i> Format Import WALI
                </p>
                <div class="overflow-x-auto rounded-xl border border-gray-200 mb-6">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-indigo-50 border-b border-gray-200 text-indigo-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Nama Orang Tua</th>
                                <th class="px-4 py-3 font-semibold">No HP</th>
                                <th class="px-4 py-3 font-semibold">Hubungan</th>
                                <th class="px-4 py-3 font-semibold">Nama Santri</th>
                                <th class="px-4 py-3 font-semibold">NIS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="bg-white">
                                <td class="px-4 py-3">Bapak Ahmad Wijaya</td>
                                <td class="px-4 py-3 font-mono text-xs">085712345678</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs">ayah</span>
                                </td>
                                <td class="px-4 py-3">Ahmad Wulansari</td>
                                <td class="px-4 py-3 font-mono text-xs">PST.2026.001</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-5 text-xs text-blue-700">
                    <strong>Catatan:</strong> Kolom <code>Hubungan</code> diisi: <code>ayah</code>, <code>ibu</code>, atau <code>wali</code>.
                    Jika NIS santri belum ada, akan dibuat otomatis. Username &amp; password login akan di-generate dan disimpan di server.
                </div>

                {{-- Santri --}}
                <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap"></i> Format Import SANTRI
                </p>
                <div class="overflow-x-auto rounded-xl border border-gray-200 mb-4">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-emerald-50 border-b border-gray-200 text-emerald-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold">NIS</th>
                                <th class="px-4 py-3 font-semibold">Nama</th>
                                <th class="px-4 py-3 font-semibold">Tanggal Masuk (YYYY-MM-DD)</th>
                                <th class="px-4 py-3 font-semibold">Kamar</th>
                                <th class="px-4 py-3 font-semibold">Fingerprint ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="bg-white">
                                <td class="px-4 py-3 font-mono text-xs">PST.2026.001</td>
                                <td class="px-4 py-3">Ahmad Wulansari</td>
                                <td class="px-4 py-3 font-mono text-xs">2026-01-15</td>
                                <td class="px-4 py-3">Kamar A1</td>
                                <td class="px-4 py-3 italic text-gray-400 text-xs">(boleh kosong)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-xs text-amber-700">
                    <strong>Tip:</strong> Gunakan tombol <strong>Download Template XLSX</strong> untuk mendapat file siap isi dengan format yang benar.
                </div>
            </div>
        </div>

        {{-- Riwayat import --}}
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-white/50 px-6 py-4">
                <h6 class="text-lg font-bold text-gray-800">Riwayat Import</h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Tanggal</th>
                            <th class="px-6 py-4 font-medium">Tipe</th>
                            <th class="px-6 py-4 font-medium">File</th>
                            <th class="px-6 py-4 font-medium text-center">Total</th>
                            <th class="px-6 py-4 font-medium text-center text-green-600">Berhasil</th>
                            <th class="px-6 py-4 font-medium text-center text-red-600">Gagal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->tipe === 'wali')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">WALI</span>
                                @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">SANTRI</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $log->file_asli }}">
                                {{ $log->file_asli }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $log->total_baris }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-green-600">{{ $log->berhasil }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold {{ $log->gagal > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $log->gagal }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-clock-rotate-left text-4xl mb-3 text-gray-200"></i>
                                    <p class="font-medium text-gray-400">Belum ada riwayat import data.</p>
                                    <p class="text-xs text-gray-400 mt-1">Upload file Excel di panel kiri untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sync template-tipe dengan import-type select
    const importTypeSelect = document.getElementById('import-type');
    const templateTipeInput = document.getElementById('template-tipe');

    importTypeSelect.addEventListener('change', function () {
        templateTipeInput.value = this.value;
    });

    // Preview nama file yang dipilih
    const fileInput = document.getElementById('file-upload');
    const fileLabel = document.getElementById('file-label');

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            const fileName = this.files[0].name;
            const fileSize = (this.files[0].size / 1024).toFixed(1);
            fileLabel.textContent = `${fileName} (${fileSize} KB)`;
            fileLabel.classList.add('text-indigo-600', 'font-medium');
            fileLabel.classList.remove('text-gray-500');
        }
    });

    // Disable submit button saat sedang upload
    const uploadForm = document.getElementById('upload-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');

    uploadForm.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        submitText.textContent = 'Mengupload...';
    });
</script>
@endsection
