@extends('layouts.app')

@section('title', 'Pengaturan Raport')
@section('page-title', 'Pengaturan Raport')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Pengaturan Raport</h2>
            <p class="text-sm text-gray-500">Atur identitas pesantren, logo, dan pejabat penandatangan untuk raport.</p>
        </div>

        <form action="{{ route('admin.akademik.raport-setting.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf @method('PUT')

            {{-- ── KOP ───────────────────────────────────────── --}}
            <section>
                <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-700 border-b border-indigo-100 pb-2 mb-4">
                    <i class="fa-solid fa-mosque mr-2"></i>KOP / Identitas Pesantren
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pesantren</label>
                        <input type="text"
                               name="raport_nama_pesantren"
                               value="{{ $settings['raport.nama_pesantren'] }}"
                               placeholder="Pondok Pesantren Ash-Shiddiq"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <input type="text"
                               name="raport_alamat"
                               value="{{ $settings['raport.alamat'] }}"
                               placeholder="Jl. Merdeka No. 123, Yogyakarta"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kontak / Telepon / Email</label>
                        <input type="text"
                               name="raport_kontak"
                               value="{{ $settings['raport.kontak'] }}"
                               placeholder="(0274) 123456 / info@pondok.id"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo Pesantren
                            <span class="text-xs text-gray-400">(JPG/PNG/SVG, max 2MB)</span>
                        </label>

                        @if(!empty($settings['raport.logo_url']))
                            <div class="flex items-center gap-3 p-3 mb-2 bg-gray-50 rounded-lg border border-gray-200">
                                <img src="/{{ ltrim($settings['raport.logo_url'], '/') }}" alt="Logo" class="w-16 h-16 object-contain border border-gray-200 rounded bg-white">
                                <div class="flex-1 text-xs text-gray-500 break-all">{{ $settings['raport.logo_url'] }}</div>
                                <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer hover:text-red-800">
                                    <input type="checkbox" name="remove_logo" value="1" class="w-4 h-4 rounded border-gray-300 text-red-500">
                                    Hapus
                                </label>
                            </div>
                        @endif

                        <input type="file" name="logo_file" accept="image/jpeg,image/png,image/svg+xml"
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Logo akan otomatis di-resize agar pas di KOP raport.</p>
                        @error('logo_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- ── Pejabat ───────────────────────────────────── --}}
            <section>
                <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-700 border-b border-indigo-100 pb-2 mb-4">
                    <i class="fa-solid fa-user-tie mr-2"></i>Mudir (Penandatangan Raport)
                </h3>

                <div class="border border-gray-200 rounded-xl p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Nama Mudir</label>
                            <input type="text"
                                   name="raport_mudir"
                                   value="{{ $settings['raport.mudir'] }}"
                                   placeholder="KH. Ahmad Dahlan, M.A."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Jabatan</label>
                            <input type="text"
                                   name="raport_mudir_jabatan"
                                   value="{{ $settings['raport.mudir_jabatan'] }}"
                                   placeholder="Mudir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Penerbitan Raport</label>
                    <input type="text"
                           name="raport_tempat_terbit"
                           value="{{ $settings['raport.tempat_terbit'] }}"
                           placeholder="Yogyakarta"
                           class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Akan muncul di tanggal tanda tangan Mudir, contoh: <em>Yogyakarta, 16 Mei 2026</em>.</p>
                </div>

                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Wali Kelas akan otomatis diambil dari data kelas masing-masing santri (atur di menu <strong>Kelas</strong>). Wali Santri otomatis diambil dari data wali yang terhubung dengan santri.
                </div>
            </section>

            <div class="flex justify-end pt-4 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
