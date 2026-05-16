@extends('layouts.app')
@section('title', 'Pengaturan Finance')
@section('page-title', 'Pengaturan Finance')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.settings.update') }}" class="max-w-4xl">
    @csrf @method('PUT')

    @php
        $groupLabels = [
            'organisasi' => ['Identitas Yayasan', 'fa-building', 'Tampil di kop laporan PDF.'],
            'fitur'      => ['Aktifkan / Nonaktifkan Fitur', 'fa-toggle-on', 'Atur fitur mana yang ingin digunakan oleh bendahara.'],
            'approval'   => ['Threshold Approval', 'fa-user-check', 'Pengeluaran di atas nominal akan butuh approval.'],
            'pajak'      => ['Konfigurasi Pajak (PPh 21)', 'fa-percent', 'Hanya berfungsi jika fitur pajak diaktifkan.'],
            'reminder'   => ['Reminder & Alert', 'fa-bell', 'Pengingat jatuh tempo hutang & alert budget.'],
        ];
    @endphp

    @foreach($settings as $group => $items)
        <div class="glass-panel rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid {{ $groupLabels[$group][1] ?? 'fa-cog' }}"></i>
                </div>
                <div>
                    <h6 class="text-lg font-bold text-gray-800">{{ $groupLabels[$group][0] ?? ucfirst($group) }}</h6>
                    <p class="text-xs text-gray-500">{{ $groupLabels[$group][2] ?? '' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($items as $s)
                <div class="{{ $s->type == 'boolean' ? 'md:col-span-2' : '' }}">
                    <label class="block text-sm font-semibold mb-1">{{ $s->label ?: $s->key }}</label>

                    @if($s->type == 'boolean')
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings[{{ $s->key }}]" value="0">
                            <input type="checkbox" name="settings[{{ $s->key }}]" value="1" class="sr-only peer" @checked($s->casted_value)>
                            <div class="relative w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:bg-emerald-500 transition-colors">
                                <div class="absolute top-0.5 left-0.5 bg-white rounded-full w-5 h-5 shadow-md transition-all peer-checked:translate-x-6"></div>
                            </div>
                            <span class="ms-3 text-sm font-medium text-gray-700">
                                {{ $s->casted_value ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </label>
                    @elseif($s->type == 'integer' || $s->type == 'decimal')
                        <input type="number" step="{{ $s->type == 'decimal' ? '0.01' : '1' }}" name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="w-full rounded-lg border-gray-300 text-sm">
                    @else
                        <input type="text" name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="w-full rounded-lg border-gray-300 text-sm">
                    @endif

                    @if($s->description)
                        <p class="text-xs text-gray-500 mt-1">{{ $s->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="flex gap-3 sticky bottom-0 bg-white/80 backdrop-blur-md p-3 rounded-xl border">
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">
            <i class="fa-solid fa-save mr-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
