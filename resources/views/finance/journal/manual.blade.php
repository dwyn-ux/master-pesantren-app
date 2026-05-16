@extends('layouts.app')
@section('title', 'Jurnal Manual')
@section('page-title', 'Buat Jurnal Manual / Penyesuaian')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="POST" action="{{ route('finance.journal.store-manual') }}" class="max-w-5xl" x-data="manualJournal()">
    @csrf

    <div class="glass-panel rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Keterangan *</label>
                <input type="text" name="keterangan" required placeholder="Contoh: Penyesuaian saldo per akhir bulan" class="w-full rounded-lg border-gray-300">
            </div>
        </div>

        <table class="w-full text-sm border">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="px-2 py-2 text-left">Akun</th>
                    <th class="px-2 py-2 text-right">Debit</th>
                    <th class="px-2 py-2 text-right">Kredit</th>
                    <th class="px-2 py-2 text-left">Keterangan</th>
                    <th class="px-2 py-2"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(line, idx) in lines" :key="idx">
                    <tr class="border-b">
                        <td class="px-2 py-1">
                            <select :name="`lines[${idx}][account_id]`" required class="w-full rounded border-gray-300 text-xs">
                                <option value="">— Pilih akun —</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->kode }} - {{ $acc->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" step="0.01" min="0" :name="`lines[${idx}][debit]`" x-model="line.debit" @input="onDebit(idx)" class="w-full rounded border-gray-300 text-xs text-right">
                        </td>
                        <td class="px-2 py-1">
                            <input type="number" step="0.01" min="0" :name="`lines[${idx}][kredit]`" x-model="line.kredit" @input="onKredit(idx)" class="w-full rounded border-gray-300 text-xs text-right">
                        </td>
                        <td class="px-2 py-1">
                            <input type="text" :name="`lines[${idx}][keterangan]`" class="w-full rounded border-gray-300 text-xs">
                        </td>
                        <td class="px-2 py-1 text-center">
                            <button type="button" @click="removeLine(idx)" class="text-red-600 text-xs"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td class="px-2 py-2 text-right">TOTAL</td>
                    <td class="px-2 py-2 text-right" x-text="'Rp ' + Number(totalDebit).toLocaleString('id-ID')"></td>
                    <td class="px-2 py-2 text-right" x-text="'Rp ' + Number(totalKredit).toLocaleString('id-ID')"></td>
                    <td colspan="2" class="px-2 py-2">
                        <span x-show="balanced" class="text-green-600">✓ Balance</span>
                        <span x-show="!balanced" class="text-red-600">✗ Tidak Balance</span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <button type="button" @click="addLine()" class="mt-3 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
            <i class="fa-solid fa-plus mr-1"></i> Tambah Baris
        </button>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold" :disabled="!balanced || totalDebit <= 0">
            <i class="fa-solid fa-check mr-1"></i> Posting Jurnal
        </button>
        <a href="{{ route('finance.laporan.jurnal') }}" class="px-6 py-3 bg-gray-200 rounded-lg font-bold">Batal</a>
    </div>

    @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded text-sm">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
</form>

<script>
function manualJournal() {
    return {
        lines: [
            { account_id: '', debit: 0, kredit: 0, keterangan: '' },
            { account_id: '', debit: 0, kredit: 0, keterangan: '' },
        ],
        get totalDebit()  { return this.lines.reduce((s, l) => s + (Number(l.debit) || 0), 0); },
        get totalKredit() { return this.lines.reduce((s, l) => s + (Number(l.kredit) || 0), 0); },
        get balanced()    { return Math.abs(this.totalDebit - this.totalKredit) < 0.01; },
        addLine() { this.lines.push({ account_id: '', debit: 0, kredit: 0, keterangan: '' }); },
        removeLine(idx) { if (this.lines.length > 2) this.lines.splice(idx, 1); },
        onDebit(idx)  { if (this.lines[idx].debit  > 0) this.lines[idx].kredit = 0; },
        onKredit(idx) { if (this.lines[idx].kredit > 0) this.lines[idx].debit  = 0; },
    };
}
</script>
@endsection
