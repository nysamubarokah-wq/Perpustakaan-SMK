@php
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Denda - Perpustakaan SMK Maarif</title>
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

        .tagihan-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .tagihan-header {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            padding: 25px 35px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tagihan-header h2 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .tagihan-body { padding: 25px 35px; }

        .total-box {
            background: #f0fff4;
            border: 2px solid #1a6e35;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
        }

        .total-box .label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .total-box .amount {
            font-size: 32px;
            font-weight: 800;
            color: #dc3545;
        }

        .denda-item {
            border: 1px solid #f0f0f0;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .denda-item:last-child { margin-bottom: 0; }

        .denda-sampul {
            width: 50px;
            height: 65px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sampul-placeholder {
            width: 50px;
            height: 65px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.6);
            font-size: 20px;
            flex-shrink: 0;
        }

        .denda-info { flex: 1; }

        .denda-info h6 {
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .denda-info p {
            font-size: 12px;
            color: #888;
            margin: 0 0 3px;
        }

        .denda-info .nominal {
            font-size: 15px;
            font-weight: 700;
            color: #dc3545;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i { font-size: 60px; margin-bottom: 15px; display: block; }
        .empty-state p { font-size: 15px; color: #888; }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .back-btn:hover { background: #eee; color: #333; text-decoration: none; }

        @media (max-width: 600px) {
            .main-container { margin-top: 80px; padding: 0 15px; }
            .tagihan-header { padding: 20px 20px; }
            .tagihan-body { padding: 20px 15px; }
            .total-box .amount { font-size: 26px; }
            .denda-item { flex-wrap: wrap; }
        }

        body.dark-mode { background: #121212; color: #fff; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar span,
        body.dark-mode .navbar a { color: #fff !important; }
        body.dark-mode .tagihan-card { background: #1e1e1e; }
        body.dark-mode .tagihan-header { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        body.dark-mode .tagihan-header h2 { color: #fff; }
        body.dark-mode .tagihan-header i { color: #fff !important; }
        body.dark-mode .tagihan-body { background: #1e1e1e; }
        body.dark-mode .total-box { background: #1e1e1e; border-color: #1a6e35; }
        body.dark-mode .total-box .amount { color: #27ae60; }
        body.dark-mode .total-box .label { color: #888; }
        body.dark-mode .denda-item { background: #2a2a2a; border-color: #333; }
        body.dark-mode .denda-info h6 { color: #fff; }
        body.dark-mode .denda-info p { color: #aaa; }
        body.dark-mode .denda-info .nominal { color: #27ae60; }
        body.dark-mode .empty-state { color: #888; }
        body.dark-mode .empty-state i { color: #27ae60; }
        body.dark-mode .empty-state p { color: #888; }
        body.dark-mode .back-btn { background: #1e1e1e; color: #fff; border: 1px solid #333; }
        body.dark-mode .back-btn:hover { background: #2a2a2a; }
        body.dark-mode .alert-success { background: #1a2e1a; border-color: #27ae60; color: #27ae60; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between w-100">
                <a href="{{ route('koleksi.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                    <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
                </a>
                <a href="{{ route('profil.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="tagihan-card">
            <div class="tagihan-header">
                <i class="bi bi-wallet2" style="font-size:28px"></i>
                <h2>Tagihan Denda</h2>
            </div>
            <div class="tagihan-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="total-box">
                    <div class="label">Total Tagihan</div>
                    <div class="amount">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                </div>

                @if(count($tagihanDenda) > 0)
                    @foreach($tagihanDenda as $tagihan)
                    <div class="denda-item">
                        @if($tagihan->buku && $tagihan->buku->sampul)
                            <img src="{{ asset($tagihan->buku->sampul) }}" class="denda-sampul" alt="{{ $tagihan->buku->judul }}">
                        @else
                            <div class="sampul-placeholder"><i class="bi bi-book"></i></div>
                        @endif

                        <div class="denda-info">
                            <h6>{{ $tagihan->buku->judul ?? '-' }}</h6>
                            <p><i class="bi bi-clock-history"></i> Terlambat {{ $tagihan->hari_terlambat }} hari</p>
                            <div class="nominal">Rp {{ number_format($tagihan->total_denda ?? $tagihan->denda ?? 0, 0, ',', '.') }}</div>
                        </div>

                        <div>
                            <span class="badge rounded-pill bg-danger px-3 py-2">Belum Dibayar</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Tidak ada tagihan denda.</p>
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
