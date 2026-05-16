<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>Slip Gaji {{ $slip->ustadz->nama }}</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; max-width: 700px; margin: auto; }
    h1, h2, h3 { margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    td, th { padding: 6px 10px; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
    .info { display: flex; justify-content: space-between; margin: 15px 0; }
    .label { color: #666; font-size: 11px; }
    .value { font-weight: bold; }
    .total-row { background: #f3f4f6; font-weight: bold; }
    .net-row { background: #4f46e5; color: white; font-weight: bold; font-size: 14px; }
    .signature { margin-top: 60px; display: flex; justify-content: space-around; }
    @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head><body>
<button onclick="window.print()" class="no-print" style="float:right; padding: 8px 16px; background: #4f46e5; color: white; border: 0; border-radius: 6px; cursor: pointer;">Print</button>

<div class="header">
    <h2>SLIP GAJI</h2>
    <p>Periode: {{ \Carbon\Carbon::create($slip->payrollRun->tahun, $slip->payrollRun->bulan)->translatedFormat('F Y') }}</p>
    <p style="font-size:10px; color:#666">No: {{ $slip->payrollRun->nomor }}-{{ str_pad($slip->id, 4, '0', STR_PAD_LEFT) }}</p>
</div>

<div class="info">
    <div>
        <div class="label">Nama</div>
        <div class="value">{{ $slip->ustadz->nama }}</div>
    </div>
    <div>
        <div class="label">NIK</div>
        <div class="value">{{ $slip->ustadz->nik ?: '-' }}</div>
    </div>
    <div>
        <div class="label">Tanggal Bayar</div>
        <div class="value">{{ $slip->tanggal_bayar?->format('d/m/Y') ?: '-' }}</div>
    </div>
</div>

<table>
    <tr style="background:#e0e7ff"><td colspan="2"><strong>PENDAPATAN</strong></td></tr>
    <tr><td>Gaji Pokok</td><td style="text-align:right">Rp {{ number_format($slip->gaji_pokok) }}</td></tr>
    <tr><td>Tunjangan Jabatan</td><td style="text-align:right">Rp {{ number_format($slip->tunjangan_jabatan) }}</td></tr>
    <tr><td>Tunjangan Transport</td><td style="text-align:right">Rp {{ number_format($slip->tunjangan_transport) }}</td></tr>
    <tr><td>Tunjangan Makan</td><td style="text-align:right">Rp {{ number_format($slip->tunjangan_makan) }}</td></tr>
    <tr><td>Tunjangan Lain</td><td style="text-align:right">Rp {{ number_format($slip->tunjangan_lain) }}</td></tr>
    @if($slip->bonus > 0)<tr><td>Bonus</td><td style="text-align:right">Rp {{ number_format($slip->bonus) }}</td></tr>@endif
    <tr class="total-row"><td>TOTAL PENDAPATAN (GROSS)</td><td style="text-align:right">Rp {{ number_format($slip->gross) }}</td></tr>

    <tr style="background:#fee2e2"><td colspan="2"><strong>POTONGAN</strong></td></tr>
    @if($slip->potongan_absensi > 0)<tr><td>Potongan Absensi</td><td style="text-align:right">Rp {{ number_format($slip->potongan_absensi) }}</td></tr>@endif
    @if($slip->potongan_pph21 > 0)<tr><td>PPh 21</td><td style="text-align:right">Rp {{ number_format($slip->potongan_pph21) }}</td></tr>@endif
    @if($slip->potongan_lain > 0)<tr><td>Potongan Lain</td><td style="text-align:right">Rp {{ number_format($slip->potongan_lain) }}</td></tr>@endif
    <tr class="total-row"><td>TOTAL POTONGAN</td><td style="text-align:right">Rp {{ number_format($slip->potongan_absensi + $slip->potongan_pph21 + $slip->potongan_lain) }}</td></tr>

    <tr class="net-row"><td>TAKE HOME PAY</td><td style="text-align:right">Rp {{ number_format($slip->net) }}</td></tr>
</table>

@if($slip->catatan)
<p style="margin-top:15px; font-size:11px; padding:8px; background:#fffbeb; border-left:3px solid #f59e0b">
    <strong>Catatan:</strong> {{ $slip->catatan }}
</p>
@endif

<div class="signature">
    <div style="text-align:center">
        <p>Bendahara</p>
        <p style="margin-top:60px; border-top:1px solid #333; padding-top:5px"><strong>(.......................)</strong></p>
    </div>
    <div style="text-align:center">
        <p>Penerima</p>
        <p style="margin-top:60px; border-top:1px solid #333; padding-top:5px"><strong>{{ $slip->ustadz->nama }}</strong></p>
    </div>
</div>
</body></html>
