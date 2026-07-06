<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-book Digital - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }

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
            max-width: 1100px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            margin-bottom: 25px;
        }

        /* CARD */
        .buku-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1);
            height: 100%;
            display: flex;
            flex-direction: column;
            max-width: 185px;
            margin: 0 auto;
            width: 100%;
        }

        .buku-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .buku-cover {
            height: 220px;
            position: relative;
            overflow: hidden;
        }

        .buku-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .buku-card:hover .buku-cover img {
            transform: scale(1.05);
        }

        .buku-cover-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: rgba(255,255,255,0.5);
        }

        .cover-1 { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        .cover-2 { background: linear-gradient(135deg, #2c3e50, #3498db); }
        .cover-3 { background: linear-gradient(135deg, #8e44ad, #e056fd); }
        .cover-4 { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .cover-5 { background: linear-gradient(135deg, #d35400, #e67e22); }
        .cover-6 { background: linear-gradient(135deg, #16a085, #1abc9c); }

        .buku-body {
            padding: 12px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 2px;
        }

        .buku-body h5 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.2em;
            line-height: 1.3;
        }

        .buku-meta { font-size: 12px; color: #888; margin-bottom: 0; display: flex; align-items: center; gap: 5px; }

        .btn-baca {
            display: block;
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            margin-top: auto;
            transition: all 0.3s;
        }

        .btn-baca:hover {
            opacity: 0.9;
            transform: scale(1.02);
            color: white;
        }

        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 15px; display: block; }
        .empty-state p { color: #aaa; font-size: 15px; }

        @media (max-width: 992px) {
            .main-container { max-width: 100%; }
            .buku-cover { height: 170px; }
        }

        @media (max-width: 768px) {
            .main-container { max-width: 100%; padding: 0 14px; }
            .page-title { font-size: 18px; margin-bottom: 18px; }
            .buku-cover { height: 170px; }
            .buku-cover-placeholder { font-size: 44px; }
            .buku-body { padding: 10px; }
            .buku-body h5 { font-size: 12px; min-height: 2em; }
            .buku-meta { font-size: 11px; }
            .btn-baca { padding: 10px; font-size: 12px; }
        }

        @media (max-width: 400px) {
            .main-container { padding: 0 8px; }
            .buku-cover { height: 140px; }
            .buku-cover-placeholder { font-size: 40px; }
            .buku-body { padding: 8px; }
            .buku-body h5 { font-size: 11px; min-height: 1.8em; }
            .btn-baca { padding: 8px; font-size: 11px; }
        }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar span { color: #1a6e35 !important; }
        body.dark-mode .buku-card { background: #1e1e1e; }
        body.dark-mode .page-title,
        body.dark-mode .buku-body h5 { color: #ffffff; }
        body.dark-mode .buku-meta { color: #bdbdbd; }
        body.dark-mode .empty-state i { color: #444; }
        body.dark-mode .empty-state p { color: #888; }
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
            <a href="{{ route('profil.index') }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="main-container">

    <h1 class="page-title">
        <i class="bi bi-book-half" style="color:#7c3aed"></i> E-book Digital
        <span style="font-size:14px;color:#888;font-weight:400">({{ $ebooks->total() }} buku tersedia)</span>
    </h1>

    @if($ebooks->count() > 0)
    <div class="row g-3">
        @foreach($ebooks as $index => $ebook)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('ebook.show', $ebook->id) }}" style="text-decoration:none;height:100%;display:flex;flex-direction:column">
                <div class="buku-card" style="height:100%">
                    <div class="buku-cover cover-{{ ($index % 6) + 1 }}">
                        @if($ebook->cover)
                            <img src="{{ asset($ebook->cover) }}" alt="{{ $ebook->judul }}">
                        @else
                            <div class="buku-cover-placeholder">
                                <i class="bi bi-book-half"></i>
                            </div>
                        @endif

                        {{-- Badge VIP/Koin/Gratis di pojok --}}
                        <div style="position:absolute;top:8px;left:8px">
                            @if($ebook->is_vip)
                                <span style="background:#f59e0b;color:white;font-size:10px;padding:3px 8px;border-radius:8px;font-weight:600">⭐ VIP</span>
                            @elseif($ebook->harga_koin > 0)
                                <span style="background:#1a6e35;color:white;font-size:10px;padding:3px 8px;border-radius:8px;font-weight:600">🪙 {{ $ebook->harga_koin }}</span>
                            @else
                                <span style="background:#27ae60;color:white;font-size:10px;padding:3px 8px;border-radius:8px;font-weight:600">Gratis</span>
                            @endif
                        </div>
                    </div>
                    <div class="buku-body">
                        <h5>{{ $ebook->judul }}</h5>
                        <p class="buku-meta"><i class="bi bi-person"></i> {{ $ebook->penulis }}</p>
                        <div class="btn-baca">
                            <i class="bi bi-book-open"></i> Baca Sekarang
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $ebooks->links() }}</div>

    @else
    <div class="empty-state">
        <i class="bi bi-book-half"></i>
        <p>Belum ada e-book tersedia</p>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
}
</script>


</body>
</html>