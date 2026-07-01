<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Koleksi Buku - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }

        /* NAVBAR */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .navbar-brand span {
            font-size: 13px;
            font-weight: 700;
            color: #1a6e35;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 15px !important;
            transition: color 0.3s;
        }

        .nav-link:hover { color: #1a6e35 !important; }

        .navbar-search {
            display: flex;
            align-items: center;
            background: #f5f5f5;
            border-radius: 25px;
            padding: 6px 15px;
            gap: 8px;
        }

        .navbar-search input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 13px;
            width: 180px;
        }

        .navbar-search i { color: #999; }

        /* Dropdown ngambang */
        .floating-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 20px;
            min-width: 220px;
            display: none;
            z-index: 999;
            animation: fadeDown 0.2s ease;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .floating-dropdown.show { display: block; }

        .floating-dropdown h6 {
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .genre-list-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 13px;
            color: #333;
            text-decoration: none;
        }

        .genre-list-item:hover {
            background: #f0faf4;
            color: #1a6e35;
        }

        .genre-list-item i { color: #1a6e35; width: 18px; }

        /* Layanan dropdown */
        .layanan-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 20px;
            min-width: 260px;
            display: none;
            z-index: 999;
            animation: fadeDown 0.2s ease;
        }

        .layanan-dropdown.show { display: block; }

        .penjaga-card {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .penjaga-card img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1a6e35;
        }

        .penjaga-card h6 { font-weight: 700; color: #222; margin-bottom: 3px; }
        .penjaga-card p { font-size: 12px; color: #666; margin: 0; }

        /* Profil avatar */
        .profil-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #1a6e35;
            cursor: pointer;
            object-fit: cover;
        }

        .nav-item { position: relative; }

        /* HERO */
        .hero {
            height: 45vh;
            min-height: 300px;
            background: url('/images/sekolah.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 70px;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
        }

        .hero-content {
            position: relative;
            text-align: center;
            color: white;
            padding: 60px 20px 20px;
        }

        .hero-content h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-content p {
            font-size: 15px;
            opacity: 0.85;
        }

        /* SEARCH BOX */
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }

        .search-input {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 14px;
            width: 100%;
            outline: none;
            transition: border 0.3s;
        }

        .search-input:focus { border-color: #27ae60; }

        .btn-search {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        /* BUKU CARDS */
        .buku-section { padding: 40px 0 60px; }

        .buku-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .buku-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .buku-cover {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .buku-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .buku-cover-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: rgba(255,255,255,0.5);
        }

        .buku-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 6px;
        }

        .buku-body h5 {
            font-size: 14px;
            font-weight: 700;
            color: #222;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4em;
        }

        .buku-meta {
            font-size: 12px;
            color: #888;
            margin-bottom: 0;
        }

        .stok-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .stok-ada { background: #d4edda; color: #1a6e35; }
        .stok-habis { background: #f8d7da; color: #721c24; }

        .btn-pinjam {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: auto;
        }

        .btn-pinjam-aktif {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
        }

        .btn-pinjam-aktif:hover { opacity: 0.88; transform: scale(1.02); }

        .btn-pinjam-nonaktif {
            background: #eee;
            color: #aaa;
            cursor: not-allowed;
        }

        .btn-pinjam-link {
            margin-top: auto;
        }

        .empty-state {
            text-align: center;
            padding: 60px 0;
        }

        .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 15px; }
        .empty-state p { color: #aaa; font-size: 15px; }

        /* Cover colors */
        .cover-1 { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        .cover-2 { background: linear-gradient(135deg, #2c3e50, #3498db); }
        .cover-3 { background: linear-gradient(135deg, #8e44ad, #e056fd); }
        .cover-4 { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .cover-5 { background: linear-gradient(135deg, #d35400, #e67e22); }
        .cover-6 { background: linear-gradient(135deg, #16a085, #1abc9c); }

        /* ============================================
           RESPONSIVE BREAKPOINTS
           ============================================ */

        @keyframes fadeDownCenter {
            from { opacity: 0; transform: translate(-50%, -10px); }
            to   { opacity: 1; transform: translate(-50%, 0); }
        }

        /* --- Tablet & below (≤991px) --- */
        @media (max-width: 991px) {
            .navbar-brand span { font-size: 11px; }
            .navbar-search input { width: 140px; }
        }

        /* --- Phone landscape & small tablet (≤768px) --- */
        @media (max-width: 768px) {
            /* Container padding */
            .container { padding-left: 12px !important; padding-right: 12px !important; }

            /* Navbar */
            .navbar { padding: 8px 0; }
            .navbar .container-fluid { padding-left: 12px !important; padding-right: 12px !important; }
            .navbar .container-fluid > div {
                flex-wrap: wrap;
                gap: 6px;
            }
            .navbar-search { display: none; }
            .nav-link {
                padding: 6px 8px !important;
                font-size: 12px;
            }
            .nav-text { display: none; }
            .navbar-brand img,
            .navbar-brand img[style*="45px"] { width: 36px !important; height: 36px !important; }
            .navbar-brand span { font-size: 10px; }

            /* Dropdowns – centered on mobile */
            #genreDropdown,
            #layananDropdown,
            #profilDropdown {
                position: fixed;
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                width: 92vw;
                max-width: 320px;
                max-height: calc(100vh - 80px);
                overflow-y: auto;
                animation: fadeDownCenter 0.2s ease;
                border-radius: 16px;
            }

            /* Hero */
            .hero { height: 30vh; min-height: 180px; margin-top: 56px; }
            .hero-content h1 { font-size: 20px; }
            .hero-content p { font-size: 12px; padding: 0 16px; }

            /* Search box */
            .search-box {
                padding: 14px;
                margin-top: -16px;
                border-radius: 12px;
            }
            .search-box form > .d-flex {
                flex-direction: column;
                gap: 10px !important;
            }
            .btn-search { width: 100%; }

            /* Book section */
            .buku-section { padding: 24px 0 40px; }
            .buku-body { padding: 12px; }
            .buku-body h5 { font-size: 13px; min-height: 2.2em; }
            .buku-meta { font-size: 11px; }
            .buku-cover { height: 160px; }

            /* Badges */
            .badge-wrapper { gap: 3px; }
            .stok-badge,
            .badge-populer,
            .badge-baru {
                font-size: 9px;
                padding: 2px 6px;
            }

            /* Section heading */
            .buku-section .d-flex.justify-content-between { flex-wrap: wrap; gap: 4px; }
            .buku-section .d-flex.justify-content-between h5 { font-size: 15px; word-break: break-word; }
            .buku-section .d-flex.justify-content-between span { font-size: 12px !important; }

            /* Notifications – stack bottom-center */
            #notifTerlambat,
            #notifH1,
            #notifH2 {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                max-width: calc(100vw - 32px) !important;
                width: calc(100vw - 32px);
                border-radius: 12px;
                padding: 12px 14px;
                font-size: 13px;
            }

            /* Modal Peraturan */
            #modalPeraturan { padding: 12px !important; }
            #modalPeraturan > div { border-radius: 16px !important; max-height: 90vh !important; }
            #modalPeraturan > div > div:first-child { padding: 18px !important; }
            #modalPeraturan > div > div:first-child div:first-child { font-size: 28px !important; }
            #modalPeraturan > div > div:first-child div:nth-child(2) { font-size: 15px !important; }
            #modalPeraturan > div > div:nth-child(2) { padding: 14px 16px !important; }
            #modalPeraturan > div > div:last-child { padding: 14px 16px !important; }

            /* Modal VIP */
            #modalVip > div {
                width: 94% !important;
                padding: 20px !important;
                border-radius: 18px !important;
            }

            /* Scan modals */
            .scan-modal-box {
                width: 94% !important;
                padding: 18px !important;
                border-radius: 14px !important;
            }
            .scan-manual-input { flex-direction: column; }
            .scan-manual-input button { width: 100%; }

            /* Penjaga card in dropdown */
            .penjaga-card img { width: 50px !important; height: 50px !important; }
            .penjaga-card h6 { font-size: 13px; }
            .penjaga-card p { font-size: 11px; }

            /* Floating dropdown padding */
            .floating-dropdown,
            .layanan-dropdown { padding: 14px !important; min-width: auto !important; }

            /* Pagination */
            .pagination { flex-wrap: wrap; gap: 4px; }
            .pagination .page-link { padding: 6px 10px; font-size: 13px; }
        }

        /* --- Small phone (≤480px) --- */
        @media (max-width: 480px) {
            .navbar .container-fluid { padding-left: 8px !important; padding-right: 8px !important; }
            .navbar-brand img,
            .navbar-brand img[style*="45px"] { width: 30px !important; height: 30px !important; }
            .navbar-brand span { font-size: 9px; }
            .nav-link { padding: 5px 6px !important; font-size: 11px; }

            .hero { height: 25vh; min-height: 150px; margin-top: 50px; }
            .hero-content h1 { font-size: 17px; }
            .hero-content p { font-size: 11px; padding: 0 10px; }

            .search-box { padding: 10px; margin-top: -12px; }
            .search-input { padding: 10px 14px; font-size: 13px; }

            .buku-section { padding: 16px 0 30px; }
            .buku-cover { height: 130px; }
            .buku-body { padding: 10px; }
            .buku-body h5 { font-size: 12px; -webkit-line-clamp: 2; min-height: 2em; }
            .buku-meta { font-size: 10px; }
            .btn-pinjam, .btn-pinjam-link { padding: 8px !important; font-size: 12px !important; }

            .buku-section .d-flex.justify-content-between { flex-direction: column; gap: 4px; }
            .buku-section .d-flex.justify-content-between h5 { font-size: 14px; }

            /* Dropdowns full width on tiny screens */
            #genreDropdown,
            #layananDropdown,
            #profilDropdown {
                width: 96vw;
                max-width: none;
            }
        }

/* DARK MODE */
body.dark-mode {
    background: #121212;
    color: #e0e0e0;
}

body.dark-mode .navbar,
body.dark-mode .search-box,
body.dark-mode .buku-card,
body.dark-mode .floating-dropdown,
body.dark-mode .layanan-dropdown {
    background: #1e1e1e;
    color: #e0e0e0;
}

body.dark-mode .nav-link,
body.dark-mode .genre-list-item,
body.dark-mode .buku-body h5,
body.dark-mode h5,
body.dark-mode h6 {
    color: #ffffff !important;
}

body.dark-mode .navbar-search {
    background: #2a2a2a;
}

body.dark-mode .navbar-search input,
body.dark-mode .search-input {
    background: #2a2a2a;
    color: white;
    border-color: #444;
}

body.dark-mode .buku-meta {
    color: #bdbdbd;
}

body.dark-mode .search-box {
    box-shadow: 0 5px 25px rgba(255,255,255,0.05);
}

body.dark-mode .genre-list-item:hover {
    background: #2d2d2d;
}

body.dark-mode .btn-pinjam-nonaktif {
    background: #333;
    color: #888;
}
body.dark-mode {
    background: #121212;
    color: white;
    transition: all .3s ease;
}

body.dark-mode .navbar,
body.dark-mode .detail-card,
body.dark-mode .profil-card,
body.dark-mode .riwayat-card,
body.dark-mode .modal-box {
    background: #1e1e1e;
    transition: all .3s ease;
}
body.dark-mode form[action*="favorit"] button {
    background: rgba(40,40,40,0.9) !important;
}

/* Perbaikan dark mode teks */
body.dark-mode span[style*="color:#222"],
body.dark-mode h5[style*="color:#222"],
body.dark-mode h6[style*="color:#222"],
body.dark-mode p[style*="color:#555"],
body.dark-mode p[style*="color:#666"],
body.dark-mode div[style*="color:#444"] {
    color: #ffffff !important;
}

body.dark-mode a {
    color: #e0e0e0;
}

/* Dropdown Profil */
body.dark-mode #profilDropdown {
    background: #1e1e1e !important;
    color: white !important;
}

body.dark-mode #profilDropdown h6,
body.dark-mode #profilDropdown p {
    color: white !important;
}

body.dark-mode #profilDropdown > div[style*="background:#f9f9f9"] {
    background: #2a2a2a !important;
}

body.dark-mode #profilDropdown button[type="submit"] {
    background: #333 !important;
    color: white !important;
}

/* Modal VIP */
body.dark-mode #modalVip > div {
    background: #1e1e1e !important;
    color: white !important;
}

body.dark-mode #modalVip h5,
body.dark-mode #modalVip p,
body.dark-mode #modalVip div {
    color: white !important;
}

body.dark-mode #modalVip div[style*="background:#f8f9fa"] {
    background: #2a2a2a !important;
}

body.dark-mode #modalVip button[onclick*="modalVip"] {
    color: #ddd !important;
}

/* CARD VIP AKTIF */
body.dark-mode #modalVip div[style*="#fff8e1"]{
    background: linear-gradient(135deg,#2d2412,#3d3118) !important;
}

body.dark-mode #modalVip div[style*="#fff3cd"]{
    background: linear-gradient(135deg,#2d2412,#3d3118) !important;
}

body.dark-mode #modalVip div[style*="color:#856404"]{
    color: #fbbf24 !important;
}

body.dark-mode #modalVip div[style*="color:#888"]{
    color: #d1d5db !important;
}

.badge-populer,
.badge-baru{
    display:inline-block;
    padding:3px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}

.badge-populer{
    background:#fff3cd;
    color:#d97706;
}

.badge-baru{
    background:#d4edda;
    color:#1a6e35;
}

.badge-wrapper{
    display:flex;
    gap:4px;
    align-items:center;
    flex-wrap:wrap;
    margin:8px 0 12px;
}

/* Badge responsive styles moved to main @media block */

/* ===========================
   DARK MODE - MODAL PERATURAN
=========================== */

body.dark-mode #modalPeraturan > div{
    background:#1e1e1e !important;
}

body.dark-mode #modalPeraturan div[style*="overflow-y:auto"]{
    background:#1e1e1e !important;
}

body.dark-mode #modalPeraturan div[style*="background:#fafafa"]{
    background:#252525 !important;
    border-top:1px solid #444 !important;
}

body.dark-mode #modalPeraturan p{
    color:#d1d5db !important;
}

body.dark-mode #modalPeraturan div[style*="color:#444"]{
    color:#f3f4f6 !important;
}

body.dark-mode #modalPeraturan div[style*="color:#666"]{
    color:#d1d5db !important;
}

body.dark-mode #modalPeraturan div[style*="color:#888"]{
    color:#cbd5e1 !important;
}

.pagination .page-link {
    color: #1a6e35;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #1a6e35, #27ae60);
    border-color: #1a6e35;
    color: white;
}

.pagination .page-link:hover {
    background: #f0faf4;
    border-color: #1a6e35;
}

body.dark-mode .pagination .page-link {
    background: #2a2a2a;
    border-color: #444;
    color: #e0e0e0;
}

body.dark-mode .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #1a6e35, #27ae60);
    border-color: #1a6e35;
}

body.dark-mode .pagination .page-link:hover {
    background: #3a3a3a;
}
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <ul class="navbar-nav flex-row gap-1 mb-0">
                    <li class="nav-item">
                       <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-house"></i> <span class="nav-text">Home</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleLayanan(event)">
    <i class="bi bi-person-workspace"></i> <span class="nav-text">Layanan</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
</a>
                        <div class="layanan-dropdown" id="layananDropdown">
                            <p style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Penjaga Perpustakaan</p>
                @php
    $adminAktif = \App\Models\User::where('role', 'admin')
        ->where('is_on_duty', true)
        ->first();

    // fallback kalau belum ada yang di-set bertugas
    if (!$adminAktif) {
        $adminAktif = \App\Models\User::where('role', 'admin')->first();
    }

    $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
@endphp
@php
    $noHpAdmin = $anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '';
    $waLink = $noHpAdmin ? 'https://wa.me/62' . ltrim(preg_replace('/[^0-9]/', '', $noHpAdmin), '0') : '#';
@endphp
<div class="penjaga-card">
    @if($adminAktif?->foto)
        <img src="{{ asset($adminAktif->foto) }}" alt="Penjaga" style="width:65px;height:65px;border-radius:50%;object-fit:cover;border:3px solid #1a6e35">
    @else
        <img src="https://ui-avatars.com/api/?name={{ urlencode($adminAktif?->name ?? 'Admin') }}&background=1a6e35&color=fff" alt="Penjaga">
    @endif
    <div>
        <h6>{{ $adminAktif?->name ?? 'Nama Penjaga' }}</h6>
        <p><i class="bi bi-telephone"></i> {{ $anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '-' }}</p>
        <p><i class="bi bi-clock"></i> {{ now()->locale('id')->isoFormat('dddd') }}, {{ now()->format('H:i') }} WIB</p>
    </div>
</div>
@if($noHpAdmin)
<a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px;padding:8px 16px;background:#25D366;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:background 0.2s" onmouseover="this.style.background='#1ebe57'" onmouseout="this.style.background='#25D366'">
    <i class="bi bi-whatsapp" style="font-size:16px"></i> Chat Admin
</a>
@endif
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleGenre(event)">
    <i class="bi bi-collection-fill"></i> <span class="nav-text">Genre Buku</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
</a>
                        <div class="floating-dropdown" id="genreDropdown">

                         
    <h6>Pilih Genre</h6>
    <a href="{{ route('koleksi.index') }}" class="genre-list-item">
        <i class="bi bi-grid-fill"></i> Semua Buku
    </a>
@foreach([
                                
                                ['icon' => 'bi-journal-text', 'nama' => 'Fiksi'],
                                ['icon' => 'bi-lightbulb', 'nama' => 'Non-Fiksi'],
                                ['icon' => 'bi-tools', 'nama' => 'Kejuruan'],
                                ['icon' => 'bi-calculator', 'nama' => 'Sains & Teknologi'],
                                ['icon' => 'bi-globe', 'nama' => 'Sejarah'],
                                ['icon' => 'bi-heart', 'nama' => 'Romance'],
                                ['icon' => 'bi-mortarboard', 'nama' => 'Pendidikan'],
                                ['icon' => 'bi-brush', 'nama' => 'Seni & Budaya'],
                            ] as $g)
                            <a href="{{ route('koleksi.index', ['genre' => $g['nama']]) }}" class="genre-list-item">
    <i class="bi {{ $g['icon'] }}"></i> {{ $g['nama'] }}
</a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="{{ route('favorit.index', ['from' => 'koleksi']) }}"><i class="bi bi-heart-fill"></i> <span class="nav-text">Favorit</span></a>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="#" onclick="openScanModal(event)"><i class="bi bi-qr-code-scan"></i> <span class="nav-text">Scan</span></a>
                    </li>
                </ul>

                <!-- Search -->
                <form method="GET" action="{{ route('koleksi.index') }}" class="d-flex">
                    @if($genre && is_string($genre))
                        <input type="hidden" name="genre" value="{{ $genre }}">
                    @elseif($genre && is_array($genre))
                        @foreach($genre as $g)
                            <input type="hidden" name="genre" value="{{ $g }}">
                        @endforeach
                    @endif
                    <div class="navbar-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari buku..." value="{{ $search ?? '' }}">
                    </div>
                </form>

                <!-- Profil -->
                 <button id="darkModeToggle"
        class="btn btn-sm btn-outline-secondary rounded-circle">
    <i class="bi bi-moon-fill"></i>
</button>
                <div class="nav-item">
                    <a href="#" onclick="toggleProfil(event)">
                      @if(auth()->user()->foto)
    <img src="{{ asset(auth()->user()->foto) }}" class="profil-avatar" alt="Profil">
@else
    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=1a6e35&color=fff"
         class="profil-avatar" alt="Profil">
@endif
                    </a>
                    <div class="floating-dropdown" id="profilDropdown" style="right:0;left:auto;min-width:250px">
                        <div style="text-align:center;margin-bottom:15px">
                           @if(auth()->user()->foto)
    <img src="{{ asset(auth()->user()->foto) }}" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
@else
    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=1a6e35&color=fff&size=200"
         style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
@endif
                            <h6 style="font-weight:700;color:#222;margin:0">{{ auth()->user()->name }}</h6>
                        </div>
                        <div style="background:#f9f9f9;border-radius:10px;padding:12px;margin-bottom:12px">
                            <p style="font-size:12px;color:#555;margin-bottom:6px"><i class="bi bi-person-badge" style="color:#1a6e35"></i> NIS: {{ auth()->user()->nis }}</p>
                            <p style="font-size:12px;color:#555;margin:0"><i class="bi bi-envelope" style="color:#1a6e35"></i> {{ auth()->user()->email }}</p>
                        </div>

                        @php
    $vipAktif = auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at);
    $sisaHari = $vipAktif ? (int) now()->diffInDays(auth()->user()->vip_expired_at) + 1 : 0;
@endphp
<button onclick="document.getElementById('profilDropdown').classList.remove('show');document.getElementById('modalVip').style.display='flex'"
    style="width:100%;padding:10px;background:{{ $vipAktif ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,#374151,#1f2937)' }};color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:8px;text-align:left">
    ⭐ {{ $vipAktif ? 'VIP Aktif · '.$sisaHari.' hari lagi' : 'Upgrade VIP' }}
</button>

                       <a href="{{ route('profil.index') }}" style="display:block;width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-align:center;text-decoration:none">
                       <i class="bi bi-person"></i> Lihat Profil
                       </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin-top:8px">
                            @csrf
                            <button type="submit" style="width:100%;padding:10px;background:#f8f9fa;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1><i class="bi bi-collection"></i> Koleksi Buku</h1>
        <p>Temukan buku favoritmu di perpustakaan SMK Maarif Walisongo Kajoran</p>
    </div>
</div>

<!-- SEARCH BOX -->
<div class="container">
    <div class="search-box">
        <form method="GET" action="{{ route('koleksi.index') }}">
            @if($genre && is_string($genre))
                <input type="hidden" name="genre" value="{{ $genre }}">
            @elseif($genre && is_array($genre))
                @foreach($genre as $g)
                    <input type="hidden" name="genre" value="{{ $g }}">
                @endforeach
            @endif
            <div class="d-flex gap-3">
                <input type="text" name="search" class="search-input"
                       placeholder="Cari judul buku atau pengarang..."
                       value="{{ $search ?? '' }}">
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>



<!-- KOLEKSI BUKU -->
<div class="buku-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 style="font-weight:700;color:#222">
                {{ $buku->count() }} Buku Ditemukan
                @if($search)
                    <span style="color:#888;font-weight:400;font-size:14px"> untuk "{{ $search }}"</span>
                @endif
            </h5>
        </div>

        @if($buku->count() > 0)
        <div class="row g-4">
            @foreach($buku as $index => $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="buku-card">
                   <div class="buku-cover cover-{{ ($index % 6) + 1 }}">
                    @if(($item->peminjaman_count ?? 0) >= 10)
<div style="
position:absolute;
top:10px;
left:10px;
background:#ff5722;
color:white;
padding:4px 10px;
border-radius:20px;
font-size:11px;
font-weight:bold;
z-index:5;">
🔥 Trending
</div>
@endif
    @if($item->sampul)
        <img src="{{ asset($item->sampul) }}" alt="{{ $item->judul }}">
    @else
        <div class="buku-cover-placeholder">
            <i class="bi bi-book"></i>
        </div>
    @endif

    <button type="button"
        onclick="toggleFavorit({{ $item->id }}, this)"
        data-favorit="{{ in_array($item->id, $favoritIds ?? []) ? 'true' : 'false' }}"
        style="position:absolute;top:8px;right:8px;width:32px;height:32px;border-radius:50%;border:none;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.15);z-index:5">
    @if(in_array($item->id, $favoritIds ?? []))
        <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>
    @else
        <i class="bi bi-heart" style="color:#999;font-size:15px"></i>
    @endif
</button>
</div>
                    <div class="buku-body">
                        <h5>{{ $item->judul }}</h5>
                        <p class="buku-meta"><i class="bi bi-person"></i> {{ $item->pengarang }}</p>
                        <p class="buku-meta"><i class="bi bi-building"></i> {{ $item->penerbit }}</p>
                      <div class="badge-wrapper">

    <span class="stok-badge {{ $item->stok > 0 ? 'stok-ada' : 'stok-habis' }}">
        {{ $item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Tidak Tersedia' }}
    </span>

  @if(($item->peminjaman_count ?? 0) >= 3)
    <span class="badge-populer">🔥 {{ $item->peminjaman_count }}x</span>
@elseif($item->created_at && $item->created_at->diffInDays(now()) <= 30)
    <span class="badge-baru">🟢 Terbaru</span>
@endif

</div>
                    
   @if($item->stok > 0)
    <a href="{{ route('buku.detail', $item->id) }}" 
       class="btn-pinjam-link"
       style="display:block;width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-align:center;text-decoration:none;transition:all 0.3s">
        <i class="bi bi-bookmark-plus"></i> Pinjam
    </a>
@else
    <button class="btn-pinjam btn-pinjam-nonaktif" disabled>
        Tidak Tersedia
    </button>
@endif
                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <p>Buku tidak ditemukan</p>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
   function positionDropdown(el) {
    const navbar = document.querySelector('.navbar');
    if (window.innerWidth <= 768) {
        el.style.top = (navbar.offsetHeight + 10) + 'px';
    } else {
        el.style.top = '';
    }
}

function toggleLayanan(e) {
    e.preventDefault();
    const el = document.getElementById('layananDropdown');
    positionDropdown(el);
    el.classList.toggle('show');
    document.getElementById('genreDropdown').classList.remove('show');
    document.getElementById('profilDropdown').classList.remove('show');
}

function toggleGenre(e) {
    e.preventDefault();
    const el = document.getElementById('genreDropdown');
    positionDropdown(el);
    el.classList.toggle('show');
    document.getElementById('layananDropdown').classList.remove('show');
    document.getElementById('profilDropdown').classList.remove('show');
}

function toggleProfil(e) {
    e.preventDefault();
    const el = document.getElementById('profilDropdown');
    positionDropdown(el);
    el.classList.toggle('show');
    document.getElementById('layananDropdown').classList.remove('show');
    document.getElementById('genreDropdown').classList.remove('show');
}

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-item') && !e.target.closest('.profil-avatar')) {
            document.getElementById('layananDropdown').classList.remove('show');
            document.getElementById('genreDropdown').classList.remove('show');
            document.getElementById('profilDropdown').classList.remove('show');
        }
    });

    
</script>
@php
    $anggotaLogin = \App\Models\Anggota::where('email', auth()->user()->email)->first();
    $bukuTerlambat = 0;
    if ($anggotaLogin) {
        $bukuTerlambat = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)
            ->where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->count();
    }
@endphp

@if($bukuTerlambat > 0)
<div id="notifTerlambat" style="
    position:fixed;bottom:20px;right:20px;
    background:#e74c3c;color:white;
    padding:15px 20px;border-radius:15px;
    box-shadow:0 10px 30px rgba(231,76,60,0.4);
    z-index:9999;max-width:300px;
    animation:slideIn 0.5s ease;
">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">⚠️</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Buku Terlambat!</div>
            <div style="font-size:12px;opacity:0.9">
                Kamu punya <strong>{{ $bukuTerlambat }} buku</strong> yang melewati tanggal kembali. Segera kembalikan!
            </div>
            <a href="{{ route('profil.index') }}" style="display:inline-block;margin-top:8px;background:white;color:#e74c3c;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">
                Lihat Sekarang →
            </a>
        </div>
        <button onclick="document.getElementById('notifTerlambat').style.display='none'" 
                style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">✕</button>
    </div>
</div>

<style>
@keyframes slideIn {
    from { transform: translateX(150px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
@endif

{{-- NOTIFIKASI HAMPIR JATUH TEMPO --}}
@php
    $bukuH1 = 0;
    $bukuH2 = 0;
    if ($anggotaLogin) {
        $besok = now()->addDay()->toDateString();
        $lusaDari = now()->addDays(2)->toDateString();
        
        $bukuH1 = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', $besok)
            ->count();

        $bukuH2 = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', $lusaDari)
            ->count();
    }
@endphp

@if($bukuH1 > 0)
<div id="notifH1" style="
    position:fixed;bottom:{{ $bukuTerlambat > 0 ? '110px' : '20px' }};right:20px;
    background:#e67e22;color:white;
    padding:15px 20px;border-radius:15px;
    box-shadow:0 10px 30px rgba(230,126,34,0.4);
    z-index:9998;max-width:300px;
    animation:slideIn 0.5s ease;
">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">🔔</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Segera Kembalikan Buku!</div>
            <div style="font-size:12px;opacity:0.9">
                Kamu punya <strong>{{ $bukuH1 }} buku</strong> yang harus dikembalikan <strong>besok</strong>!
            </div>
            <a href="{{ route('profil.index') }}" style="display:inline-block;margin-top:8px;background:white;color:#e67e22;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">
                Lihat Sekarang →
            </a>
        </div>
        <button onclick="document.getElementById('notifH1').style.display='none'"
                style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">✕</button>
    </div>
</div>
@endif

@if($bukuH2 > 0)
<div id="notifH2" style="
    position:fixed;bottom:{{ $bukuTerlambat > 0 && $bukuH1 > 0 ? '200px' : ($bukuTerlambat > 0 || $bukuH1 > 0 ? '110px' : '20px') }};right:20px;
    background:#f39c12;color:white;
    padding:15px 20px;border-radius:15px;
    box-shadow:0 10px 30px rgba(243,156,18,0.4);
    z-index:9997;max-width:300px;
    animation:slideIn 0.6s ease;
">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">📅</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Buku Akan Jatuh Tempo</div>
            <div style="font-size:12px;opacity:0.9">
                Kamu punya <strong>{{ $bukuH2 }} buku</strong> yang akan jatuh tempo dalam <strong>2 hari</strong>.
            </div>
            <a href="{{ route('profil.index') }}" style="display:inline-block;margin-top:8px;background:white;color:#f39c12;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">
                Lihat Sekarang →
            </a>
        </div>
        <button onclick="document.getElementById('notifH2').style.display='none'"
                style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">✕</button>
    </div>
</div>
@endif

<script>
const darkToggle = document.getElementById('darkModeToggle');

if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
    darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
}

darkToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');

    if(document.body.classList.contains('dark-mode')){
        localStorage.setItem('darkMode','enabled');
        darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        localStorage.setItem('darkMode','disabled');
        darkToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
    }
});

// Notifikasi hanya tampil saat pertama kali masuk (bukan saat refresh)
document.addEventListener('DOMContentLoaded', function() {
    const notifShown = sessionStorage.getItem('notifShown');

    if (notifShown) {
        // Sembunyikan semua notif jika sudah pernah ditampilin
        const notifTerlambat = document.getElementById('notifTerlambat');
        const notifH1 = document.getElementById('notifH1');
        const notifH2 = document.getElementById('notifH2');
        if (notifTerlambat) notifTerlambat.style.display = 'none';
        if (notifH1) notifH1.style.display = 'none';
        if (notifH2) notifH2.style.display = 'none';
    } else {
        // Tandai bahwa notif sudah ditampilin
        sessionStorage.setItem('notifShown', 'true');
    }
});
</script>
<script>
function toggleFavorit(bukuId, btn) {
    fetch(`/buku/${bukuId}/favorit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            icon.className = 'bi bi-heart-fill';
            icon.style.color = '#e74c3c';
            btn.dataset.favorit = 'true';
        } else {
            icon.className = 'bi bi-heart';
            icon.style.color = '#999';
            btn.dataset.favorit = 'false';
        }
    })
    .catch(() => alert('Gagal mengubah favorit, coba lagi.'));
}
</script>
<script>
// Simpan URL filter saat halaman dimuat
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.toString()) {
    sessionStorage.setItem('koleksiFilterUrl', window.location.pathname + '?' + urlParams.toString());
} else {
    sessionStorage.setItem('koleksiFilterUrl', window.location.pathname);
}

// Simpan posisi scroll pas user klik "Pinjam" (mau pindah ke halaman detail)
document.querySelectorAll('.btn-pinjam-link').forEach(link => {
    link.addEventListener('click', function() {
        sessionStorage.setItem('koleksiScrollY', window.scrollY);
    });
});

// Restore posisi scroll pas halaman koleksi dimuat lagi (setelah selesai pinjam)
window.addEventListener('load', function() {
    const savedScroll = sessionStorage.getItem('koleksiScrollY');
    if (savedScroll !== null) {
        window.scrollTo(0, parseInt(savedScroll));
        sessionStorage.removeItem('koleksiScrollY');
    }
});
</script>
{{-- MODAL PERATURAN --}}
@auth
@if(!auth()->user()->agreed_rules)
<div id="modalPeraturan" style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:20px;max-width:480px;width:100%;max-height:85vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
        
        <div style="background:linear-gradient(135deg,#1a6e35,#27ae60);padding:25px;text-align:center">
            <div style="font-size:35px;margin-bottom:8px">📋</div>
            <div style="color:white;font-size:18px;font-weight:700">Peraturan Perpustakaan</div>
            <div style="color:rgba(255,255,255,0.8);font-size:13px;margin-top:4px">SMK Maarif Walisongo Kajoran</div>
        </div>

        <div style="padding:20px 25px;overflow-y:auto;flex:1">
            <p style="font-size:13px;color:#666;margin-bottom:15px">Dengan menggunakan layanan perpustakaan digital ini, kamu wajib mematuhi peraturan berikut:</p>
            <div style="display:flex;flex-direction:column;gap:12px">
                @foreach([
                    'Kartu anggota perpustakaan wajib dibawa setiap kali berkunjung.',
                    'Buku yang dipinjam wajib dikembalikan tepat waktu sesuai batas peminjaman.',
                    'Keterlambatan pengembalian buku dikenakan denda Rp 1.000 per hari.',
                    'Buku yang rusak atau hilang wajib diganti sesuai harga buku.',
                    'Dilarang membawa makanan dan minuman ke dalam area perpustakaan.',
                    'Jaga ketenangan dan ketertiban selama berada di perpustakaan.',
                    'Gunakan fasilitas perpustakaan dengan bertanggung jawab.',
                    'E-book hanya boleh dibaca melalui platform ini, tidak untuk disebarluaskan.',
                ] as $i => $p)
                <div style="display:flex;gap:12px;align-items:flex-start">
                    <div style="min-width:28px;height:28px;background:#1a6e35;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                        {{ $i + 1 }}
                    </div>
                    <div style="font-size:13px;color:#444;line-height:1.6;padding-top:4px">{{ $p }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="padding:20px 25px;border-top:1px solid #f0f0f0;background:#fafafa">
            <p style="font-size:12px;color:#888;text-align:center;margin-bottom:15px">
                Dengan menekan tombol di bawah, kamu menyatakan telah membaca dan menyetujui seluruh peraturan di atas.
            </p>
            <button onclick="setujuPeraturan()"
                    style="width:100%;padding:14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">
                ✅ Saya Setuju & Mengerti
            </button>
        </div>
    </div>
</div>

<script>
// Cek apakah peraturan sudah ditampilkan di login ini
var loginToken = '{{ auth()->user()->rules_session_token }}';
var rulesKey = 'rules_{{ auth()->id() }}_' + loginToken;
if (localStorage.getItem(rulesKey)) {
    var mp = document.getElementById('modalPeraturan');
    if (mp) mp.style.display = 'none';
} else {
    // Hapus key lama user ini
    Object.keys(localStorage).forEach(function(k) {
        if (k.startsWith('rules_{{ auth()->id() }}_') && k !== rulesKey) localStorage.removeItem(k);
    });
    localStorage.setItem(rulesKey, '1');
}

function setujuPeraturan() {
    fetch('{{ route('setuju.peraturan') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(() => {
        document.getElementById('modalPeraturan').style.display = 'none';
    })
    .catch(() => {
        document.getElementById('modalPeraturan').style.display = 'none';
    });
}
</script>
@endif
@endauth

{{-- MODAL VIP --}}
<div id="modalVip" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:3000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:24px;padding:30px;width:90%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative">
        <button onclick="document.getElementById('modalVip').style.display='none'"
            style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:20px;color:#aaa;cursor:pointer">✕</button>

        <div style="text-align:center;margin-bottom:20px">
            <div style="font-size:40px;margin-bottom:8px">⭐</div>
            <h5 style="font-weight:800;color:#222;margin:0">Member VIP</h5>
            <p style="font-size:13px;color:#888;margin-top:4px">Akses semua e-book & fitur eksklusif</p>
        </div>

        @if($vipAktif)
        <div style="background:linear-gradient(135deg,#fff8e1,#fff3cd);border-radius:14px;padding:15px;text-align:center;margin-bottom:20px">
            <div style="font-size:13px;color:#856404;font-weight:600">✅ VIP Aktif</div>
            <div style="font-size:12px;color:#888;margin-top:4px">Berakhir: {{ auth()->user()->vip_expired_at->format('d M Y') }}</div>
            <div style="font-size:20px;font-weight:800;color:#f59e0b;margin-top:6px">{{ $sisaHari }} Hari Lagi</div>
        </div>
        @endif

        <div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:20px">
            <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:10px">Keuntungan VIP:</div>
            <div style="font-size:12px;color:#555;line-height:2">
                <div>📚 Akses semua e-book VIP</div>
                <div>📖 Pinjam hingga 6 buku sekaligus</div>
                <div>⏰ Durasi pinjam hingga 14 hari</div>
                <div>⭐ Badge VIP eksklusif</div>
            </div>
        </div>
@if(auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at))

    <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">
        Status VIP:
    </div>

    <button type="button"
        disabled
        style="width:100%;padding:13px;border-radius:12px;border:none;background:#e5e7eb;color:#6b7280;font-weight:700;font-size:14px;cursor:not-allowed">
        ✅ VIP Masih Aktif
    </button>

@else

    <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">
        Upgrade VIP 7 hari — 100 Koin:
    </div>

    <form action="{{ route('vip.beli') }}" method="POST">
        @csrf
        <button type="submit"
            {{ (auth()->user()->coin ?? 0) < 100 ? 'disabled' : '' }}
            onclick="return confirm('Upgrade VIP 7 hari dengan 100 koin?')"
            style="width:100%;padding:13px;border-radius:12px;border:none;background:{{ (auth()->user()->coin ?? 0) >= 100 ? 'linear-gradient(135deg,#1a6e35,#27ae60)' : '#e5e7eb' }};color:{{ (auth()->user()->coin ?? 0) >= 100 ? 'white' : '#9ca3af' }};font-weight:700;font-size:14px;cursor:{{ (auth()->user()->coin ?? 0) >= 100 ? 'pointer' : 'not-allowed' }}">
            🪙 {{ (auth()->user()->coin ?? 0) >= 100 ? 'Upgrade dengan 100 Koin' : 'Koin Tidak Cukup ('.(auth()->user()->coin ?? 0).'/100)' }}
        </button>
    </form>

@endif
</div>
</div>

<style>
.scan-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:10000;align-items:center;justify-content:center}
.scan-modal.show{display:flex}
.scan-modal-box{background:#fff;border-radius:16px;padding:24px;width:90%;max-width:420px;max-height:90vh;overflow-y:auto}
.scan-modal-box h4{font-weight:700;color:#1a6e35;margin-bottom:4px}
.scan-modal-box .scan-subtitle{font-size:13px;color:#888;margin-bottom:20px}
.scan-modal-info{background:#f8f9fa;border-radius:10px;padding:14px;margin-bottom:20px}
.scan-modal-info p{font-size:13px;margin-bottom:6px;display:flex;gap:8px}
.scan-modal-info p:last-child{margin-bottom:0}
.scan-modal-info i{color:#1a6e35;width:16px}
.scan-cover{width:60px;height:85px;object-fit:cover;border-radius:6px;border:1px solid #eee}
.scan-btn-primary{width:100%;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:#fff;border:none;border-radius:10px;font-weight:600;font-size:15px;cursor:pointer}
.scan-btn-primary:hover{opacity:.9}
.scan-btn-secondary{width:100%;padding:10px;background:#f0f0f0;color:#333;border:none;border-radius:10px;font-weight:500;margin-top:10px;cursor:pointer}
.scan-manual-input{display:flex;gap:8px}
.scan-manual-input input{flex:1;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px}
.scan-manual-input button{padding:10px 16px;background:#1a6e35;color:#fff;border:none;border-radius:8px;font-weight:600}
.scan-toast{position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#fff;border-radius:12px;padding:14px 20px;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:20000;display:none;min-width:280px;text-align:center}
.scan-toast.show{display:block}
.scan-toast.success{border-left:4px solid #27ae60}
.scan-toast.error{border-left:4px solid #e74c3c}
.scan-label{color:#555}
.scan-date-input{background:#fff;color:#222}
body.dark-mode .scan-modal-box{background:#1e1e1e;color:#e0e0e0}
body.dark-mode .scan-modal-box h4{color:#4ade80}
body.dark-mode .scan-subtitle{color:#aaa}
body.dark-mode .scan-modal-info{background:#2a2a2a}
body.dark-mode .scan-modal-info p{color:#e0e0e0}
body.dark-mode .scan-modal-info i{color:#4ade80}
body.dark-mode .scan-cover{border-color:#444}
body.dark-mode .scan-btn-secondary{background:#2a2a2a;color:#e0e0e0}
body.dark-mode .scan-manual-input input{background:#2a2a2a;border-color:#444;color:#fff}
body.dark-mode .scan-manual-input input::placeholder{color:#888}
body.dark-mode .scan-toast{background:#1e1e1e;color:#e0e0e0}
body.dark-mode .scan-toast.success{border-left-color:#4ade80}
body.dark-mode .scan-toast.error{border-left-color:#e74c3c}
body.dark-mode .scan-label{color:#ccc}
body.dark-mode .scan-date-input{background:#2a2a2a;border-color:#444;color:#fff}
</style>

<div class="scan-modal" id="scanModal">
    <div class="scan-modal-box">
        <h4><i class="bi bi-qr-code-scan"></i> Scan Barcode Buku</h4>
        <p class="scan-subtitle">Arahkan kamera ke barcode ISBN buku</p>
        <div id="scanReader" style="width:100%;border-radius:12px;overflow:hidden;margin-bottom:16px"></div>
        <div id="scanError" style="display:none;text-align:center;padding:20px;color:#e74c3c">
            <i class="bi bi-exclamation-triangle" style="font-size:48px;margin-bottom:10px"></i>
            <p id="scanErrorMsg">Kamera tidak tersedia atau ditolak</p>
            <p id="scanErrorDetail" style="font-size:11px;color:#999;margin-top:8px;word-break:break-all"></p>
        </div>
        <div class="scan-manual-input">
            <input type="text" id="scanManualIsbn" placeholder="Masukkan ISBN manual">
            <button id="scanManualBtn" type="button">Cari</button>
        </div>
        <button class="scan-btn-secondary" id="scanCloseBtn" type="button">Batal</button>
    </div>
</div>

<div class="scan-modal" id="scanPinjamModal">
    <div class="scan-modal-box">
        <h4><i class="bi bi-bookmark-plus"></i> Form Peminjaman</h4>
        <p class="scan-subtitle">Ajukan peminjaman buku</p>
        <div id="scanPinjamContent"></div>
    </div>
</div>

<div class="scan-modal" id="scanKembaliModal">
    <div class="scan-modal-box">
        <h4><i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian</h4>
        <p class="scan-subtitle">Kembalikan buku yang sedang dipinjam</p>
        <div id="scanKembaliContent"></div>
    </div>
</div>

<div class="scan-toast" id="scanToast"></div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
var scanScanner = null;
var scanCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
var scanIsVip = {{ auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at) ? 'true' : 'false' }};

function openScanModal(e) {
    if(e) e.preventDefault();
    scanProcessing = false;
    var modal = document.getElementById('scanModal');
    if(modal) modal.classList.add('show');
    var err = document.getElementById('scanError');
    if(err) err.style.display = 'none';
    var reader = document.getElementById('scanReader');
    if(reader) reader.style.display = 'block';
    var isbn = document.getElementById('scanManualIsbn');
    if(isbn) isbn.value = '';
    scanStartScanner();
}

function scanCloseFn() {
    scanStopScanner();
    var modal = document.getElementById('scanModal');
    if(modal) modal.classList.remove('show');
}

function scanStartScanner() {
    if (scanScanner) return;
    if (typeof Html5Qrcode === 'undefined') {
        console.error('[Scanner] Html5Qrcode library belum dimuat');
        scanShowError('Library scanner gagal dimuat.', 'Pastikan koneksi internet stabil dan muat ulang halaman.');
        return;
    }

    // Cek secure context
    if (!window.isSecureContext) {
        console.error('[Scanner] Halaman tidak berjalan di secure context (HTTPS)');
        scanShowError('Halaman harus dibuka dengan HTTPS.', 'Kamera memerlukan secure context. Pastikan URL menggunakan https:// atau localhost.');
        return;
    }

    // Cek dukungan getUserMedia
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.error('[Scanner] Browser tidak mendukung getUserMedia');
        scanShowError('Browser tidak mendukung akses kamera.', 'Gunakan browser modern seperti Chrome, Firefox, atau Edge.');
        return;
    }

    console.log('[Scanner] Memulai inisialisasi...');
    scanScanner = new Html5Qrcode("scanReader");

    var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    console.log('[Scanner] Device:', isMobile ? 'Mobile' : 'Desktop');

    // Pilih kamera: mobile → belakang, desktop → enumerasi lalu pilih yang tersedia
    var cameraConfig;
    if (isMobile) {
        cameraConfig = { facingMode: "environment" };
        console.log('[Scanner] Menggunakan facingMode: environment (kamera belakang)');
        scanStartWithCamera(cameraConfig);
    } else {
        console.log('[Scanner] Melakukan enumerasi kamera...');
        Html5Qrcode.getCameras().then(function(cameras) {
            console.log('[Scanner] Kamera ditemukan:', cameras.length, cameras);
            if (cameras && cameras.length > 0) {
                // Cari kamera belakang dulu, fallback ke kamera pertama
                var selected = cameras.find(function(c) {
                    return c.label && /back|rear|environment/i.test(c.label);
                }) || cameras[0];
                console.log('[Scanner] Kamera dipilih:', selected.label || selected.id);
                cameraConfig = { deviceId: { exact: selected.id } };
            } else {
                console.warn('[Scanner] Tidak ada kamera ditemukan via enumerasi, fallback facingMode');
                cameraConfig = { facingMode: "environment" };
            }
            scanStartWithCamera(cameraConfig);
        }).catch(function(err) {
            console.warn('[Scanner] Gagal enumerasi kamera:', err.message || err);
            // Fallback: coba langsung dengan facingMode
            cameraConfig = { facingMode: "environment" };
            scanStartWithCamera(cameraConfig);
        });
    }
}

function scanStartWithCamera(cameraConfig) {
    console.log('[Scanner] Memulai kamera dengan config:', JSON.stringify(cameraConfig));
    scanScanner.start(
        cameraConfig,
        { fps: 10, qrbox: 250 },
        function(code) {
            if (scanProcessing) return;
            scanProcessing = true;
            console.log('[Scan] Callback triggered, raw code:', code);
            try { scanScanner.pause(true); } catch(e) {}
            setTimeout(function() { scanStopScanner(); }, 300);
            scanCloseFn();
            scanProcessIsbn(code);
        },
        function() {}
    ).catch(function(err) {
        console.error('[Scanner] Gagal memulai kamera:', err);
        scanStopScanner();
        var msg = String(err.message || err || '');
        var detail = msg;
        if (msg.indexOf('NotAllowedError') !== -1 || msg.indexOf('Permission') !== -1 || msg.indexOf('permission') !== -1 || msg.indexOf('denied') !== -1) {
            scanShowError('Izin kamera ditolak.', 'Berikan izin kamera di pengaturan browser, lalu muat ulang halaman.');
        } else if (msg.indexOf('NotFoundError') !== -1 || msg.indexOf('not found') !== -1 || msg.indexOf('could not be found') !== -1) {
            scanShowError('Kamera tidak ditemukan.', 'Pastikan perangkat memiliki kamera dan tidak sedang digunakan aplikasi lain.');
        } else if (msg.indexOf('NotReadableError') !== -1 || msg.indexOf('in use') !== -1 || msg.indexOf('Could not start') !== -1) {
            scanShowError('Kamera sedang digunakan.', 'Tutup aplikasi lain yang menggunakan kamera (Zoom, Meet, dll), lalu coba lagi.');
        } else if (msg.indexOf('OverconstrainedError') !== -1) {
            scanShowError('Kamera tidak memenuhi syarat.', 'Kamera yang dipilih tidak mendukung resolusi yang diminta.');
        } else {
            scanShowError('Gagal mengakses kamera.', detail);
        }
    });
}

function scanShowError(title, detail) {
    var err = document.getElementById('scanError');
    var errTitle = document.getElementById('scanErrorMsg');
    var errDetail = document.getElementById('scanErrorDetail');
    var reader = document.getElementById('scanReader');
    if (err) err.style.display = 'block';
    if (errTitle) errTitle.textContent = title;
    if (errDetail) errDetail.textContent = detail || '';
    if (reader) reader.style.display = 'none';
}

var scanProcessing = false;

function scanStopScanner() {
    if (!scanScanner) return;
    try {
        if (scanScanner.isScanning) {
            scanScanner.stop().catch(function() {});
        }
    } catch(e) {
        console.warn('[Scanner] stop error (ignored):', e);
    }
    scanScanner = null;
}

function scanManualSearch() {
    var isbn = document.getElementById('scanManualIsbn').value.trim();
    if (!isbn) return;
    scanStopScanner();
    scanCloseFn();
    scanProcessIsbn(isbn);
}

function scanProcessIsbn(isbn) {
    console.log('[Scan] ISBN terbaca:', isbn);
    fetch('/barcode/cek-buku', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': scanCsrf },
        body: JSON.stringify({ kode: isbn })
    })
    .then(function(r) {
        console.log('[Scan] Response status:', r.status);
        return r.json().then(function(data) { return { status: r.status, data: data }; });
    })
    .then(function(result) {
        console.log('[Scan] Response data:', JSON.stringify(result.data));
        if (result.status !== 200 || !result.data.buku || !result.data.buku.id) {
            alert('Buku tidak ditemukan untuk kode: ' + isbn);
            return;
        }
        window.location.href = '/buku/' + result.data.buku.id + '/detail';
    })
    .catch(function(err) {
        console.error('[Scan] Error:', err);
        alert('Gagal mencari buku: ' + err.message);
    });
}

function scanShowPinjam(data) {
    var maxDurasi = scanIsVip ? 14 : 7;
    var today = new Date().toISOString().split('T')[0];
    var minKembali = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    document.getElementById('scanPinjamContent').innerHTML =
        '<div class="scan-modal-info">' +
            '<p><i class="bi bi-person"></i> Peminjam: <strong>{{ auth()->user()->name }}</strong></p>' +
            '<p><i class="bi bi-book"></i> Buku: <strong>' + data.buku.judul + '</strong></p>' +
            '<p><i class="bi bi-star-fill"></i> Status: <strong>' + (scanIsVip ? 'VIP - maks. 6 buku, 14 hari' : 'Reguler - maks. 3 buku, 7 hari') + '</strong></p>' +
        '</div>' +
        (data.buku.sampul ? '<img src="' + data.buku.sampul + '" class="scan-cover" style="margin:0 auto 16px;display:block">' : '') +
        '<div style="margin-bottom:12px">' +
            '<label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="scan-label">Tanggal Pinjam</label>' +
            '<input type="date" id="scanTglPinjam" min="' + today + '" value="' + today + '" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="scan-date-input">' +
        '</div>' +
        '<div style="margin-bottom:16px">' +
            '<label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="scan-label">Tanggal Kembali</label>' +
            '<input type="date" id="scanTglKembali" min="' + minKembali + '" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="scan-date-input">' +
        '</div>' +
        '<div id="scanValidasiMsg" style="color:#e74c3c;font-size:12px;margin-bottom:12px;display:none"></div>' +
        '<button class="scan-btn-primary" onclick="scanSubmitPinjam(' + data.buku.id + ')"><i class="bi bi-check-circle"></i> Ajukan Peminjaman</button>' +
        '<button class="scan-btn-secondary" onclick="document.getElementById(\'scanPinjamModal\').classList.remove(\'show\')">Batal</button>';
    document.getElementById('scanPinjamModal').classList.add('show');
}

function scanSubmitPinjam(bukuId) {
    var tglPinjam = document.getElementById('scanTglPinjam').value;
    var tglKembali = document.getElementById('scanTglKembali').value;
    var maxDurasi = scanIsVip ? 14 : 7;
    var msg = document.getElementById('scanValidasiMsg');
    if (!tglPinjam || !tglKembali) { msg.textContent = 'Tanggal Pinjam dan Tanggal Kembali wajib diisi.'; msg.style.display = 'block'; return; }
    var pinjam = new Date(tglPinjam), kembali = new Date(tglKembali), hariIni = new Date();
    hariIni.setHours(0,0,0,0); pinjam.setHours(0,0,0,0); kembali.setHours(0,0,0,0);
    if (pinjam < hariIni) { msg.textContent = 'Tanggal Pinjam tidak boleh kurang dari hari ini.'; msg.style.display = 'block'; return; }
    if (kembali <= pinjam) { msg.textContent = 'Tanggal Kembali harus lebih besar dari Tanggal Pinjam.'; msg.style.display = 'block'; return; }
    if (Math.ceil((kembali - pinjam) / 86400000) > maxDurasi) { msg.textContent = 'Durasi peminjaman maksimal ' + maxDurasi + ' hari.'; msg.style.display = 'block'; return; }
    msg.style.display = 'none';
    fetch('{{ route("barcode.pinjam") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': scanCsrf },
        body: JSON.stringify({ buku_id: bukuId, tanggal_pinjam: tglPinjam, tanggal_kembali: tglKembali })
    })
    .then(function(r) { return r.json().then(function(data) {
        if (r.status === 422 && data.errors) { scanToastFn('error', Object.values(data.errors).flat().join(', ')); }
        else { document.getElementById('scanPinjamModal').classList.remove('show'); scanToastFn(data.status === 'success' ? 'success' : 'error', data.pesan || 'Terjadi kesalahan'); }
    }); })
    .catch(function(err) { scanToastFn('error', 'Terjadi kesalahan: ' + err.message); });
}

function scanShowKembali(data) {
    document.getElementById('scanKembaliContent').innerHTML =
        '<div class="scan-modal-info">' +
            '<p><i class="bi bi-person"></i> Peminjam: <strong>{{ auth()->user()->name }}</strong></p>' +
            '<p><i class="bi bi-book"></i> Buku: <strong>' + data.buku.judul + '</strong></p>' +
            '<p><i class="bi bi-calendar"></i> Batas kembali: <strong>' + data.peminjaman_aktif.tanggal_kembali + '</strong></p>' +
        '</div>' +
        (data.buku.sampul ? '<img src="' + data.buku.sampul + '" class="scan-cover" style="margin:0 auto 16px;display:block">' : '') +
        '<button class="scan-btn-primary" onclick="scanSubmitKembali(' + data.peminjaman_aktif.id + ')"><i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian</button>' +
        '<button class="scan-btn-secondary" onclick="document.getElementById(\'scanKembaliModal\').classList.remove(\'show\')">Batal</button>';
    document.getElementById('scanKembaliModal').classList.add('show');
}

function scanSubmitKembali(peminjamanId) {
    fetch('{{ route("barcode.kembali") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': scanCsrf },
        body: JSON.stringify({ peminjaman_id: peminjamanId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { document.getElementById('scanKembaliModal').classList.remove('show'); scanToastFn(data.status === 'success' ? 'success' : 'error', data.pesan); })
    .catch(function() { scanToastFn('error', 'Terjadi kesalahan.'); });
}

function scanToastFn(type, msg) {
    var t = document.getElementById('scanToast');
    t.className = 'scan-toast ' + type;
    t.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + msg;
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 4000);
}

document.getElementById('scanCloseBtn').addEventListener('click', scanCloseFn);
document.getElementById('scanManualBtn').addEventListener('click', scanManualSearch);

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('scan-modal')) {
        e.target.classList.remove('show');
        scanStopScanner();
    }
});
</script>
</body>
</html>