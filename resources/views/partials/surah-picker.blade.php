{{--
  Surah Search Picker (tanpa nested x-data — state di parent scope)
  Props:
    $prefix      : prefix variabel Alpine di parent, misal 'awal' → awalSearch, awalOpen, awalFilter()
    $model       : nama variabel surah ID di parent, misal 'surahAwal'
    $fn          : method parent yang dipanggil setelah pilih, misal 'onSurahAwalChange'
    $name        : name attribute hidden input, misal 'surah_awal'
    $placeholder : teks placeholder
--}}
@php $ph = $placeholder ?? 'Cari nama atau nomor surat...'; @endphp

<div class="relative" @click.outside="{{ $prefix }}Open = false">

    {{-- Hidden input untuk submit --}}
    <input type="hidden" name="{{ $name }}" :value="{{ $model }}">

    {{-- Search Input --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </div>
        <input
            type="text"
            x-model="{{ $prefix }}Search"
            @input="{{ $prefix }}Filter(); {{ $prefix }}Open = true"
            @focus="{{ $prefix }}Filter(); {{ $prefix }}Open = true"
            class="w-full pl-8 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all"
            placeholder="{{ $ph }}"
            autocomplete="off"
        >
        <button type="button"
            x-show="{{ $model }}"
            @click="{{ $model }} = ''; {{ $prefix }}Search = ''; {{ $prefix }}Open = false"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-300 hover:text-red-400 transition-colors"
            tabindex="-1">
            <i class="fa-solid fa-circle-xmark text-sm"></i>
        </button>
    </div>

    {{-- Dropdown --}}
    <div
        x-show="{{ $prefix }}Open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute z-[999] w-full mt-1 bg-white border border-gray-100 rounded-2xl shadow-xl"
        style="max-height:240px; overflow-y:auto;"
    >
        <div x-show="{{ $prefix }}Filtered.length === 0" class="px-4 py-5 text-sm text-gray-400 text-center">
            <i class="fa-solid fa-magnifying-glass block text-2xl text-gray-200 mb-2"></i>
            Surat tidak ditemukan
        </div>
        <template x-for="s in {{ $prefix }}Filtered" :key="s.id">
            <div
                @mousedown.prevent="{{ $model }} = String(s.id); {{ $prefix }}Search = s.id + '. ' + s.nama_latin; {{ $prefix }}Open = false; {{ $fn }}()"
                class="flex items-center gap-3 px-4 py-2.5 cursor-pointer text-sm transition-colors hover:bg-indigo-50"
                :class="{{ $model }} == s.id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700'"
            >
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-extrabold shrink-0 transition-colors"
                    :class="{{ $model }} == s.id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500'"
                    x-text="s.id"></div>
                <span class="font-medium truncate" x-text="s.nama_latin"></span>
                <span class="ml-auto text-xs text-gray-400 shrink-0" x-text="s.jumlah_ayat + ' ayat'"></span>
            </div>
        </template>
    </div>
</div>
