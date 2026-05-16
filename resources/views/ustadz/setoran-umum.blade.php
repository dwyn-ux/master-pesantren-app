@extends('layouts.app')
@section('title', 'Setoran Umum')
@section('page-title', 'Setoran Umum')
@section('sidebar')
    @include('partials.sidebar-ustadz')
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="setoranUmumForm()" x-init="init()">

@if(session('success'))
<div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-bold">
    <i class="fa-solid fa-circle-check text-xl"></i> {{ session('success') }}
</div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h4 class="text-xl font-extrabold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-users text-indigo-500"></i> Setoran Umum
        </h4>
        <p class="text-sm text-gray-500 mt-1">Menerima setoran dari santri mana saja (tidak terbatas halaqah sendiri)</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('ustadz.setoran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-bold text-sm hover:bg-amber-100 transition-colors">
            <i class="fa-solid fa-book-quran"></i> Setoran Halaqah
        </a>
        <button @click="showForm = !showForm" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors shadow-md">
            <i class="fa-solid" :class="showForm ? 'fa-xmark' : 'fa-plus'"></i>
            <span x-text="showForm ? 'Tutup' : 'Catat Setoran'"></span>
        </button>
    </div>
</div>

{{-- FORM --}}
<div x-show="showForm" x-transition x-cloak class="glass-panel rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50/50">
        <h6 class="font-bold text-indigo-800 flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-indigo-500"></i> Form Setoran Umum
            <span class="ml-2 text-xs font-medium bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full border border-indigo-200">Semua Santri Aktif</span>
        </h6>
    </div>
    <form method="POST" action="{{ route('ustadz.setoran-umum.store') }}" class="p-6 space-y-5">
        @csrf
        <input type="hidden" name="jumlah_halaman" :value="jumlahHalaman">

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1.5">Santri <span class="text-red-500">*</span></label>
            <select name="santri_id" x-model="santriId" @change="loadLastPosition" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm focus:border-indigo-500" required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santriList as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                @endforeach
            </select>
        </div>

        <div x-show="lastInfo" x-transition class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5"></i>
            <div>
                <span class="font-bold text-indigo-700">Setoran ziyadah terakhir:</span>
                <span class="text-indigo-600" x-text="lastInfo"></span>
                <span class="block text-xs text-indigo-400 mt-0.5">Form dilanjutkan dari posisi berikutnya ↓</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Jenis</label>
                <select name="jenis" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm" required>
                    <option value="ziyadah">Ziyadah</option>
                    <option value="murojaah">Murojaah</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm" required>
                    <option value="maqbul">✓ Maqbul</option>
                    <option value="dhaif">~ Dhaif</option>
                    <option value="kurang">✗ Kurang</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Tanggal</label>
                <input type="date" name="tanggal" :value="tanggal" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm" required>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-5 space-y-4">
            <h6 class="text-sm font-extrabold text-gray-600 flex items-center gap-2">
                <i class="fa-solid fa-book-open text-indigo-400"></i> Rentang Hafalan
            </h6>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Surat Awal</label>
                    @include('partials.surah-picker', [
                        'prefix'      => 'awal',
                        'model'       => 'surahAwal',
                        'fn'          => 'onSurahAwalChange',
                        'name'        => 'surah_awal',
                        'placeholder' => 'Cari surat awal...',
                    ])
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ayat Awal</label>
                    <input type="number" name="ayat_awal" x-model="ayatAwal" @input="hitungHalaman"
                        min="1" :max="maxAyatAwal"
                        class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm text-sm focus:border-indigo-500 transition-all"
                        :disabled="!surahAwal" :placeholder="surahAwal ? '1 – ' + maxAyatAwal : '—'" required>
                    <p class="mt-1 text-xs text-gray-400" x-show="surahAwal">Maks: <span x-text="maxAyatAwal" class="font-bold"></span> ayat</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Surat Akhir</label>
                    @include('partials.surah-picker', [
                        'prefix'      => 'akhir',
                        'model'       => 'surahAkhir',
                        'fn'          => 'onSurahAkhirChange',
                        'name'        => 'surah_akhir',
                        'placeholder' => 'Cari surat akhir...',
                    ])
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ayat Akhir</label>
                    <input type="number" name="ayat_akhir" x-model="ayatAkhir" @input="hitungHalaman"
                        min="1" :max="maxAyatAkhir"
                        class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm text-sm focus:border-indigo-500 transition-all"
                        :disabled="!surahAkhir" :placeholder="surahAkhir ? '1 – ' + maxAyatAkhir : '—'" required>
                    <p class="mt-1 text-xs text-gray-400" x-show="surahAkhir">Maks: <span x-text="maxAyatAkhir" class="font-bold"></span> ayat</p>
                </div>
            </div>
            <div x-show="jumlahHalaman > 0" x-transition class="bg-white border border-indigo-100 rounded-xl px-5 py-3 flex items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-lines text-indigo-400 text-xl"></i>
                    <div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Hafalan</div>
                        <div class="font-extrabold text-gray-800">
                            <span x-text="jumlahHalaman" class="text-2xl text-indigo-600"></span>
                            <span class="text-sm text-gray-500 ml-1">halaman</span>
                            <span class="text-sm text-gray-400 ml-2" x-show="jumlahJuz > 0">(~<span x-text="jumlahJuz"></span> juz)</span>
                        </div>
                    </div>
                </div>
                <div class="text-xs text-gray-400 text-right" x-show="halamanAwal > 0">
                    Hal. <span x-text="halamanAwal" class="font-bold"></span> → Hal. <span x-text="halamanAkhir" class="font-bold"></span>
                    <span class="block">Standar Mushaf Indonesia</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1.5">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl outline-none shadow-sm resize-none" placeholder="Catatan..."></textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-extrabold transition-colors shadow-md flex justify-center items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Setoran
            </button>
            <button type="button" @click="showForm = false" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold">Batal</button>
        </div>
    </form>
</div>

{{-- TABEL --}}
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-white/50">
        <h6 class="font-bold text-gray-800">Riwayat Setoran Umum <span class="text-gray-400 font-normal">({{ $setoran->total() }})</span></h6>
    </div>
    @if($setoran->isEmpty())
        <div class="p-16 text-center"><i class="fa-solid fa-scroll text-4xl text-gray-300 mb-3 block"></i><p class="text-gray-500">Belum ada catatan setoran umum.</p></div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead><tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="px-4 py-4 font-medium">Tanggal</th>
                    <th class="px-4 py-4 font-medium">Santri</th>
                    <th class="px-4 py-4 font-medium">Rentang</th>
                    <th class="px-4 py-4 font-medium">Jenis</th>
                    <th class="px-4 py-4 font-medium">Halaman</th>
                    <th class="px-4 py-4 font-medium">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($setoran as $s)
                    @php $sd = match($s->status) { 'maqbul'=>['bg-emerald-100','text-emerald-700','✓ Maqbul'], 'dhaif'=>['bg-amber-100','text-amber-700','~ Dhaif'], 'kurang'=>['bg-red-100','text-red-700','✗ Kurang'], default=>['bg-gray-100','text-gray-600',$s->status] }; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-bold text-gray-700">{{ $s->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $s->santri?->nama ?? '-' }}<div class="text-xs text-gray-500 font-mono">{{ $s->santri?->nis }}</div></td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $s->surahAwal?->nama_latin ?? 'S.'.$s->surah_awal }} {{ $s->ayat_awal }} → {{ $s->surahAkhir?->nama_latin ?? 'S.'.$s->surah_akhir }} {{ $s->ayat_akhir }}</td>
                        <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s->jenis === 'ziyadah' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ ucfirst($s->jenis) }}</span></td>
                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $s->jumlah_halaman ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $sd[0] }} {{ $sd[1] }}">{{ $sd[2] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($setoran->hasPages())<div class="px-6 py-4 border-t border-gray-100">{{ $setoran->links() }}</div>@endif
    @endif
</div>
</div>

@push('scripts')
<script>
// Data surah — global, dibutuhkan surah-picker
const SURAH_DATA = @json($surahList->keyBy('id'));
const PAGE_START_U = {1:1,2:2,3:50,4:77,5:106,6:128,7:151,8:177,9:187,10:208,11:221,12:235,13:249,14:255,15:262,16:267,17:282,18:293,19:305,20:312,21:322,22:332,23:342,24:350,25:359,26:367,27:377,28:385,29:396,30:404,31:411,32:415,33:418,34:428,35:434,36:440,37:446,38:453,39:458,40:467,41:477,42:483,43:489,44:496,45:499,46:502,47:507,48:511,49:515,50:518,51:520,52:523,53:526,54:528,55:531,56:534,57:537,58:542,59:545,60:549,61:551,62:553,63:554,64:556,65:558,66:560,67:562,68:564,69:566,70:568,71:570,72:572,73:574,74:575,75:577,76:578,77:580,78:582,79:583,80:585,81:586,82:587,83:587,84:589,85:590,86:591,87:591,88:592,89:593,90:594,91:595,92:595,93:596,94:596,95:597,96:597,97:598,98:598,99:599,100:599,101:600,102:601,103:601,104:601,105:602,106:602,107:602,108:602,109:603,110:603,111:603,112:604,113:604,114:604};

function getPageU(surahId, ayat) {
    const sr = SURAH_DATA[surahId]; if (!sr) return 0;
    const ps = PAGE_START_U[parseInt(surahId)] || 0;
    const pn = parseInt(surahId) < 114 ? (PAGE_START_U[parseInt(surahId)+1] || ps+1) : 605;
    return Math.max(1, ps + Math.floor(((ayat-1)/sr.jumlah_ayat)*(pn-ps)));
}

function setoranUmumForm() {
    return {
        showForm: false,
        santriId: '', tanggal: '{{ now()->format('Y-m-d') }}',
        surahAwal: '', ayatAwal: '', surahAkhir: '', ayatAkhir: '',
        maxAyatAwal: 0, maxAyatAkhir: 0,
        jumlahHalaman: 0, jumlahJuz: 0, halamanAwal: 0, halamanAkhir: 0,
        lastInfo: '',
        apiUrl: '{{ route('ustadz.api.last-position') }}',

        // ── Picker state ──
        awalSearch: '', awalOpen: false, awalFiltered: [],
        awalFilter() {
            const all = Object.values(SURAH_DATA).sort((a,b)=>a.id-b.id);
            const q = this.awalSearch.toLowerCase().trim();
            this.awalFiltered = q ? all.filter(s=>s.nama_latin.toLowerCase().includes(q)||String(s.id).startsWith(q)) : all;
        },
        akhirSearch: '', akhirOpen: false, akhirFiltered: [],
        akhirFilter() {
            const all = Object.values(SURAH_DATA).sort((a,b)=>a.id-b.id);
            const q = this.akhirSearch.toLowerCase().trim();
            this.akhirFiltered = q ? all.filter(s=>s.nama_latin.toLowerCase().includes(q)||String(s.id).startsWith(q)) : all;
        },

        init() {
            const all = Object.values(SURAH_DATA).sort((a,b)=>a.id-b.id);
            this.awalFiltered = all; this.akhirFiltered = all;
            this.$watch('surahAwal',  v=>{ const s=SURAH_DATA[v]; this.awalSearch=s?s.id+'. '+s.nama_latin:''; });
            this.$watch('surahAkhir', v=>{ const s=SURAH_DATA[v]; this.akhirSearch=s?s.id+'. '+s.nama_latin:''; });
        },
        buildAyatAwal(id) { const s=SURAH_DATA[id]; this.maxAyatAwal=s?s.jumlah_ayat:0; },
        buildAyatAkhir(id) { const s=SURAH_DATA[id]; this.maxAyatAkhir=s?s.jumlah_ayat:0; },
        onSurahAwalChange() { this.buildAyatAwal(this.surahAwal); this.ayatAwal=1; this.surahAkhir=this.surahAwal; this.buildAyatAkhir(this.surahAkhir); this.hitungHalaman(); },
        onSurahAkhirChange() { this.buildAyatAkhir(this.surahAkhir); const s=SURAH_DATA[this.surahAkhir]; this.ayatAkhir=s?s.jumlah_ayat:''; this.hitungHalaman(); },
        hitungHalaman() {
            if (!this.surahAwal||!this.ayatAwal||!this.surahAkhir||!this.ayatAkhir) { this.jumlahHalaman=0; return; }
            const pA=getPageU(this.surahAwal,parseInt(this.ayatAwal)), pB=getPageU(this.surahAkhir,parseInt(this.ayatAkhir));
            this.halamanAwal=pA; this.halamanAkhir=pB;
            this.jumlahHalaman=Math.max(0,pB-pA+1);
            this.jumlahJuz=Math.round(this.jumlahHalaman/20*10)/10;
        },
        async loadLastPosition() {
            if (!this.santriId) { this.lastInfo=''; return; }
            try {
                const res=await fetch(`${this.apiUrl}?santri_id=${this.santriId}`);
                const data=await res.json();
                if (data.has_last) {
                    this.surahAwal=String(data.surah_awal); this.ayatAwal=data.ayat_awal;
                    this.surahAkhir=String(data.surah_awal);
                    this.buildAyatAwal(this.surahAwal); this.buildAyatAkhir(this.surahAkhir);
                    this.lastInfo=`${data.last_info} (${data.last_tanggal})`; this.hitungHalaman();
                } else { this.lastInfo=''; }
            } catch(e) { console.error(e); }
        }
    }
}
</script>
@endpush
@endsection
