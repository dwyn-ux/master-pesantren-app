@extends('pdf.layout')

@section('content')
<div class="summary-grid">
    <div class="summary-card">
        <div class="label">Total Order</div>
        <div class="value">{{ number_format($totalOrder) }}</div>
    </div>
    <div class="summary-card" style="background:#ecfccb;border-color:#a3e635;">
        <div class="label" style="color:#3f6212;">Total Pendapatan</div>
        <div class="value" style="color:#3f6212;">Rp {{ number_format($totalRevenue) }}</div>
    </div>
    <div class="summary-card" style="background:#dbeafe;border-color:#60a5fa;">
        <div class="label" style="color:#1e40af;">Total Cucian</div>
        <div class="value" style="color:#1e40af;">{{ number_format($totalKg, 2) }} kg</div>
        <div class="muted">Ø {{ $avgKg }} kg/order</div>
    </div>
    <div class="summary-card" style="background:#fef3c7;border-color:#fbbf24;">
        <div class="label" style="color:#92400e;">Santri Aktif</div>
        <div class="value" style="color:#92400e;">{{ $santriUnik }}</div>
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

<h2>Status Breakdown</h2>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th class="text-right">Jumlah Order</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($statusBreakdown as $s)
            <tr>
                <td>
                    <span class="badge badge-{{ $s->status === 'diambil' ? 'success' : ($s->status === 'selesai' ? 'info' : 'warning') }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
                <td class="text-right">{{ $s->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($s->total) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center muted">Belum ada data.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Top 10 Pelanggan</h2>
<table>
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th>Nama Santri</th>
            <th class="text-right">Order</th>
            <th class="text-right">Total Cucian</th>
            <th class="text-right">Total Belanja</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topPelanggan as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->santri?->nama ?? '—' }}</td>
                <td class="text-right">{{ $t->jumlah }}</td>
                <td class="text-right">{{ number_format($t->total_kg, 2) }} kg</td>
                <td class="text-right">Rp {{ number_format($t->total_belanja) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center muted">Belum ada pelanggan.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>100 Order Terbaru</h2>
<table>
    <thead>
        <tr>
            <th>Tiket</th>
            <th>Tanggal</th>
            <th>Santri</th>
            <th class="text-right">Berat (kg)</th>
            <th class="text-right">Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $o)
            <tr>
                <td>{{ $o->nomor_tiket }}</td>
                <td>{{ $o->created_at->format('d/m/Y') }}</td>
                <td>{{ $o->santri?->nama ?? '—' }}</td>
                <td class="text-right">{{ number_format($o->berat_kg, 2) }}</td>
                <td class="text-right">Rp {{ number_format($o->total) }}</td>
                <td>{{ ucfirst($o->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
