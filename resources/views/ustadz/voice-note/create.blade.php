@extends('layouts.app')
@section('title', 'Kirim Voice Note')
@section('page-title', 'Kirim Voice Note ke Wali')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl shadow-md p-6 mb-6 text-white" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%)">
    <p class="text-teal-200 text-sm font-medium">Voice Note</p>
    <h3 class="text-2xl font-extrabold">Kirim Pesan Suara ke Wali</h3>
    <p class="text-teal-100 text-sm mt-1">
        <i class="fa-solid fa-gift mr-1"></i> Voice note dari ustadz gratis — tidak memotong uang saku santri.
    </p>
</div>

<form method="POST" action="{{ route('ustadz.voice-note.store') }}" enctype="multipart/form-data"
      class="glass-panel rounded-2xl shadow-sm p-6 space-y-5"
      x-data="voiceRecorder()" @submit="prepareSubmit()">
    @csrf

    <div x-data="santriPickerVN()" class="relative">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Santri <span class="text-red-500">*</span></label>
        <input type="hidden" name="santri_id" :value="santriId" required>
        @if($santri)
            {{-- Pre-fill kalau dari kunjungan klinik --}}
            <script>document.addEventListener('alpine:init', () => { window._vnPreselect = {{ $santri->id }}; window._vnPreselectLabel = '{{ $santri->nama }} ({{ $santri->nis }})'; })</script>
        @endif
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
            </div>
            <input type="text" x-model="query" @input="filter()" @focus="open = true; filter()" @click.away="open = false"
                   :placeholder="selected ? selected : 'Ketik nama, NIS, atau kelas...'"
                   :class="selected ? 'text-gray-800 font-semibold' : 'text-gray-500'"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
            <button x-show="selected" @click="reset()" type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div x-show="open && results.length > 0" @click.away="open = false"
             class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 max-h-56 overflow-y-auto">
            <template x-for="s in results" :key="s.id">
                <div @click="pick(s)" class="px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b border-gray-50 flex justify-between items-center">
                    <span class="font-semibold text-gray-800 text-sm" x-text="s.nama"></span>
                    <span class="text-xs text-gray-400" x-text="s.nis + (s.kelas ? ' · ' + s.kelas : '')"></span>
                </div>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Konteks</label>
            <select name="konteks" class="w-full px-4 py-2.5 rounded-xl border border-gray-200">
                <option value="umum">Umum</option>
                <option value="kesehatan" @selected($kunjungan)>Kesehatan</option>
                <option value="akademik">Akademik / Hafalan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan Singkat (Opsional)</label>
            <input type="text" name="pesan_teks" maxlength="500"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200"
                   placeholder="Judul/ringkasan yang muncul di notifikasi">
        </div>
    </div>

    @if($kunjungan)
        <input type="hidden" name="kunjungan_klinik_id" value="{{ $kunjungan->id }}">
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700">
            <i class="fa-solid fa-link mr-1"></i> Voice note ini terkait dengan kunjungan klinik <strong>{{ $kunjungan->diagnosa }}</strong> pada {{ $kunjungan->tanggal_kunjungan?->format('d/m/Y') }}.
        </div>
    @endif

    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6">
        <div class="flex flex-col items-center">
            <button type="button" @click="toggleRecord()"
                    class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl transition-all"
                    :class="recording ? 'bg-red-600 animate-pulse' : 'bg-teal-600 hover:bg-teal-700'">
                <i class="fa-solid" :class="recording ? 'fa-stop' : 'fa-microphone'"></i>
            </button>
            <p class="mt-3 text-sm font-semibold" x-text="recording ? 'Sedang merekam... tekan untuk berhenti' : 'Tekan untuk mulai merekam'"></p>
            <p class="text-2xl font-bold mt-2" x-text="formatTime(duration)"></p>
            <p class="text-xs text-gray-400 mt-1">Max 5 menit.</p>

            <template x-if="audioUrl">
                <div class="mt-4 w-full">
                    <audio :src="audioUrl" controls class="w-full"></audio>
                    <button type="button" @click="reset()" class="mt-2 text-xs text-red-500 hover:underline">Rekam ulang</button>
                </div>
            </template>
        </div>
        <input type="file" name="audio" x-ref="audioInput" accept="audio/*" class="hidden">
        <input type="hidden" name="durasi_detik" :value="duration">
    </div>

    <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
        <a href="{{ route('ustadz.voice-note.index') }}" class="px-5 py-2 rounded-xl text-gray-600 hover:bg-gray-100">Batal</a>
        <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-medium shadow-sm"
                :disabled="!audioBlob || recording">
            <i class="fa-solid fa-paper-plane mr-1"></i> Kirim
        </button>
    </div>
</form>

<script>
function voiceRecorder() {
    return {
        recording: false,
        mediaRecorder: null,
        chunks: [],
        audioBlob: null,
        audioUrl: null,
        duration: 0,
        timer: null,

        async toggleRecord() {
            if (this.recording) {
                this.mediaRecorder.stop();
                this.recording = false;
                clearInterval(this.timer);
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.chunks = [];
                this.mediaRecorder = new MediaRecorder(stream);
                this.mediaRecorder.ondataavailable = (e) => this.chunks.push(e.data);
                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.chunks, { type: 'audio/webm' });
                    this.audioUrl = URL.createObjectURL(this.audioBlob);
                    stream.getTracks().forEach(t => t.stop());
                };
                this.mediaRecorder.start();
                this.recording = true;
                this.duration = 0;
                this.timer = setInterval(() => {
                    this.duration++;
                    if (this.duration >= 300) this.toggleRecord();
                }, 1000);
            } catch (e) {
                alert('Gagal akses mikrofon: ' + e.message);
            }
        },

        reset() {
            this.audioBlob = null;
            this.audioUrl = null;
            this.duration = 0;
            this.$refs.audioInput.value = '';
        },

        prepareSubmit() {
            if (!this.audioBlob) return;
            const file = new File([this.audioBlob], 'voice-note.webm', { type: 'audio/webm' });
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.audioInput.files = dt.files;
        },

        formatTime(s) {
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        }
    }
}

// Santri searchable picker untuk voice note
function santriPickerVN() {
    const ALL = @json($santriList->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->kelas ?? '']));
    const preId    = typeof window._vnPreselect !== 'undefined' ? window._vnPreselect : null;
    const preLabel = typeof window._vnPreselectLabel !== 'undefined' ? window._vnPreselectLabel : '';
    return {
        query: '', open: false, results: ALL,
        santriId: preId || '',
        selected: preLabel || '',
        filter() {
            const q = this.query.toLowerCase().trim();
            this.results = q
                ? ALL.filter(s => s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q) || (s.kelas||'').toLowerCase().includes(q))
                : ALL;
            this.open = true;
        },
        pick(s) {
            this.santriId = s.id;
            this.selected = s.nama + ' (' + s.nis + ')';
            this.query = ''; this.open = false;
        },
        reset() { this.santriId = ''; this.selected = ''; this.query = ''; this.results = ALL; }
    }
}
</script>
@endsection
