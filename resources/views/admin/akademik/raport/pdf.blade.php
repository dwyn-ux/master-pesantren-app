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

        /* ── KOP ───────────────────────────────────────────── */
        .kop {
            border-bottom: 4px double #1e3a8a;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 90px; vertical-align: middle; text-align: center; padding-right: 12px; }
        .kop-logo img { max-width: 80px; max-height: 80px; }
        .kop-title { vertical-align: middle; text-align: center; }
        .kop-id {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .kop-alamat {
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            margin-top: 2px;
        }

        /* ── Title raport ──────────────────────────────────── */
        .title-raport {
            text-align: center;
            margin: 14px 0 18px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1e3a8a;
        }

        /* ── Info santri ───────────────────────────────────── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table td { padding: 4px 6px; font-size: 11px; vertical-align: top; }
        .info-table td.label { font-weight: bold; width: 22%; }
        .info-table td.colon { width: 2%; }

        /* ── Section ───────────────────────────────────────── */
        h3 {
            font-size: 12.5px;
            margin: 16px 0 8px 0;
            padding: 6px 10px;
            background: #eef2ff;
            border-left: 4px solid #4f46e5;
            color: #1e3a8a;
            font-weight: bold;
        }

        /* ── Tables ────────────────────────────────────────── */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data th, table.data td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 10.5px;
        }
        table.data th {
            background: #f1f5f9;
            font-weight: bold;
            color: #1e3a8a;
        }
        table.data td.center { text-align: center; }
        table.data td.bold { font-weight: bold; }
        .predikat-A { color: #047857; font-weight: bold; }
        .predikat-B { color: #2563eb; font-weight: bold; }
        .predikat-C { color: #d97706; font-weight: bold; }
        .predikat-D { color: #dc2626; font-weight: bold; }
        .predikat-E { color: #991b1b; font-weight: bold; }

        /* ── Catatan ───────────────────────────────────────── */
        .catatan {
            padding: 10px 12px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            font-size: 11px;
            margin-bottom: 12px;
        }

        /* ── Tanda tangan ──────────────────────────────────── */
        .ttd {
            margin-top: 32px;
            display: table;
            width: 100%;
        }
        .ttd-row { display: table-row; }
        .ttd-cell {
            display: table-cell;
            width: 33.33%;
            padding: 8px;
            text-align: center;
            vertical-align: top;
            font-size: 10.5px;
        }
        .ttd-cell .role { font-weight: 600; }
        .ttd-cell .nama {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            display: inline-block;
            padding: 0 14px 1px;
            font-weight: bold;
        }
        .ttd-cell .placeholder-tanggal {
            visibility: hidden;
        }
    </style>
</head>
<body>

    {{-- ═══ KOP ═══════════════════════════════════════════════════ --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @php
                        $logoPath = !empty($settings['logo_url'])
                            ? public_path(ltrim($settings['logo_url'], '/'))
                            : public_path('logo-nobg.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="logo">
                    @endif
                </td>
                <td class="kop-title">
                    <div class="kop-id">{{ $settings['nama_pesantren'] ?: config('app.name', 'Pesantren') }}</div>
                    @if(!empty($settings['alamat']))
                        <div class="kop-alamat">{{ $settings['alamat'] }}</div>
                    @endif
                    @if(!empty($settings['kontak']))
                        <div class="kop-alamat">{{ $settings['kontak'] }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══ Title Raport ══════════════════════════════════════════ --}}
    <div class="title-raport">RAPORT SEMESTER {{ strtoupper($tahunAjaran->semester) }} TAHUN AJARAN {{ $tahunAjaran->nama }}</div>

    {{-- ═══ Info Santri ═══════════════════════════════════════════ --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Santri</td>
            <td class="colon">:</td>
            <td>{{ $santri->nama }}</td>

            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td>{{ $kelas?->tingkat?->nama ?? '-' }} - {{ $kelas?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIS / Nomor Induk</td>
            <td class="colon">:</td>
            <td>{{ $santri->nis ?? '-' }}</td>

            <td class="label">Tahun Ajaran</td>
            <td class="colon">:</td>
            <td>{{ $tahunAjaran->nama }} ({{ ucfirst($tahunAjaran->semester) }})</td>
        </tr>
        <tr>
            <td class="label">Wali Santri</td>
            <td class="colon">:</td>
            <td colspan="4">{{ $santri->wali->first()?->nama ?? '-' }}</td>
        </tr>
        @if($santri->tanggal_lahir)
        <tr>
            <td class="label">Tanggal Lahir</td>
            <td class="colon">:</td>
            <td colspan="4">{{ \Carbon\Carbon::parse($santri->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        @endif
    </table>

    {{-- ═══ Nilai Mapel Diniyah ═══════════════════════════════════ --}}
    <h3>Nilai Mata Pelajaran Diniyah</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th>Mata Pelajaran</th>
                <th style="width: 80px;" class="center">Nilai</th>
                <th style="width: 70px;" class="center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiAkhir as $no => $n)
            <tr>
                <td class="center">{{ $no + 1 }}</td>
                <td>{{ $n->mataPelajaran->nama }}</td>
                <td class="center bold">{{ number_format($n->nilai_akhir, 2) }}</td>
                <td class="center predikat-{{ $n->predikat }}">{{ $n->predikat }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="center">Belum ada nilai untuk semester ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ═══ Penilaian Sikap & Akhlak ══════════════════════════════ --}}
    @if($nilaiSikap->isNotEmpty())
    <h3>Penilaian Sikap &amp; Akhlak</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Aspek</th>
                <th style="width: 70px;" class="center">Predikat</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiSikap as $sikap)
            <tr>
                <td>{{ $sikap->aspek }}</td>
                <td class="center predikat-{{ $sikap->predikat }}">{{ $sikap->predikat ?? '-' }}</td>
                <td>{{ $sikap->deskripsi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ═══ Kehadiran ═════════════════════════════════════════════ --}}
    @if($absensi->isNotEmpty())
    <h3>Kehadiran</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Status</th>
                <th style="width: 100px;" class="center">Jumlah Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $a)
            <tr>
                <td>{{ ucfirst($a->status) }}</td>
                <td class="center">{{ $a->jumlah }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ═══ Catatan Wali Kelas ════════════════════════════════════ --}}
    @if($catatanWaliKelas && $catatanWaliKelas->catatan)
    <h3>Catatan Wali Kelas</h3>
    <div class="catatan">{{ $catatanWaliKelas->catatan }}</div>
    @endif

    {{-- ═══ Tanda Tangan (3 kolom: Wali Santri, Wali Kelas, Mudir) ═══ --}}
    <div class="ttd">
        <div class="ttd-row">
            {{-- Wali Santri --}}
            <div class="ttd-cell">
                <div class="placeholder-tanggal">.</div>
                <div class="role">Wali Santri</div>
                <div class="nama">{{ $santri->wali->first()?->nama ?? '_______________' }}</div>
            </div>

            {{-- Wali Kelas --}}
            <div class="ttd-cell">
                <div class="placeholder-tanggal">.</div>
                <div class="role">Wali Kelas</div>
                <div class="nama">{{ $waliKelas->user->name ?? $waliKelas->nama ?? '_______________' }}</div>
            </div>

            {{-- Mudir --}}
            <div class="ttd-cell">
                <div>{{ $settings['tempat_terbit'] ? $settings['tempat_terbit'] . ', ' : '' }}{{ now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="role">{{ $settings['mudir_jabatan'] ?: 'Mudir' }}</div>
                <div class="nama">{{ $settings['mudir'] ?: '_______________' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
