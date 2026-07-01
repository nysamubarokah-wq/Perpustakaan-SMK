<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Semua QR Code</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #222; }
        .header p { font-size: 12px; color: #888; }
        .grid { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }
        .card { background: white; border: 2px solid #1a6e35; border-radius: 12px; padding: 15px; text-align: center; width: 200px; break-inside: avoid; }
        .card h3 { font-size: 12px; color: #222; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; }
        .card .kode { font-size: 11px; color: #1a6e35; font-weight: 700; font-family: monospace; margin-bottom: 5px; }
        .card .isbn { font-size: 9px; color: #888; margin-bottom: 8px; }
        .qr-wrap svg { width: 140px; height: 140px; }
        @media print {
            body { background: white; padding: 10px; }
            .card { border: 1.5px solid #000; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;margin-bottom:20px">
        <button onclick="window.print()" style="padding:10px 24px;background:#1a6e35;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer">
            Cetak Semua
        </button>
        <a href="{{ route('buku.index') }}" style="display:inline-block;margin-left:10px;padding:10px 24px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none">
            Kembali
        </a>
    </div>
    <div class="header">
        <h1>Seluruh QR Code Buku</h1>
        <p>Total: {{ count($items) }} buku &mdash; Dicetak: {{ date('d M Y H:i') }}</p>
    </div>
    <div class="grid">
        @foreach($items as $item)
        <div class="card">
            <h3>{{ $item['buku']->judul }}</h3>
            <div class="kode">{{ $item['buku']->kode_buku }}</div>
            <div class="isbn">ISBN: {{ $item['buku']->isbn ?? '-' }}</div>
            <div class="qr-wrap">{!! $item['svg'] !!}</div>
        </div>
        @endforeach
    </div>
</body>
</html>
