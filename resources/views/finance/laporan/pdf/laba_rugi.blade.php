<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1f2937; }
    .kop { text-align: center; padding-bottom: 8px; border-bottom: 2px solid #1f2937; margin-bottom: 12px; }
    .kop h2 { margin: 0; font-size: 14px; }
    .kop p { margin: 2px 0; font-size: 9px; color: #555; }
    h3 { margin: 12px 0 6px; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { padding: 5px 8px; text-align: left; }
    .header { background: #f3f4f6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
    .group { background: #e0e7ff; font-weight: bold; }
    .group-r { background: #fee2e2; font-weight: bold; }
    .total { background: #dbeafe; font-weight: bold; font-size: 11px; }
    .total-r { background: #fecaca; font-weight: bold; font-size: 11px; }
    .laba { background: #4f46e5; color: white; font-weight: bold; font-size: 12px; }
    .right { text-align: right; }
    tr { page-break-inside: avoid; }
</style></head><body>
<div class="kop">
    <h2>{{ strtoupper($org['nama']) }}</h2>
    @if($org['alamat'])<p>{{ $org['alamat'] }}</p>@endif
    @if($org['telepon'] || $org['email'])<p>{{ $org['telepon'] }} {{ $org['email'] ? '· ' . $org['email'] : '' }}</p>@endif
</div>

<h3 style="text-align:center; margin-top: 4px;">LAPORAN LABA RUGI</h3>
<p style="text-align:center; margin-top: 0; font-size: 10px;">Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</p>

<table>
    <tr class="group"><td colspan="3">PENDAPATAN</td></tr>
    @foreach($data['pendapatan'] as $row)
    <tr>
        <td style="padding-left: 25px; font-family: monospace; color: #666; font-size: 9px;">{{ $row->kode }}</td>
        <td>{{ $row->nama }}</td>
        <td class="right">Rp {{ number_format($row->saldo) }}</td>
    </tr>
    @endforeach
    <tr class="total"><td colspan="2">TOTAL PENDAPATAN</td><td class="right">Rp {{ number_format($data['total_pendapatan']) }}</td></tr>

    <tr><td colspan="3" style="height: 8px;"></td></tr>

    <tr class="group-r"><td colspan="3">BEBAN</td></tr>
    @foreach($data['beban'] as $row)
    <tr>
        <td style="padding-left: 25px; font-family: monospace; color: #666; font-size: 9px;">{{ $row->kode }}</td>
        <td>{{ $row->nama }}</td>
        <td class="right">Rp {{ number_format($row->saldo) }}</td>
    </tr>
    @endforeach
    <tr class="total-r"><td colspan="2">TOTAL BEBAN</td><td class="right">Rp {{ number_format($data['total_beban']) }}</td></tr>

    <tr><td colspan="3" style="height: 8px;"></td></tr>

    <tr class="laba">
        <td colspan="2">{{ $data['laba_bersih'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
        <td class="right">Rp {{ number_format(abs($data['laba_bersih'])) }}</td>
    </tr>
</table>

<p style="margin-top: 30px; font-size: 8px; color: #777;">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} oleh {{ auth()->user()->name ?? '-' }}</p>
</body></html>
