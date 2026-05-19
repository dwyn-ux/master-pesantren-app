@extends('layouts.app')
@section('title', 'Kelola Password Staff')
@section('page-title', 'Kelola Password Staff')

@section('sidebar')
    @include('partials.sidebar-superadmin')
@endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" class="mb-5 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-xl shadow-sm relative">
    <button @click="show = false" class="absolute top-3 right-3 text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
    <div class="flex gap-3 text-green-800">
        <i class="fa-solid fa-circle-check text-lg mt-0.5"></i>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('credential'))
<div x-data="{ show: true }" x-show="show" class="mb-5 bg-amber-50 border-l-4 border-amber-400 p-5 rounded-r-xl shadow-sm relative">
    <button @click="show = false" class="absolute top-3 right-3 text-amber-500 hover:text-amber-700"><i class="fa-solid fa-xmark"></i></button>
    <div class="flex gap-3 text-amber-800">
        <i class="fa-solid fa-key text-xl mt-0.5"></i>
        <div>
            <h4 class="font-bold mb-1">Password Baru — Catat Sekarang!</h4>
            <div class="text-sm leading-relaxed">{!! session('credential') !!}</div>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-5 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
    <div class="flex gap-3 text-red-800">
        <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif

<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <h6 class="text-lg font-bold text-gray-800">Manajemen Password Staff</h6>
        <p class="text-sm text-gray-500">Reset atau ubah password akun staff pesantren</p>
    </div>

    {{-- Filter --}}
    <div class="p-5 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama / username..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none text-sm">
            </div>
            <div class="min-w-[160px]">
                <select name="role" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none bg-white text-sm">
                    <option value="">Semua Role</option>
                    @foreach($roleLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('role') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('superadmin.staff-password.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-6 py-4 font-medium">Nama</th>
                    <th class="px-6 py-4 font-medium">Username</th>
                    <th class="px-6 py-4 font-medium">Role</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-purple-50/20 transition-colors" x-data="{ open: false }">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-md border border-gray-200">{{ $user->username }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                        @php
                            $color = match($role->name) {
                                'admin'         => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'bendahara'     => 'bg-green-100 text-green-700 border-green-200',
                                'kepala_pondok' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'kesantrian'    => 'bg-orange-100 text-orange-700 border-orange-200',
                                'ustadz'        => 'bg-blue-100 text-blue-700 border-blue-200',
                                'outlet'        => 'bg-pink-100 text-pink-700 border-pink-200',
                                default         => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                            {{ $roleLabels[$role->name] ?? $role->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        @if($user->must_change_pw)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i> Belum ganti PW
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                <i class="fa-solid fa-check text-xs"></i> Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Reset ke default --}}
                            <form method="POST" action="{{ route('superadmin.staff-password.reset', $user) }}"
                                  onsubmit="return confirm('Reset password {{ $user->name }} ke default ({{ $user->username }}@1234)?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs font-medium transition-colors flex items-center gap-1.5"
                                        title="Reset ke password default">
                                    <i class="fa-solid fa-rotate-left"></i> Reset Default
                                </button>
                            </form>

                            {{-- Set password manual --}}
                            <button @click="open = !open"
                                    class="px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-medium transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-key"></i> Set Password
                            </button>
                        </div>

                        {{-- Inline form set password --}}
                        <div x-show="open" x-transition class="mt-3 p-4 bg-purple-50 rounded-xl border border-purple-200 text-left">
                            <form method="POST" action="{{ route('superadmin.staff-password.update', $user) }}">
                                @csrf @method('PUT')
                                <p class="text-xs font-bold text-purple-700 mb-3">Set Password Baru — {{ $user->name }}</p>
                                <div class="space-y-2">
                                    <input type="password" name="password" placeholder="Password baru (min. 6 karakter)"
                                           class="w-full px-3 py-2 rounded-lg border border-purple-200 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none text-sm bg-white">
                                    <input type="password" name="password_confirmation" placeholder="Konfirmasi password"
                                           class="w-full px-3 py-2 rounded-lg border border-purple-200 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none text-sm bg-white">
                                    <label class="flex items-center gap-2 text-xs text-purple-700 cursor-pointer">
                                        <input type="checkbox" name="must_change_pw" value="1" checked class="rounded">
                                        Wajib ganti password saat login berikutnya
                                    </label>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <button type="submit" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition-colors">
                                        Simpan
                                    </button>
                                    <button type="button" @click="open = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data staff ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Info box --}}
<div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
    <i class="fa-solid fa-circle-info mr-2"></i>
    <strong>Reset Default</strong> akan mengubah password menjadi <code class="bg-blue-100 px-1 rounded">username@1234</code> dan menandai akun wajib ganti password saat login berikutnya.
</div>

@endsection
