@php
    $user = auth()->user();
    $buku = $peminjaman->buku;
    $eksemplar = $peminjaman->eksemplar;
    $denda = $peminjaman->denda;

    $badgeClass = 'status-dipinjam';
    $badgeText = 'Dipinjam';
    $badgeIcon = 'bi bi-bookmark-fill';
    if ($peminjaman->status === 'dikembalikan') {
        $badgeClass = 'status-dikembalikan';
        $badgeText = 'Dikembalikan';
        $badgeIcon = 'bi bi-check-circle-fill';
    } elseif ($peminjaman->status === 'dipinjam' && $peminjaman->terlambat_hari > 0) {
        $badgeClass = 'status-terlambat';
        $badgeText = 'Terlambat';
        $badgeIcon = 'bi bi-exclamation-triangle-fill';
    } elseif (in_array($peminjaman->status, ['menunggu_konfirmasi', 'menunggu_pengembalian'])) {
        $badgeClass = 'status-menunggu';
        if ($peminjaman->tipe_konfirmasi === 'kembali') {
            $badgeText = 'Menunggu Konfirmasi';
        } else {
            $badgeText = 'Menunggu Konfirmasi';
        }
        $badgeIcon = 'bi bi-hourglass-split';
    }

    $totalDenda = $denda ? $denda->jumlah_denda : $peminjaman->denda ?? $peminjaman->taksiran_denda ?? 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { overflow-x: hidden; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .main-container {
            max-width: 700px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .detail-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .detail-header {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            padding: 25px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 18px;
        }
        .back-btn:hover { background: rgba(255,255,255,0.3); color: white; }

        .detail-header h2 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .detail-body { padding: 30px; }

        .book-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .book-cover {
            width: 120px;
            height: 160px;
            border-radius: 14px;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .cover-placeholder {
            width: 120px;
            height: 160px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.6);
            font-size: 48px;
            flex-shrink: 0;
        }

        .book-info { flex: 1; }

        .book-title {
            font-size: 18px;
            font-weight: 700;
            color: #222;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .book-author { font-size: 14px; color: #888; margin-bottom: 10px; }
        .book-author i { color: #1a6e35; margin-right: 5px; }

        .book-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #666;
        }

        .meta-item i { color: #1a6e35; width: 18px; text-align: center; }
        .meta-item .meta-label { color: #aaa; min-width: 120px; }
        .meta-item .meta-value { font-weight: 600; color: #333; }

        .info-section { margin-bottom: 25px; }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px 18px;
        }

        .info-box.full { grid-column: 1 / -1; }

        .info-box .label {
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-box .value {
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-dipinjam { background: #fff3cd; color: #856404; }
        .status-dikembalikan { background: #d1e7dd; color: #0f5132; }
        .status-terlambat { background: #f8d7da; color: #721c24; }
        .status-menunggu { background: #e0e7ff; color: #4338ca; }

        .denda-box {
            background: #fff5f5;
            border: 2px solid #f8d7da;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-top: 15px;
        }

        .denda-box.paid {
            background: #f0fff4;
            border-color: #d1e7dd;
        }

        .denda-box .denda-label {
            font-size: 12px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .denda-box .denda-amount {
            font-size: 28px;
            font-weight: 800;
            color: #dc3545;
        }

        .denda-box.paid .denda-amount { color: #1a6e35; }

        .denda-box .denda-days {
            font-size: 13px;
            color: #888;
            margin-top: 5px;
        }

        .qr-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #f0f0f0;
        }

        .qr-section .section-title { text-align: left; }

        .qr-wrapper {
            display: inline-block;
            background: white;
            padding: 15px;
            border-radius: 16px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .qr-wrapper img, .qr-wrapper svg {
            width: 150px;
            height: 150px;
        }

        .qr-label {
            margin-top: 10px;
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #1a6e35;
        }

        @media (max-width: 600px) {
            .main-container { margin-top: 80px; padding: 0 15px; }
            .detail-header { padding: 20px 20px; }
            .detail-body { padding: 20px 15px; }
            .book-section { flex-direction: column; align-items: center; text-align: center; }
            .book-cover, .cover-placeholder { width: 140px; height: 190px; }
            .book-info { width: 100%; }
            .info-grid { grid-template-columns: 1fr; }
        }

        body.dark-mode { background: #121212; color: #fff; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .detail-card, body.dark-mode .detail-header { background: #1e1e1e; }
        body.dark-mode .detail-header h2, body.dark-mode .book-title { color: #fff; }
        body.dark-mode .detail-body { background: #1e1e1e; }
        body.dark-mode .info-box { background: #2a2a2a; }
        body.dark-mode .info-box .value { color: #fff; }
        body.dark-mode .info-box .label { color: #888; }
        body.dark-mode .meta-item { color: #aaa; }
        body.dark-mode .meta-item .meta-value { color: #ccc; }
        body.dark-mode .book-author { color: #888; }
        body.dark-mode .section-title { color: #888; border-color: #333; }
        body.dark-mode .qr-section { border-color: #333; }
        body.dark-mode .qr-wrapper { background: #2a2a2a; }
        body.dark-mode .denda-box { background: #2a1a1a; border-color: #5a2a2a; }
        body.dark-mode .denda-box.paid { background: #1a2a1a; border-color: #2a5a2a; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid px-4">
            <a href="{{ route('profil.riwayat') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
            <span style="font-weight:700;color:#333;font-size:16px;">Detail Peminjaman</span>
        </div>
    </nav>

    <div class="main-container">
        <div class="detail-card">
            <div class="detail-header">
                <a href="{{ route('profil.riwayat') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
                <h2><i class="{{ $badgeIcon }} me-2"></i>Detail Peminjaman</h2>
            </div>
            <div class="detail-body">

                <div class="book-section">
                    @if($buku && $buku->sampul)
                        <img src="{{ asset($buku->sampul) }}" class="book-cover" alt="{{ $buku->judul }}">
                    @else
                        <div class="cover-placeholder"><i class="bi bi-book"></i></div>
                    @endif
                    <div class="book-info">
                        <div class="book-title">{{ $buku->judul ?? 'Buku tidak ditemukan' }}</div>
                        <div class="book-author"><i class="bi bi-person-fill"></i>{{ $buku->pengarang ?? '-' }}</div>
                        <div class="book-meta">
                            <div class="meta-item">
                                <i class="bi bi-upc-scan"></i>
                                <span class="meta-label">Kode Buku</span>
                                <span class="meta-value">
                                    @if($eksemplar){{ $eksemplar->kode_buku }}@elseif($buku){{ $buku->kode_buku }}@else-@endif
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-buildings"></i>
                                <span class="meta-label">Penerbit</span>
                                <span class="meta-value">{{ $buku->penerbit ?? '-' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-calendar3"></i>
                                <span class="meta-label">Tahun Terbit</span>
                                <span class="meta-value">{{ $buku->tahun_terbit ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="section-title">Informasi Peminjaman</div>
                    <div class="info-grid">
                        <div class="info-box">
                            <div class="label">Tanggal Pinjam</div>
                            <div class="value">{{ $peminjaman->tanggal_pinjam }}</div>
                        </div>
                        <div class="info-box">
                            <div class="label">Tanggal Jatuh Tempo</div>
                            <div class="value">{{ $peminjaman->tanggal_kembali }}</div>
                        </div>
                        <div class="info-box">
                            <div class="label">Tanggal Kembali</div>
                            <div class="value">
                                @if($peminjaman->tanggal_dikembalikan)
                                    {{ $peminjaman->tanggal_dikembalikan }}
                                @else
                                    <span style="color:#aaa;font-weight:400">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-box">
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="status-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </div>
                        </div>
                        @if($peminjaman->terlambat_hari > 0)
                            <div class="info-box full">
                                <div class="label">Lama Keterlambatan</div>
                                <div class="value" style="color:#dc3545">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $peminjaman->terlambat_hari }} hari
                                </div>
                            </div>
                        @endif
                        @if($peminjaman->catatan)
                            <div class="info-box full">
                                <div class="label">Catatan</div>
                                <div class="value" style="font-weight:400;font-size:13px">{{ $peminjaman->catatan }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($totalDenda > 0)
                    <div class="denda-box {{ $denda && $denda->status === 'lunas' ? 'paid' : '' }}">
                        <div class="denda-label">Total Denda</div>
                        <div class="denda-amount">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                        @if($peminjaman->terlambat_hari > 0)
                            <div class="denda-days">{{ $peminjaman->terlambat_hari }} hari x Rp 1.000</div>
                        @endif
                        @if($denda && $denda->status)
                            <div style="margin-top:8px;font-size:12px;color:#888">
                                Status: {{ ucfirst($denda->status) }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($eksemplar && $eksemplar->qrcode_path && file_exists(public_path($eksemplar->qrcode_path)))
                    <div class="qr-section">
                        <div class="section-title">QR Code Eksemplar</div>
                        <div class="qr-wrapper">
                            <img src="{{ asset($eksemplar->qrcode_path) }}" alt="QR Code">
                            <div class="qr-label">{{ $eksemplar->kode_buku }}</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        if(localStorage.getItem('darkMode') === 'enabled'){
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
