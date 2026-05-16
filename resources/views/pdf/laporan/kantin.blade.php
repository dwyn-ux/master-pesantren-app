@extends('pdf.layout')

@section('content')
<div class="summary-grid">
    <div class="summary-card">
        <div class="label">Total Transaksi</div>
        <div class="value">{{ number_format($totalTransaksi) }}</div>
    </div>
    <div class="summary-card" style="background:#ecfccb;border-color:#a3e635;">
        <div class="label" style="color:#3f6212;">Total Pendapatan</div>
        <div class="value" style="color:#3f6212;">Rp {{ number_format($totalRevenue) }}</div>
    </div>
    <div class="summary-card" style="background:#dbeafe;border-color:#60a5fa;">
        <div class="label" style="color:#1e40af;">Santri Aktif</div>
        <div class="value" style="color:#1e40af;">{{ $santriUnik }}</div>
        <div class="muted">Ø {{ $analysis['frekuensi_per_santri'] }} trx/santri</div>
    </div>
    <div class="summary-card" style="background:#fef3c7;border-color:#fbbf24;">
        <div class="label" style="color:#92400e;">Rata-rata / Transaksi</div>
        <div class="value" style="color:#92400e;">Rp {{ number_format($avgTransaksi) }}</div>
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

<h2>Breakdown per Outlet</h2>
<table>
    <thead>
        <tr>
            <th>Outlet</th>
            <th class="text-right">Jumlah Transaksi</th>
            <th class="text-right">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($perOutlet as $o)
            <tr>
                <td>{{ $o->nama }}</td>
                <td class="text-right">{{ $o->transaksi }}</td>
                <td class="text-right">Rp {{ number_format($o->revenue) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center muted">Belum ada outlet kantin.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Top 10 Pembeli</h2>
<table>
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th>Nama Santri</th>
            <th class="text-right">Frekuensi</th>
            <th class="text-right">Total Belanja</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topPembeli as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->santri?->nama ?? '—' }}</td>
                <td class="text-right">{{ $t->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($t->total_belanja) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center muted">Belum ada pembeli.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>100 Transaksi Terbaru</h2>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Santri</th>
            <th>Outlet</th>
            <th class="text-right">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaksi as $t)
            <tr>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->santri?->nama ?? '—' }}</td>
                <td>{{ $t->outlet?->nama ?? '—' }}</td>
                <td class="text-right">Rp {{ number_format($t->total) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
