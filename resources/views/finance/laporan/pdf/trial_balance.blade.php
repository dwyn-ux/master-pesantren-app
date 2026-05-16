<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
    .kop { text-align: center; padding-bottom: 8px; border-bottom: 2px solid #1f2937; margin-bottom: 12px; }
    .kop h2 { margin: 0; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    td, th { padding: 3px 6px; }
    .header { background: #f3f4f6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
    .right { text-align: right; }
    .total { background: #4f46e5; color: white; font-weight: bold; }
</style></head><body>
<div class="kop"><h2>{{ strtoupper($org['nama']) }}</h2></div>
<h3 style="text-align:center;">NERACA SALDO (TRIAL BALANCE)</h3>
<p style="text-align:center;font-size:10px;margin:0">Per {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</p>

<table>
    <tr class="header">
        <td>Kode</td>
        <td>Nama Akun</td>
        <td class="right">Total Debit</td>
        <td class="right">Total Kredit</td>
        <td class="right">Saldo</td>
    </tr>
    @foreach($data['rows'] as $row)
    <tr>
        <td style="font-family:monospace; font-size:9px;">{{ $row['account']->kode }}</td>
        <td>{{ $row['account']->nama }}</td>
        <td class="right">Rp {{ number_format($row['debit']) }}</td>
        <td class="right">Rp {{ number_format($row['kredit']) }}</td>
        <td class="right" style="font-weight:bold">Rp {{ number_format($row['saldo']) }}</td>
    </tr>
    @endforeach
    <tr class="total">
        <td colspan="2">TOTAL</td>
        <td class="right">Rp {{ number_format($data['total_debit']) }}</td>
        <td class="right">Rp {{ number_format($data['total_kredit']) }}</td>
        <td class="right">{{ abs($data['total_debit'] - $data['total_kredit']) < 0.01 ? '✓ BALANCE' : '✗ SELISIH' }}</td>
    </tr>
</table>
<p style="margin-top:15px;font-size:8px;color:#777">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
</body></html>
