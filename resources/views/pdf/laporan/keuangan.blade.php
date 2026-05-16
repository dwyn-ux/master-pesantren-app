@extends('pdf.layout')

@section('content')
<div class="summary-grid">
    <div class="summary-card">
        <div class="label">Total Tagihan</div>
        <div class="value">Rp {{ number_format($totalTagihan) }}</div>
        <div class="muted">{{ $jumlahTagihan }} tagihan</div>
    </div>
    <div class="summary-card" style="background:#ecfccb;border-color:#a3e635;">
        <div class="label" style="color:#3f6212;">Sudah Terbayar</div>
        <div class="value" style="color:#3f6212;">Rp {{ number_format($totalTagihan - $belumBayar) }}</div>
        <div class="muted">{{ $analysis['persen_terbayar'] }}%</div>
    </div>
    <div class="summary-card" style="background:#fef3c7;border-color:#fbbf24;">
        <div class="label" style="color:#92400e;">Belum Bayar</div>
        <div class="value" style="color:#92400e;">Rp {{ number_format($belumBayar) }}</div>
        <div class="muted">{{ $analysis['persen_belum'] }}%</div>
    </div>
    <div class="summary-card" style="background:#dbeafe;border-color:#60a5fa;">
        <div class="label" style="color:#1e40af;">Pembayaran Masuk</div>
        <div class="value" style="color:#1e40af;">Rp {{ number_format($totalPembayaran) }}</div>
        <div class="muted">{{ $jumlahPembayaran }} transaksi</div>
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

<h2>Tagihan per Jenis</h2>
<table>
    <thead>
        <tr>
            <th>Jenis Tagihan</th>
            <th class="text-right">Jumlah</th>
            <th class="text-right">Total Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tagihanByJenis as $row)
            <tr>
                <td>{{ $row->jenisTagihan?->nama ?? '—' }}</td>
                <td class="text-right">{{ $row->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($row->total) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center muted">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Pembayaran Terbaru</h2>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Santri / Wali</th>
            <th>Metode</th>
            <th>Status</th>
            <th class="text-right">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($recentPayments as $p)
            <tr>
                <td>{{ $p->paid_at?->format('d M Y H:i') ?? $p->created_at->format('d M Y H:i') }}</td>
                <td>
                    {{ $p->tagihan?->santri?->nama ?? $p->wali?->nama ?? '—' }}
                    @if($p->wali)
                        <div class="muted" style="font-size:9px;">Wali: {{ $p->wali->nama }}</div>
                    @endif
                </td>
                <td>{{ $p->metode ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ $p->status }}
                    </span>
                </td>
                <td class="text-right">Rp {{ number_format($p->nominal) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center muted">Belum ada pembayaran pada periode ini.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
