<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport - {{ $santri->nama }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 14px;
            margin: 4px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 4px 8px;
            font-size: 11px;
        }
        .info td.label {
            font-weight: bold;
            width: 30%;
        }
        h3 {
            font-size: 13px;
            margin: 15px 0 8px 0;
            padding: 5px;
            background: #f0f0f0;
            border-left: 4px solid #4f46e5;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data th, table.data td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        table.data th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .signature {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-row {
            display: table-row;
        }
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }
        .signature-cell .nama {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            display: inline-block;
            padding: 0 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Pesantren') }}</h1>
        <h2>RAPORT SEMESTER {{ strtoupper($tahunAjaran->semester) }} {{ $tahunAjaran->nama }}</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td class="label">Nama Santri</td>
                <td>{{ $santri->nama }}</td>
                <td class="label">Kelas</td>
                <td>{{ $santri->kelasSantri->firstWhere('tahun_ajaran_id', $tahunAjaran->id)?->kelas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td>{{ $santri->nis ?? '-' }}</td>
                <td class="label">Tanggal Lahir</td>
                <td>{{ $santri->tanggal_lahir ? \Carbon\Carbon::parse($santri->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Wali</td>
                <td colspan="3">{{ $santri->wali->first()?->nama ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <h3>NILAI MATA PELAJARAN DINIYAH</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Mata Pelajaran</th>
                <th style="width: 80px; text-align: center;">Nilai</th>
                <th style="width: 60px; text-align: center;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiAkhir as $no => $n)
            <tr>
                <td style="text-align: center;">{{ $no + 1 }}</td>
                <td>{{ $n->mataPelajaran->nama }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($n->nilai_akhir, 2) }}</td>
                <td style="text-align: center; font-weight: bold; color: {{ in_array($n->predikat, ['A', 'B']) ? 'green' : 'red' }};">{{ $n->predikat }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Belum ada nilai.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($nilaiSikap->isNotEmpty())
    <h3>PENILAIAN SIKAP & AKHLAK</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Aspek</th>
                <th style="width: 60px; text-align: center;">Predikat</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiSikap as $sikap)
            <tr>
                <td>{{ $sikap->aspek }}</td>
                <td style="text-align: center;">{{ $sikap->predikat }}</td>
                <td>{{ $sikap->deskripsi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($absensi->isNotEmpty())
    <h3>KEHADIRAN</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Status</th>
                <th style="text-align: center;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $a)
            <tr>
                <td>{{ ucfirst($a->status) }}</td>
                <td style="text-align: center;">{{ $a->jumlah }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($catatanWaliKelas)
    <h3>CATATAN WALI KELAS</h3>
    <p style="padding: 10px; background: #f9f9f9; border-left: 4px solid #888;">
        {{ $catatanWaliKelas->catatan ?? '-' }}
    </p>
    @endif

    <div class="signature">
        <div class="signature-row">
            <div class="signature-cell">
                <div>Mengetahui,</div>
                <div>Wali Santri</div>
                <div class="nama">{{ $santri->wali->first()?->nama ?? '_______________' }}</div>
            </div>
            <div class="signature-cell">
                <div>{{ now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div>Wali Kelas</div>
                <div class="nama">{{ $catatanWaliKelas->ustadz->user->name ?? '_______________' }}</div>
            </div>
            <div class="signature-cell">
                <div>{{ now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div>Kepala Madrasah</div>
                <div class="nama">_______________</div>
            </div>
        </div>
    </div>
</body>
</html>
