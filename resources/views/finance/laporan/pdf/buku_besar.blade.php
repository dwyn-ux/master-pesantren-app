<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', sans-serif; font-size: 9px; }
    .kop { text-align: center; padding-bottom: 8px; border-bottom: 2px solid #1f2937; margin-bottom: 12px; }
    .kop h2 { margin: 0; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    td, th { padding: 3px 5px; border-bottom: 1px solid #eee; }
    .header { background: #f3f4f6; font-weight: bold; font-size: 8px; text-transform: uppercase; }
    .right { text-align: right; }
    .saldo-awal { background: #f3f4f6; font-style: italic; }
    .saldo-akhir { background: #4f46e5; color: white; font-weight: bold; }
</style></head><body>
<div class="kop"><h2>{{ strtoupper($org['nama']) }}</h2></div>
<h3 style="text-align:center;">BUKU BESAR - {{ $data['account']->kode }} {{ $data['account']->nama }}</h3>
<p style="text-align:center;font-size:9px;margin:0">Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</p>

<table>
    <tr class="header">
        <td>Tanggal</td>
        <td>No. Jurnal</td>
        <td>Keterangan</td>
        <td class="right">Debit</td>
        <td class="right">Kredit</td>
        <td class="right">Saldo</td>
    </tr>
    <tr class="saldo-awal">
        <td colspan="5">Saldo Awal</td>
        <td class="right">Rp {{ number_format($data['saldo_awal']) }}</td>
    </tr>
    @foreach($data['items'] as $row)
    <tr>
        <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
        <td style="font-family:monospace; font-size:8px;">{{ $row['nomor'] }}</td>
        <td>{{ $row['keterangan'] }}</td>
        <td class="right">{{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit']) : '' }}</td>
        <td class="right">{{ $row['kredit'] > 0 ? 'Rp ' . number_format($row['kredit']) : '' }}</td>
        <td class="right">Rp {{ number_format($row['saldo']) }}</td>
    </tr>
    @endforeach
    <tr class="saldo-akhir">
        <td colspan="5">SALDO AKHIR</td>
        <td class="right">Rp {{ number_format($data['saldo_akhir']) }}</td>
    </tr>
</table>
<p style="margin-top:15px;font-size:8px;color:#777">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
</body></html>
