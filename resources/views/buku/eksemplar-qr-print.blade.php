<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR - {{ $eksemplar->kode_buku }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f5f7fa; }
        .card { background: white; border: 2px solid #1a6e35; border-radius: 16px; padding: 30px; text-align: center; width: 320px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .card h2 { font-size: 14px; color: #222; margin-bottom: 3px; }
        .card .kode { font-size: 16px; color: #1a6e35; font-weight: 700; margin-bottom: 3px; font-family: monospace; }
        .card .isbn { font-size: 11px; color: #888; margin-bottom: 15px; }
        .qr-wrap svg { width: 200px; height: 200px; }
        @media print {
            body { background: white; }
            .card { border: 2px solid #000; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>{{ $buku->judul }}</h2>
        <div class="kode">{{ $eksemplar->kode_buku }}</div>
        <div class="isbn">ISBN: {{ $buku->isbn ?? '-' }}</div>
        <div class="qr-wrap">{!! $svg !!}</div>
    </div>
    <script>window.print();</script>
</body>
</html>
