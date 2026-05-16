@extends('layouts.app')
@section('title', 'Klinik (Rekam Medis)')
@section('page-title', 'Klinik')

@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative" x-data="klinikData()">
    <!-- Left Column: Input -->
    <div class="lg:col-span-7 space-y-6">
        <div class="glass-panel rounded-2xl shadow-sm overflow-visible p-6 border-l-4 border-emerald-500 relative">
            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-stethoscope text-emerald-500"></i> Pemeriksaan Pasien
            </h6>

            <!-- Search Area -->
            <div class="relative z-50">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Pasien (Nama/NIK)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="searchPatient()" 
                           @focus="searchPatient(); showDropdown = true"
                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                           placeholder="Ketik nama, NIS, NIK, atau kelas...">
                </div>

                <!-- Dropdown Results -->
                <div x-show="showDropdown && searchResults.length > 0" @click.away="showDropdown = false"
                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 max-h-80 overflow-y-auto">
                    <template x-for="item in searchResults" :key="item.type + item.id">
                        <div @click="selectPatient(item)" class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-gray-50 flex justify-between items-center transition-colors">
                            <div>
                                <div class="font-bold text-gray-800" x-text="item.nama"></div>
                                <div class="text-xs text-gray-500">
                                    <span x-show="item.nis" x-text="'NIS: ' + item.nis"></span>
                                    <span x-show="item.nis && item.kelas"> · </span>
                                    <span x-show="item.kelas" x-text="'Kelas ' + item.kelas"></span>
                                    <span x-show="item.nik" x-text="(item.nis || item.kelas ? ' · NIK: ' : 'NIK: ') + item.nik"></span>
                                    <span x-show="!item.nik && !item.nis && !item.kelas" class="italic text-gray-400">data belum lengkap</span>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-lg font-medium" 
                                  :class="item.badge === 'Santri' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'"
                                  x-text="item.badge"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Selected Patient Info -->
            <div x-show="selectedPatient" class="mt-6 p-4 rounded-xl bg-gray-50 border border-gray-200" style="display: none;">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg" x-text="selectedPatient?.nama"></h4>
                        <div class="flex gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs" x-text="selectedPatient?.badge"></span>
                            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs" x-text="selectedPatient?.nik || 'Tanpa NIK'"></span>
                        </div>
                    </div>
                    <button @click="resetSelection()" type="button" class="text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h5 class="text-sm font-bold text-gray-700 mb-2">Riwayat Medis Sebelumnya:</h5>
                    <div x-show="historyLoading" class="text-xs text-gray-500 italic">Memuat riwayat...</div>
                    <div x-show="!historyLoading && history.length === 0" class="text-xs text-gray-500 italic">Belum ada riwayat berobat.</div>
                    
                    <div class="space-y-2 mt-2 max-h-40 overflow-y-auto pr-2">
                        <template x-for="h in history" :key="h.id">
                            <div class="p-3 bg-white rounded-lg border border-gray-100 shadow-sm text-sm">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span x-text="formatDate(h.tanggal_kunjungan)"></span>
                                    <span x-text="'Pemeriksa: ' + h.pemeriksa.nama"></span>
                                </div>
                                <div class="font-semibold text-gray-800" x-text="h.diagnosa"></div>
                                <div class="text-gray-600 mt-1" x-text="h.tindakan_obat"></div>
                                <div class="mt-2 inline-block px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800" x-text="formatStatus(h.status_pengobatan, h.lama_istirahat_hari)"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <form x-show="selectedPatient" action="{{ route('ustadz.klinik.store') }}" method="POST" class="mt-6 space-y-4" style="display: none;">
                @csrf
                <input type="hidden" name="patient_type" :value="selectedPatient?.type">
                <input type="hidden" name="patient_id" :value="selectedPatient?.id">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keluhan / Gejala <span class="text-red-500">*</span></label>
                    <textarea name="keluhan" rows="2" required
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                              placeholder="Misal: Demam sejak semalam, mual"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Diagnosa <span class="text-red-500">*</span></label>
                    <input type="text" name="diagnosa" required
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                              placeholder="Misal: Gejala Tipes / Flu Biasa">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tindakan & Obat <span class="text-red-500">*</span></label>
                    <textarea name="tindakan_obat" rows="2" required
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                              placeholder="Misal: Diberikan paracetamol 500mg, kompres"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pengobatan <span class="text-red-500">*</span></label>
                        <select name="status_pengobatan" x-model="status" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all bg-white">
                            <option value="aktifitas_normal">Aktifitas Normal</option>
                            <option value="istirahat">Istirahat di Asrama</option>
                            <option value="dirujuk">Dirujuk ke RS/Puskesmas</option>
                            <option value="dirawat_orang_tua">Dirawat Orang Tua (Pulang)</option>
                        </select>
                    </div>
                    <div x-show="status === 'istirahat'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lama Istirahat (Hari)</label>
                        <input type="number" name="lama_istirahat_hari" min="1"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                               placeholder="Jumlah hari">
                    </div>
                </div>

                <div x-show="status === 'dirujuk' || status === 'dirawat_orang_tua'" style="display:none">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan untuk Orang Tua</label>
                    <textarea name="catatan_ortu" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl border border-red-200 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all bg-red-50/30"
                              placeholder="Instruksi tambahan untuk wali santri..."></textarea>
                    <p class="text-xs text-red-600 mt-1">
                        <i class="fa-solid fa-bell mr-1"></i> Notifikasi otomatis akan dikirim ke wali santri.
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Rekam Medis
                    </button>
                    <p class="text-xs text-center text-gray-500 mt-3">
                        <i class="fa-solid fa-info-circle text-emerald-500"></i> Menyimpan rekam medis akan mengirimkan notifikasi ke aplikasi Wali Santri (jika pasien adalah santri).
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Recent -->
    <div class="lg:col-span-5 space-y-6">
        <div class="glass-panel rounded-2xl shadow-sm p-6 border-t-4 border-indigo-500">
            <h6 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Pemeriksaan Terakhir
            </h6>
            
            <div class="space-y-4">
                @forelse($kunjungan as $k)
                <div class="p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-bold text-gray-800 text-sm">{{ $k->patient->nama }}</span>
                            <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded">{{ class_basename($k->patient_type) }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $k->tanggal_kunjungan->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2"><strong>Dx:</strong> {{ $k->diagnosa }}</p>
                    
                    @if($k->status_pengobatan == 'aktifitas_normal')
                        <span class="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs rounded-lg border border-green-100">Aktifitas Normal</span>
                    @elseif($k->status_pengobatan == 'istirahat')
                        <span class="inline-block px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded-lg border border-amber-100">Istirahat {{ $k->lama_istirahat_hari }} Hari</span>
                    @elseif($k->status_pengobatan == 'dirujuk')
                        <span class="inline-block px-2 py-1 bg-red-50 text-red-700 text-xs rounded-lg border border-red-100">Dirujuk</span>
                    @else
                        <span class="inline-block px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-lg border border-purple-100">Dirawat Ortu</span>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 text-sm">
                    Belum ada data pemeriksaan hari ini.
                </div>
                @endforelse
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                {{ $kunjungan->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function klinikData() {
        return {
            searchQuery: '',
            searchResults: [],
            showDropdown: false,
            selectedPatient: null,
            history: [],
            historyLoading: false,
            status: 'aktifitas_normal',
            
            async searchPatient() {
                if (this.searchQuery.length < 1) {
                    this.searchResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/ustadz/klinik/api/search?q=${encodeURIComponent(this.searchQuery)}`);
                    this.searchResults = await res.json();
                    this.showDropdown = true;
                } catch (e) {
                    console.error(e);
                }
            },
            
            async selectPatient(patient) {
                this.selectedPatient = patient;
                this.searchQuery = '';
                this.showDropdown = false;
                this.historyLoading = true;
                this.history = [];
                
                try {
                    const res = await fetch(`/ustadz/klinik/api/history?type=${encodeURIComponent(patient.type)}&id=${patient.id}`);
                    this.history = await res.json();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.historyLoading = false;
                }
            },
            
            resetSelection() {
                this.selectedPatient = null;
                this.history = [];
                this.status = 'aktifitas_normal';
            },

            formatDate(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            formatStatus(status, hari) {
                if(status === 'aktifitas_normal') return 'Aktifitas Normal';
                if(status === 'istirahat') return 'Istirahat ' + hari + ' Hari';
                if(status === 'dirujuk') return 'Dirujuk';
                if(status === 'dirawat_orang_tua') return 'Dirawat Orang Tua';
                return status;
            }
        }
    }
</script>
@endpush
@endsection
