@extends('layouts.app')
@section('title', 'Kirim Voice Note')
@section('page-title', 'Kirim Voice Note')

@section('sidebar')
    @include('partials.sidebar-wali')
@endsection

@section('content')
<div class="max-w-3xl mx-auto" x-data="voiceRecorder()">
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="border-b border-gray-100 bg-white/50 px-6 py-4 flex justify-between items-center">
            <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-microphone text-indigo-500"></i> Rekam & Kirim Pesan
            </h4>
            <a href="{{ route('wali.voice-note.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors">
                Batal
            </a>
        </div>
        
        <div class="p-6">
            <!-- Info Biaya -->
            <div class="mb-6 bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex gap-4 shadow-sm">
                <div class="text-indigo-500 text-2xl mt-0.5"><i class="fa-solid fa-circle-info"></i></div>
                <div>
                    <h6 class="font-bold text-indigo-900 mb-1">Informasi Layanan</h6>
                    <ul class="text-sm text-indigo-800/80 space-y-1">
                        <li>Biaya pengiriman: <strong class="text-indigo-700">Rp 100 per detik</strong></li>
                        <li>Batas maksimal: <strong class="text-indigo-700">5 menit (300 detik)</strong></li>
                        <li>Sisa saldo Anda: <strong class="text-indigo-700">Rp <span x-text="formatRupiah(saldoAwal)"></span></strong></li>
                    </ul>
                </div>
            </div>

            <form id="voiceNoteForm" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
                @csrf

                <!-- Pilih Penerima -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tujuan Pengiriman</label>
                    <select name="penerima_type" x-model="penerimaType" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none" required>
                        <option value="">-- Pilih Tujuan --</option>
                        <option value="santri">Santri (Anak Anda)</option>
                        <option value="wali" disabled>Wali Lain (Segera Hadir)</option>
                    </select>
                </div>

                <!-- Pilih Santri -->
                <div class="mb-6" x-show="penerimaType === 'santri'" x-transition>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Santri</label>
                    <select name="penerima_id" x-model="penerimaId" :required="penerimaType === 'santri'" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all bg-white appearance-none">
                        <option value="">-- Pilih Santri --</option>
                        @foreach($santriList as $santri)
                            <option value="{{ $santri->id }}">{{ $santri->nama }} (NIS: {{ $santri->nis }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Voice Recorder Interface -->
                <div class="mb-6 p-8 border-2 border-dashed rounded-2xl text-center transition-all duration-300" 
                     :class="{'border-red-400 bg-red-50/30': isRecording, 'border-indigo-400 bg-indigo-50/30': hasRecording && !isRecording, 'border-gray-200 bg-gray-50/50': !isRecording && !hasRecording}">
                    
                    <div x-show="!hasRecording" class="flex flex-col items-center justify-center py-4">
                        <button type="button" 
                            @click="toggleRecording"
                            class="w-24 h-24 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 transform active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/30"
                            :class="isRecording ? 'bg-red-500 text-white animate-pulse shadow-red-500/40' : 'bg-gradient-to-br from-indigo-500 to-indigo-700 text-white hover:shadow-indigo-500/40 hover:-translate-y-1'">
                            <i class="fa-solid fa-microphone text-4xl" x-show="!isRecording"></i>
                            <i class="fa-solid fa-stop text-4xl" x-show="isRecording"></i>
                        </button>
                        
                        <h5 class="mt-6 font-bold text-lg" :class="isRecording ? 'text-red-600' : 'text-gray-800'" x-text="isRecording ? 'Merekam Suara...' : 'Ketuk untuk Mulai Merekam'"></h5>
                        <p class="text-sm text-gray-500 mt-1" x-show="!isRecording">Pastikan izinkan akses mikrofon browser.</p>
                        
                        <div x-show="isRecording" class="mt-3 inline-block px-4 py-1.5 bg-red-100 text-red-700 rounded-full font-mono text-xl font-bold">
                            <span class="inline-block w-2 h-2 rounded-full bg-red-600 mr-2 animate-pulse"></span>
                            <span x-text="formatTime(recordingTime)"></span> <span class="text-red-400 text-sm">/ 05:00</span>
                        </div>
                    </div>

                    <!-- Audio Player for Preview -->
                    <div x-show="hasRecording" x-transition class="w-full">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h5 class="font-bold text-gray-800 mb-4">Rekaman Selesai</h5>
                        
                        <audio id="audioPlayback" controls class="w-full h-12 outline-none rounded-full shadow-sm bg-white mb-6"></audio>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Durasi</p>
                                <p class="font-extrabold text-gray-800 text-xl"><span x-text="recordedDuration"></span> <span class="text-sm font-normal text-gray-500">detik</span></p>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Estimasi Biaya</p>
                                <p class="font-extrabold text-indigo-600 text-xl">Rp <span x-text="formatRupiah(biaya)"></span></p>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button type="button" @click="resetRecording" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-semibold shadow-sm">
                                <i class="fa-solid fa-rotate-right mr-1.5"></i> Rekam Ulang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="durasi_detik" x-model="recordedDuration">

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold transition-all flex justify-center items-center gap-2"
                        :class="canSubmit ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-200 hover:-translate-y-0.5' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        :disabled="!canSubmit || isSubmitting">
                        
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane" x-show="saldoCukup"></i>
                            <i class="fa-solid fa-triangle-exclamation text-amber-500" x-show="!saldoCukup"></i>
                            <span x-text="submitText"></span>
                        </span>
                        
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('voiceRecorder', () => ({
        penerimaType: '',
        penerimaId: '',
        saldoAwal: {{ Auth::user()->wali->saldo ?? 0 }},
        isRecording: false,
        hasRecording: false,
        recordingTime: 0,
        recordedDuration: 0,
        biaya: 0,
        timerInterval: null,
        mediaRecorder: null,
        audioChunks: [],
        audioBlob: null,
        isSubmitting: false,

        get saldoCukup() {
            return this.saldoAwal >= this.biaya;
        },

        get canSubmit() {
            return this.penerimaType && 
                   (this.penerimaType !== 'santri' || this.penerimaId) && 
                   this.hasRecording && 
                   this.recordedDuration > 0 && 
                   this.saldoCukup;
        },

        get submitText() {
            if (!this.saldoCukup) return 'Saldo Tidak Cukup';
            return 'Kirim Pesan Suara';
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        async toggleRecording() {
            if (this.isRecording) {
                this.stopRecording();
            } else {
                await this.startRecording();
            }
        },

        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(this.audioBlob);
                    document.getElementById('audioPlayback').src = audioUrl;
                    
                    this.recordedDuration = this.recordingTime;
                    this.biaya = this.recordedDuration * 100;
                    this.hasRecording = true;
                    
                    stream.getTracks().forEach(track => track.stop());
                };

                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingTime = 0;
                
                this.timerInterval = setInterval(() => {
                    this.recordingTime++;
                    if (this.recordingTime >= 300) {
                        this.stopRecording();
                    }
                }, 1000);
            } catch (err) {
                alert('Tidak dapat mengakses mikrofon. Pastikan Anda telah memberikan izin.');
                console.error(err);
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.isRecording) {
                this.mediaRecorder.stop();
                clearInterval(this.timerInterval);
                this.isRecording = false;
            }
        },

        resetRecording() {
            this.hasRecording = false;
            this.audioBlob = null;
            this.recordedDuration = 0;
            this.biaya = 0;
            document.getElementById('audioPlayback').src = '';
        },

        async submitForm() {
            if (!this.canSubmit || this.isSubmitting) return;
            
            this.isSubmitting = true;
            const formData = new FormData(document.getElementById('voiceNoteForm'));
            formData.append('audio', this.audioBlob, 'voicenote.webm');
            
            try {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("wali.voice-note.store") }}', true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            window.location.href = data.redirect || '{{ route("wali.voice-note.index") }}';
                        } catch (e) {
                            window.location.href = '{{ route("wali.voice-note.index") }}';
                        }
                    } else {
                        alert('Gagal mengirim voice note. Kode: ' + xhr.status);
                        Alpine.store('voiceRecorder').isSubmitting = false;
                    }
                };
                
                xhr.onerror = function() {
                    alert('Terjadi kesalahan koneksi.');
                    Alpine.store('voiceRecorder').isSubmitting = false;
                };
                
                xhr.send(formData);
            } catch (e) {
                console.error(e);
            }
        }
    }));
});
</script>
@endsection