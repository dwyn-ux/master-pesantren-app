@extends('pdf.layout')

@section('content')
<div class="summary-grid">
    <div class="summary-card">
        <div class="label">Total Setoran</div>
        <div class="value">{{ number_format($totalSetoran) }}</div>
    </div>
    <div class="summary-card" style="background:#ecfccb;border-color:#a3e635;">
        <div class="label" style="color:#3f6212;">Halaman Ziyadah</div>
        <div class="value" style="color:#3f6212;">{{ $totalHalamanZiyadah }}</div>
        <div class="muted">± {{ $analysis['juz'] }} juz</div>
    </div>
    <div class="summary-card" style="background:#fef3c7;border-color:#fbbf24;">
        <div class="label" style="color:#92400e;">Halaman Murojaah</div>
        <div class="value" style="color:#92400e;">{{ $totalHalamanMurojaah }}</div>
    </div>
    <div class="summary-card" style="background:#dbeafe;border-color:#60a5fa;">
        <div class="label" style="color:#1e40af;">Santri Aktif</div>
        <div class="value" style="color:#1e40af;">{{ $santriUnik }}</div>
        <div class="muted">Ø {{ $analysis['rata_per_santri'] }} hal/santri</div>
    </div>
</div>

<div class="analysis">
    <strong>Analisis</strong>
    <ul>
        @foreach($analysis['insights'] as $insight)
            <li>{{ $insight }}</li>
        @endforeach
    </ul>
</div>

<h2>Capaian per Halaqah</h2>
<table>
    <thead>
        <tr>
            <th>Halaqah</th>
            <th>Pengampu</th>
            <th class="text-right">Jumlah Santri</th>
            <th class="text-right">Setoran</th>
            <th class="text-right">Halaman Ziyadah</th>
        </tr>
    </thead>
    <tbody>
        @forelse($perHalaqah as $h)
            <tr>
                <td>{{ $h->nama }}</td>
                <td>{{ $h->ustadz }}</td>
                <td class="text-right">{{ $h->jumlah_santri }}</td>
                <td class="text-right">{{ $h->jumlah_setoran }}</td>
                <td class="text-right">{{ $h->total_halaman }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center muted">Belum ada halaqah terdaftar.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Top 20 Santri dengan Hafalan Terbanyak</h2>
<table>
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th>Nama Santri</th>
            <th>NIS</th>
            <th class="text-right">Jumlah Setoran</th>
            <th class="text-right">Halaman</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topHafalan as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->santri?->nama ?? '—' }}</td>
                <td>{{ $s->santri?->nis ?? '—' }}</td>
                <td class="text-right">{{ $s->total_setoran }}</td>
                <td class="text-right">{{ $s->total_halaman }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center muted">Belum ada data hafalan pada periode ini.</td></tr>
        @endforelse
    </tbody>
</table>

@if(isset($detailSetoran) && $detailSetoran->count())
<h2>Rekap Setoran per Santri (Posisi Hafalan)</h2>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Santri</th>
            <th>Jenis</th>
            <th>Dari</th>
            <th>Sampai</th>
            <th class="text-right">Hlm</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detailSetoran as $s)
            <tr>
                <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $s->santri?->nama ?? '—' }}</td>
                <td>{{ ucfirst($s->jenis) }}</td>
                <td style="white-space:nowrap;">
                    @if($s->surahAwal)
                        {{ $s->surahAwal->nama_latin }} ({{ $s->surahAwal->id }}){{ $s->ayat_awal ? ': '.$s->ayat_awal : '' }}
                    @else —
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    @if($s->surahAkhir)
                        {{ $s->surahAkhir->nama_latin }} ({{ $s->surahAkhir->id }}){{ $s->ayat_akhir ? ': '.$s->ayat_akhir : '' }}
                    @else —
                    @endif
                </td>
                <td class="text-right">{{ $s->jumlah_halaman }}</td>
                <td>{{ ucfirst($s->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
