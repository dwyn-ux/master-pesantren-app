<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1f2937; }
    .kop { text-align: center; padding-bottom: 8px; border-bottom: 2px solid #1f2937; margin-bottom: 12px; }
    .kop h2 { margin: 0; font-size: 14px; }
    h3 { margin: 12px 0 6px; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { padding: 4px 8px; }
    .group { background: #e0e7ff; font-weight: bold; padding: 5px 8px; }
    .group-y { background: #fef3c7; font-weight: bold; padding: 5px 8px; }
    .total { background: #dbeafe; font-weight: bold; }
    .total-y { background: #fde68a; font-weight: bold; }
    .right { text-align: right; }
    tr { page-break-inside: avoid; }
    .col-left { width: 49%; vertical-align: top; padding-right: 5px; }
    .col-right { width: 49%; vertical-align: top; padding-left: 5px; }
</style></head><body>
<div class="kop">
    <h2>{{ strtoupper($org['nama']) }}</h2>
    @if($org['alamat'])<p style="font-size:9px; margin:2px;">{{ $org['alamat'] }}</p>@endif
</div>

<h3 style="text-align:center;">LAPORAN POSISI KEUANGAN (NERACA)</h3>
<p style="text-align:center; font-size:10px; margin-top:0;">Per {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</p>

<table>
    <tr>
        <td class="col-left">
            <table>
                <tr class="group"><td colspan="3">AKTIVA</td></tr>
                @foreach($data['aset'] as $row)
                <tr><td style="font-family:monospace; font-size:9px; padding-left:15px;">{{ $row->kode }}</td><td>{{ $row->nama }}</td><td class="right">Rp {{ number_format($row->saldo) }}</td></tr>
                @endforeach
                <tr class="total"><td colspan="2">TOTAL AKTIVA</td><td class="right">Rp {{ number_format($data['total_aset']) }}</td></tr>
            </table>
        </td>
        <td class="col-right">
            <table>
                <tr class="group-y"><td colspan="3">PASIVA</td></tr>
                <tr><td colspan="3" style="font-size:9px; font-style:italic; padding-left:5px;">Liabilitas</td></tr>
                @forelse($data['liabilitas'] as $row)
                <tr><td style="font-family:monospace; font-size:9px; padding-left:15px;">{{ $row->kode }}</td><td>{{ $row->nama }}</td><td class="right">Rp {{ number_format($row->saldo) }}</td></tr>
                @empty<tr><td colspan="3" style="padding-left:15px; color:#999; font-size:9px;">— kosong —</td></tr>@endforelse

                <tr><td colspan="3" style="font-size:9px; font-style:italic; padding-left:5px; padding-top:5px;">Ekuitas</td></tr>
                @forelse($data['ekuitas'] as $row)
                <tr><td style="font-family:monospace; font-size:9px; padding-left:15px;">{{ $row->kode }}</td><td>{{ $row->nama }}</td><td class="right">Rp {{ number_format($row->saldo) }}</td></tr>
                @empty<tr><td colspan="3" style="padding-left:15px; color:#999; font-size:9px;">— kosong —</td></tr>@endforelse
                <tr><td colspan="2" style="padding-left:15px; font-style:italic;">Laba Tahun Berjalan</td><td class="right">Rp {{ number_format($data['laba_berjalan']) }}</td></tr>

                <tr class="total-y"><td colspan="2">TOTAL PASIVA</td><td class="right">Rp {{ number_format($data['total_liabilitas'] + $data['total_ekuitas'] + $data['laba_berjalan']) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin-top:20px; font-size:8px; color:#777;">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} oleh {{ auth()->user()->name ?? '-' }}</p>
</body></html>
