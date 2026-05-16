<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
    .kop { text-align: center; padding-bottom: 8px; border-bottom: 2px solid #1f2937; margin-bottom: 12px; }
    .kop h2 { margin: 0; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { padding: 4px 8px; }
    .header { background: #f3f4f6; font-weight: bold; font-size: 9px; text-transform: uppercase; }
    .right { text-align: right; }
    .group-g { background: #d1fae5; font-weight: bold; }
    .group-r { background: #fee2e2; font-weight: bold; }
    .total { background: #4f46e5; color: white; font-weight: bold; }
</style></head><body>
<div class="kop">
    <h2>{{ strtoupper($org['nama']) }}</h2>
</div>
<h3 style="text-align:center;">LAPORAN ARUS KAS</h3>
<p style="text-align:center;font-size:10px;margin:0">Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</p>

<h3>Saldo Per Kas/Bank</h3>
<table>
    <tr class="header">
        <td>Kas/Bank</td>
        <td class="right">Saldo Awal</td>
        <td class="right">Mutasi</td>
        <td class="right">Saldo Akhir</td>
    </tr>
    @foreach($data['per_kas'] as $row)
    <tr>
        <td>{{ $row['kas_bank']->nama }}</td>
        <td class="right">Rp {{ number_format($row['saldo_awal']) }}</td>
        <td class="right">Rp {{ number_format($row['mutasi']) }}</td>
        <td class="right">Rp {{ number_format($row['saldo_akhir']) }}</td>
    </tr>
    @endforeach
    <tr class="total">
        <td>TOTAL</td>
        <td class="right">Rp {{ number_format($data['saldo_awal_total']) }}</td>
        <td class="right">Rp {{ number_format($data['kas_bersih']) }}</td>
        <td class="right">Rp {{ number_format($data['saldo_akhir_total']) }}</td>
    </tr>
</table>

<table style="width:100%; margin-top: 12px;">
<tr>
    <td style="vertical-align:top; width:49%; padding-right: 5px;">
        <h3>Pemasukan Per Kategori</h3>
        <table>
            <tr class="group-g"><td colspan="2">PEMASUKAN</td></tr>
            @foreach($data['masuk_per_kategori'] as $r)
            <tr><td>{{ $r->nama }}</td><td class="right">Rp {{ number_format($r->total) }}</td></tr>
            @endforeach
            <tr class="group-g"><td>TOTAL</td><td class="right">Rp {{ number_format($data['total_masuk']) }}</td></tr>
        </table>
    </td>
    <td style="vertical-align:top; width:49%; padding-left: 5px;">
        <h3>Pengeluaran Per Kategori</h3>
        <table>
            <tr class="group-r"><td colspan="2">PENGELUARAN</td></tr>
            @foreach($data['keluar_per_kategori'] as $r)
            <tr><td>{{ $r->nama }}</td><td class="right">Rp {{ number_format($r->total) }}</td></tr>
            @endforeach
            <tr class="group-r"><td>TOTAL</td><td class="right">Rp {{ number_format($data['total_keluar']) }}</td></tr>
        </table>
    </td>
</tr>
</table>

<p style="margin-top:20px;font-size:8px;color:#777">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
</body></html>
