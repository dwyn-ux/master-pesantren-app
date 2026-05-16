<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan' }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 18mm 14mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            line-height: 1.45;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header h1 {
            margin: 0 0 2px 0;
            color: #0f766e;
            font-size: 16px;
        }
        .header .sub {
            color: #64748b;
            font-size: 10px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #475569;
            margin-bottom: 12px;
        }
        h2 {
            font-size: 12.5px;
            color: #0f766e;
            margin: 18px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }
        h3 { font-size: 11.5px; margin: 14px 0 5px 0; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 5px 7px; text-align: left; vertical-align: top; }
        thead th {
            background: #0f766e;
            color: #fff;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #64748b; }
        .summary-grid {
            display: table;
            width: 100%;
            border-spacing: 6px 0;
            margin-bottom: 10px;
        }
        .summary-card {
            display: table-cell;
            background: #f0fdfa;
            border: 1px solid #5eead4;
            padding: 8px 10px;
            border-radius: 4px;
            width: 25%;
        }
        .summary-card .label { font-size: 9px; color: #0f766e; text-transform: uppercase; letter-spacing: 0.4px; }
        .summary-card .value { font-size: 14px; color: #134e4a; font-weight: bold; margin-top: 2px; }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .analysis {
            background: #f8fafc;
            border-left: 4px solid #0f766e;
            padding: 10px 12px;
            margin: 10px 0;
            font-size: 10.5px;
        }
        .analysis strong { color: #0f766e; }
        .analysis ul { margin: 5px 0 0 16px; padding: 0; }
        .analysis li { margin-bottom: 3px; }
        .footer {
            position: fixed;
            bottom: -6mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'Laporan' }}</h1>
        @if(!empty($subtitle))
            <div class="sub">{{ $subtitle }}</div>
        @endif
    </div>

    <div class="meta">
        <div>
            @isset($periode)
                <strong>Periode:</strong> {{ $periode }}
            @endisset
        </div>
        <div>
            <strong>Dicetak:</strong> {{ now()->translatedFormat('d F Y H:i') }}
        </div>
    </div>

    {{ $slot ?? '' }}
    @yield('content')

    <div class="footer">
        {{ config('app.name') }} — Dokumen ini dihasilkan otomatis.
    </div>
</body>
</html>
