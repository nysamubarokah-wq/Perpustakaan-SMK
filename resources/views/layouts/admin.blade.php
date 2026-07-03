<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; transition: background 0.3s, color 0.3s; }

        /* DARK MODE VARIABLES */
        :root {
            --bg-body: #f5f7fa;
            --bg-card: #ffffff;
            --bg-header: #f8f9fa;
            --text-primary: #222222;
            --text-secondary: #888888;
            --text-muted: #6c757d;
            --border-color: #eeeeee;
            --shadow-card: 0 3px 15px rgba(0,0,0,0.06);
            --bg-admin-info: #ffffff;
            --text-admin-info: #333333;
            --bg-input: #ffffff;
        }

        body.dark-mode {
            --bg-body: #1a1a2e;
            --bg-card: #16213e;
            --bg-header: #0f3460;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0b0;
            --text-muted: #808090;
            --border-color: #2a2a4a;
            --shadow-card: 0 3px 15px rgba(0,0,0,0.3);
            --bg-admin-info: #16213e;
            --text-admin-info: #e0e0e0;
            --bg-input: #16213e;
        }

        body.dark-mode { background: var(--bg-body); color: var(--text-primary); }
        body.dark-mode .main-content { background: var(--bg-body); }
        body.dark-mode .admin-info { background: var(--bg-admin-info); box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        body.dark-mode .admin-info span { color: var(--text-admin-info); }
        body.dark-mode .page-header h1 { color: var(--text-primary); }

        body.dark-mode .sidebar {
            background: linear-gradient(180deg, #0a3d22, #145a32);
        }

        /* Dark mode - common admin page elements */
        body.dark-mode .admin-dynamic-text { color: var(--text-primary) !important; }
        body.dark-mode .admin-back-btn { background: var(--bg-card) !important; color: var(--text-primary) !important; }
        body.dark-mode .card { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        body.dark-mode .card-body { background: var(--bg-card); color: var(--text-primary); }
        body.dark-mode .card-header { background: var(--bg-header); border-color: var(--border-color); color: var(--text-primary); }
        body.dark-mode .card-footer { background: var(--bg-header); border-color: var(--border-color); }
        body.dark-mode .table { color: var(--text-primary); }
        body.dark-mode .table td, body.dark-mode .table th { border-color: var(--border-color); }
        body.dark-mode .table thead th { background: var(--bg-header); color: var(--text-primary); }
        body.dark-mode .table-hover tbody tr:hover { background: rgba(255,255,255,0.04); }
        body.dark-mode .form-control, body.dark-mode .form-select {
            background: var(--bg-input);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
            background: var(--bg-input);
            color: var(--text-primary);
            border-color: #27ae60;
        }
        body.dark-mode .form-label { color: var(--text-primary); }
        body.dark-mode .form-text { color: var(--text-muted); }
        body.dark-mode .input-group-text { background: var(--bg-header); border-color: var(--border-color); color: var(--text-primary); }
        body.dark-mode .modal-content { background: var(--bg-card); color: var(--text-primary); }
        body.dark-mode .modal-header { border-color: var(--border-color); }
        body.dark-mode .modal-footer { border-color: var(--border-color); }
        body.dark-mode .dropdown-menu { background: var(--bg-card); border-color: var(--border-color); }
        body.dark-mode .dropdown-item { color: var(--text-primary); }
        body.dark-mode .dropdown-item:hover { background: var(--bg-header); color: var(--text-primary); }
        body.dark-mode .alert { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        body.dark-mode .pagination .page-link { background: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        body.dark-mode .pagination .page-item.active .page-link { background: #1a6e35; border-color: #1a6e35; }
        body.dark-mode a { color: #4fc3f7; }
        body.dark-mode .text-success { color: #81c784 !important; }
        body.dark-mode .text-danger { color: #ef9a9a !important; }
        body.dark-mode .bg-white { background: var(--bg-card) !important; }
        body.dark-mode .bg-light { background: var(--bg-header) !important; }
        body.dark-mode .shadow-sm, body.dark-mode .shadow { box-shadow: var(--shadow-card) !important; }
        body.dark-mode .border { border-color: var(--border-color) !important; }
        body.dark-mode hr { border-color: var(--border-color); }

        /* Dark mode - global overrides for inline-styled elements */
        body.dark-mode .page-header h1,
        body.dark-mode h1:not([class*="text-"]),
        body.dark-mode h2:not([class*="text-"]),
        body.dark-mode h3:not([class*="text-"]),
        body.dark-mode h4:not([class*="text-"]),
        body.dark-mode h5:not([class*="text-"]),
        body.dark-mode h6:not([class*="text-"]) { color: var(--text-primary) !important; }
        body.dark-mode p { color: var(--text-primary); }
        body.dark-mode label { color: var(--text-primary); }
        body.dark-mode span:not(.badge):not(.status-badge):not(.btn) { color: inherit; }
        body.dark-mode small { color: var(--text-muted); }

        /* Override common inline-styled containers */
        body.dark-mode div[style*="background:white"],
        body.dark-mode div[style*="background: white"],
        body.dark-mode div[style*="background:#fff"],
        body.dark-mode div[style*="background: #fff"],
        body.dark-mode section[style*="background:white"],
        body.dark-mode section[style*="background: white"] {
            background: var(--bg-card) !important;
        }

        body.dark-mode *[style*="color:#222"],
        body.dark-mode *[style*="color: #222"],
        body.dark-mode *[style*="color:#333"],
        body.dark-mode *[style*="color: #333"],
        body.dark-mode *[style*="color:#444"],
        body.dark-mode *[style*="color: #444"],
        body.dark-mode *[style*="color:#555"],
        body.dark-mode *[style*="color: #555"],
        body.dark-mode *[style*="color:#666"],
        body.dark-mode *[style*="color: #666"] {
            color: var(--text-primary) !important;
        }

        body.dark-mode *[style*="color:#888"],
        body.dark-mode *[style*="color: #888"],
        body.dark-mode *[style*="color:#999"],
        body.dark-mode *[style*="color: #999"],
        body.dark-mode *[style*="color:#aaa"],
        body.dark-mode *[style*="color: #aaa"] {
            color: var(--text-secondary) !important;
        }

        body.dark-mode *[style*="background:#f8f9fa"],
        body.dark-mode *[style*="background: #f8f9fa"],
        body.dark-mode *[style*="background:#fafafa"],
        body.dark-mode *[style*="background: #fafafa"],
        body.dark-mode *[style*="background:#f3f4f6"],
        body.dark-mode *[style*="background: #f3f4f6"],
        body.dark-mode *[style*="background:#f0f0f0"],
        body.dark-mode *[style*="background: #f0f0f0"] {
            background: var(--bg-header) !important;
        }

        body.dark-mode *[style*="border:1px solid #eee"],
        body.dark-mode *[style*="border: 1px solid #eee"],
        body.dark-mode *[style*="border:1.5px solid #e5e7eb"],
        body.dark-mode *[style*="border: 1.5px solid #e5e7eb"],
        body.dark-mode *[style*="border:2px solid #e5e7eb"],
        body.dark-mode *[style*="border: 2px solid #e5e7eb"],
        body.dark-mode *[style*="border-bottom:2px solid #e5e7eb"],
        body.dark-mode *[style*="border-bottom: 2px solid #e5e7eb"] {
            border-color: var(--border-color) !important;
        }

        /* Dark mode badge variants */
        body.dark-mode *[style*="background:#e8f5e9"],
        body.dark-mode *[style*="background:#d4edda"] {
            background: #1b4332 !important;
            color: #81c784 !important;
        }
        body.dark-mode *[style*="background:#fff3cd"],
        body.dark-mode *[style*="background:#fef3c7"] {
            background: #3d3200 !important;
            color: #ffd54f !important;
        }
        body.dark-mode *[style*="background:#f8d7da"],
        body.dark-mode *[style*="background:#fee2e2"],
        body.dark-mode *[style*="background:#fef2f2"] {
            background: #4a1a1a !important;
            color: #ef9a9a !important;
        }
        body.dark-mode *[style*="background:#e8f4fd"],
        body.dark-mode *[style*="background:#dbeafe"],
        body.dark-mode *[style*="background:#eff6ff"],
        body.dark-mode *[style*="background:#f0f9ff"] {
            background: #0d3b4f !important;
            color: #80deea !important;
        }
        body.dark-mode *[style*="background:#ede9fe"] {
            background: #2d1b4e !important;
            color: #ce93d8 !important;
        }
        body.dark-mode *[style*="background:#dcfce7"] {
            background: #1b4332 !important;
            color: #81c784 !important;
        }
        body.dark-mode *[style*="background:#d1fae5"] {
            background: #1b4332 !important;
            color: #81c784 !important;
        }

        /* Dark mode override for border colors #f0f0f0 */
        body.dark-mode *[style*="border-bottom:1px solid #f0f0f0"],
        body.dark-mode *[style*="border-top:1px solid #f0f0f0"],
        body.dark-mode *[style*="border:1px solid #f0f0f0"],
        body.dark-mode *[style*="border-bottom:1px solid #f0f0f0"] {
            border-color: var(--border-color) !important;
        }

        /* Dark mode - border #ddd / #d1d5db overrides */
        body.dark-mode *[style*="border:1px solid #ddd"],
        body.dark-mode *[style*="border:1.5px solid #ddd"],
        body.dark-mode *[style*="border:1px solid #d1d5db"] {
            border-color: var(--border-color) !important;
        }

        /* SIDEBAR */
        .sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: 240px;
    background: linear-gradient(180deg, #1a6e35, #27ae60);
    padding: 25px 0;
    z-index: 200;
    transition: transform 0.3s ease;
    overflow-y: auto; /* ← tambah ini */
}

        .sidebar-brand {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            padding: 0 20px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }

        .sidebar-brand img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-brand span {
            font-size: 12px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            line-height: 1.3;
            white-space: nowrap;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 12px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .sidebar-menu li a i { font-size: 18px; width: 20px; }

        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 15px 12px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 150;
        }

        .sidebar-toggle-btn {
            display: none;
            background: #1a6e35;
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            font-size: 18px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* MAIN */
        .main-content {
            margin-left: 240px;
            padding: 30px;
            min-height: 100vh;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #222;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 8px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .admin-info .avatar-init {
            width: 35px;
            height: 35px;
            background: #1a6e35;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .admin-info span { font-size: 13px; font-weight: 600; color: #333; }

        .card-admin-body { overflow-x: auto; }

        /* DARK MODE - cards, tables, badges */
        body.dark-mode .stat-card,
        body.dark-mode .chart-card,
        body.dark-mode .table-card {
            background: var(--bg-card);
            box-shadow: var(--shadow-card);
        }
        body.dark-mode .stat-card h3,
        body.dark-mode .chart-card h5,
        body.dark-mode .table-card-header h5 { color: var(--text-primary); }
        body.dark-mode .stat-card p { color: var(--text-secondary); }
        body.dark-mode .table-card thead { background: var(--bg-header) !important; }
        body.dark-mode .table-card thead th { color: var(--text-primary); border-color: var(--border-color); }
        body.dark-mode .table-card td { color: var(--text-primary); border-color: var(--border-color); }
        body.dark-mode .table-card-header { border-color: var(--border-color); }
        body.dark-mode .table-hover tbody tr:hover { background: rgba(255,255,255,0.05); }
        body.dark-mode .text-muted { color: var(--text-muted) !important; }
        body.dark-mode .badge { opacity: 0.9; }

        /* Dark mode toggle button */
        .dark-mode-toggle {
            background: none;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: rgba(255,255,255,0.8);
            font-size: 16px;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        .dark-mode-toggle:hover {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.5);
            color: white;
        }

        /* Dark mode badge status */
        body.dark-mode .status-dipinjam { background: #3d3200; color: #ffd54f; }
        body.dark-mode .status-dikembalikan { background: #1b4332; color: #81c784; }
        body.dark-mode .status-terlambat { background: #4a1a1a; color: #ef9a9a; }
        body.dark-mode .status-konfirmasi { background: #0d3b4f; color: #80deea; }

        /* Dark mode - konfirmasi-pinjam cards */
        body.dark-mode .kpm-card { background: var(--bg-card); border-color: var(--border-color); }
        body.dark-mode .kpm-user-name { color: var(--text-primary) !important; }
        body.dark-mode .kpm-user-email { color: var(--text-secondary) !important; }
        body.dark-mode .kpm-book-wrap { background: var(--bg-header) !important; }
        body.dark-mode .kpm-book-title { color: var(--text-primary) !important; }
        body.dark-mode .kpm-book-author { color: var(--text-secondary) !important; }
        body.dark-mode .kpm-badge-kode { background: #0d3b4f !important; color: #80deea !important; }
        body.dark-mode .kpm-badge-tersedia { background: #1b4332 !important; color: #81c784 !important; }
        body.dark-mode .kpm-card-meta { color: var(--text-secondary) !important; }
        body.dark-mode .kpm-durasi { background: #0d3b4f !important; color: #80deea !important; }

        /* Dark mode - pengembalian cards */
        body.dark-mode .embali-card { background: var(--bg-card); border-color: var(--border-color); }
        body.dark-mode .embali-card-user { color: var(--text-primary) !important; }
        body.dark-mode .embali-card-book { color: var(--text-secondary) !important; }
        body.dark-mode .embali-card-meta { color: var(--text-secondary) !important; }
        body.dark-mode .embali-card-footer { border-color: var(--border-color) !important; }

        /* Dark mode - denda stat cards */
        body.dark-mode .card-stat { background: var(--bg-card); box-shadow: var(--shadow-card); }
        body.dark-mode .card-stat.danger { border-left-color: #e74c3c; }
        body.dark-mode .card-stat.success { border-left-color: #27ae60; }
        body.dark-mode .icon-stat { background: var(--bg-header) !important; }
        body.dark-mode .table-denda th { background: var(--bg-header) !important; color: var(--text-secondary) !important; }
        body.dark-mode .table-denda td { border-color: var(--border-color); color: var(--text-primary); }
        body.dark-mode .table-denda tbody tr:hover { background: rgba(255,255,255,0.04); }

        /* Dark mode - card-admin (ulasan & others) */
        body.dark-mode .card-admin { background: var(--bg-card); border-radius: 16px; box-shadow: var(--shadow-card); overflow: hidden; }
        body.dark-mode .card-admin-body { padding: 20px 25px; }

        /* Dark mode - scanner page */
        body.dark-mode #reader { border-color: var(--border-color); background: var(--bg-header); }
        body.dark-mode .scan-mode-wrap { background: var(--bg-header); }
        body.dark-mode .scan-mode-btn { color: var(--text-muted); }
        body.dark-mode .scan-mode-btn.active { background: var(--bg-card); color: #1a6e35; }
        body.dark-mode .scan-sampul-ph { background: var(--bg-header); }
        body.dark-mode .scan-form-input { background: var(--bg-input); border-color: var(--border-color); color: var(--text-primary); }
        body.dark-mode .scan-hasil-card { background: var(--bg-card); box-shadow: var(--shadow-card); }
        body.dark-mode .scan-info-box.amber { background: #3d3200; color: #ffd54f; }
        body.dark-mode .scan-btn-lain { background: var(--bg-header); color: var(--text-primary); }
        body.dark-mode #alert-box.success { background: #1b4332; border-color: #2d6a4f; color: #81c784; }
        body.dark-mode #alert-box.error { background: #4a1a1a; border-color: #7f1d1d; color: #ef9a9a; }
        body.dark-mode #alert-box.info { background: #0d3b4f; border-color: #164e63; color: #80deea; }

        /* Dark mode - ebook form card */
        body.dark-mode .ebook-form-card { background: var(--bg-card) !important; box-shadow: var(--shadow-card) !important; }

        /* Dark mode - pagination global */
        body.dark-mode .pagination .page-link {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        body.dark-mode .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            border-color: #1a6e35;
            color: white;
        }
        body.dark-mode .pagination .page-link:hover {
            background: var(--bg-header);
            color: #81c784;
        }

        /* Dark mode - inline input/select/textarea */
        body.dark-mode input[type="text"],
        body.dark-mode input[type="email"],
        body.dark-mode input[type="password"],
        body.dark-mode input[type="number"],
        body.dark-mode input[type="date"],
        body.dark-mode input[type="file"],
        body.dark-mode select,
        body.dark-mode textarea {
            background: var(--bg-input) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        body.dark-mode input::placeholder,
        body.dark-mode textarea::placeholder {
            color: var(--text-muted) !important;
        }

        /* Dark mode scrollbar */
        body.dark-mode ::-webkit-scrollbar { width: 8px; }
        body.dark-mode ::-webkit-scrollbar-track { background: var(--bg-body); }
        body.dark-mode ::-webkit-scrollbar-thumb { background: #3a3a5a; border-radius: 4px; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar-toggle-btn {
                display: flex;
            }
            .page-header h1 {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .admin-info span { display: none; }
            .sidebar-brand { padding: 0 15px 20px; }
            .sidebar-brand img { width: 36px; height: 36px; }
            .sidebar-brand span { font-size: 11px; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo">
        <span>SMK Maarif<br>Walisongo</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.scanner') }}" class="{{ Route::is('admin.scanner*') ? 'active' : '' }}"><i class="bi bi-qr-code-scan"></i> Scan Buku</a></li>
        <hr class="sidebar-divider">
        <li><a href="{{ route('buku.index') }}" class="{{ Route::is('buku.*') ? 'active' : '' }}"><i class="bi bi-book"></i> Kelola Buku</a></li>
        <li><a href="{{ route('anggota.index') }}" class="{{ Route::is('anggota.index') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i> Daftar Siswa</a></li>
        <li><a href="{{ route('anggota.admin') }}" class="{{ Route::is('anggota.admin') ? 'active' : '' }}"><i class="bi bi-shield-check"></i> Daftar Admin</a></li>
        <li><a href="{{ route('peminjaman.index') }}" class="{{ Route::is('peminjaman.*') ? 'active' : '' }}"><i class="bi bi-journal-check"></i> Peminjaman</a></li>
        <li>
    <a href="{{ route('admin.pengembalian.index') }}" class="{{ Route::is('admin.pengembalian.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-counterclockwise"></i> Persetujuan Kembali
        @php
            $pendingReturns = \App\Models\Peminjaman::where(function ($q) {
                    $q->where('status', 'menunggu_pengembalian')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'menunggu_konfirmasi')
                             ->where('tipe_konfirmasi', 'kembali');
                      });
                })->count();
        @endphp
        @if($pendingReturns > 0)
            <span style="background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px">{{ $pendingReturns }}</span>
        @endif
    </a>
</li>
        <li><a href="{{ route('background.index') }}" class="{{ Route::is('background.*') ? 'active' : '' }}"><i class="bi bi-image"></i> Kelola Background</a></li>
        <li><a href="{{ route('admin.ulasan.index') }}" class="{{ Route::is('admin.ulasan.*') ? 'active' : '' }}"><i class="bi bi-star-fill"></i> Kelola Ulasan</a></li>
<li>
    <a href="{{ route('admin.pinjam.index') }}" class="{{ Route::is('admin.pinjam.*') ? 'active' : '' }}">
        <i class="bi bi-bookmark-check"></i> Konfirmasi Pinjam
        @php
            $pendingBorrows = \App\Models\Peminjaman::where('status', 'menunggu_konfirmasi')
                ->where('tipe_konfirmasi', 'pinjam')->count();
        @endphp
        @if($pendingBorrows > 0)
            <span style="background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px">{{ $pendingBorrows }}</span>
        @endif
    </a>
</li>
<li>
    <a href="{{ route('admin.ebook.index') }}" class="{{ Route::is('admin.ebook.*') ? 'active' : '' }}">
        <i class="bi bi-book-half"></i> Kelola E-book
    </a>
</li>
<li>
    <a href="{{ route('admin.vip.index') }}" class="{{ Route::is('admin.vip.*') ? 'active' : '' }}">
        <i class="bi bi-star-fill"></i> Kelola VIP
    </a>
</li>
<li>
    <a href="{{ route('admin.denda.index') }}" class="{{ Route::is('admin.denda.*') ? 'active' : '' }}">
        <i class="bi bi-currency-dollar"></i> Kelola Denda
    </a>
</li>
        <hr class="sidebar-divider">
        <li>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin logout?');">
                @csrf
                <button type="submit" style="background:none;border:none;width:100%;text-align:left;padding:0">
                    <a style="color:rgba(255,255,255,0.8);cursor:pointer"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h1>@yield('header_title') @yield('title')</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode" id="darkModeBtn">
                <i class="bi bi-moon-fill"></i>
            </button>
            <div class="admin-info">
                @if(auth()->user()->foto)
                    <img src="{{ asset(auth()->user()->foto) }}" alt="Foto"
                         style="width:35px;height:35px;border-radius:50%;object-fit:cover;flex-shrink:0">
                @else
                    <div class="avatar-init">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                @endif
                <span>{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('adminDarkMode', isDark ? '1' : '0');
    updateDarkModeIcon(isDark);
}

function updateDarkModeIcon(isDark) {
    const btn = document.getElementById('darkModeBtn');
    if (btn) {
        btn.innerHTML = isDark ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
    }
}

// Apply saved dark mode on load
(function() {
    const saved = localStorage.getItem('adminDarkMode');
    if (saved === '1') {
        document.body.classList.add('dark-mode');
        document.addEventListener('DOMContentLoaded', function() {
            updateDarkModeIcon(true);
        });
    }
})();
</script>
@stack('scripts')

<script>
// Simpan scroll position sidebar sebelum pindah halaman
document.querySelectorAll('.sidebar-menu li a').forEach(link => {
    link.addEventListener('click', function() {
        sessionStorage.setItem('sidebarScroll', document.getElementById('adminSidebar').scrollTop);
        sessionStorage.setItem('mainScroll', window.scrollY);
    });
});

// Restore scroll position setelah halaman dimuat
window.addEventListener('load', function() {
    // Restore sidebar scroll
    const sidebarScroll = sessionStorage.getItem('sidebarScroll');
    if (sidebarScroll !== null) {
        document.getElementById('adminSidebar').scrollTop = parseInt(sidebarScroll);
        sessionStorage.removeItem('sidebarScroll');
    }

    // Restore main content scroll
    const mainScroll = sessionStorage.getItem('mainScroll');
    if (mainScroll !== null) {
        window.scrollTo(0, parseInt(mainScroll));
        sessionStorage.removeItem('mainScroll');
    }
});
</script>
@yield('style')
@yield('script')
</body>
</html>