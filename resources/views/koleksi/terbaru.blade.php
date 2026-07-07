<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Terbaru - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }
        .navbar { background: white; box-shadow: 0 2px 15px rgba(0,0,0,0.1); padding: 12px 0; position: fixed; width: 100%; top: 0; z-index: 1000; }
        .navbar-brand img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .navbar-brand span { font-size: 13px; font-weight: 700; color: #1a6e35; text-transform: uppercase; line-height: 1.3; }
        .nav-link { color: #333 !important; font-weight: 500; font-size: 14px; padding: 8px 15px !important; transition: color 0.3s; }
        .nav-link:hover { color: #1a6e35 !important; }

        .floating-dropdown { position: absolute; top: calc(100% + 10px); left: 0; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 20px; min-width: 220px; display: none; z-index: 999; animation: fadeDown 0.2s ease; }
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .floating-dropdown.show { display: block; }
        .floating-dropdown h6 { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .genre-list-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; cursor: pointer; transition: background 0.2s; font-size: 13px; color: #333; text-decoration: none; }
        .genre-list-item:hover { background: #f0faf4; color: #1a6e35; }
        .genre-list-item i { color: #1a6e35; width: 18px; }

        /* Genre Dropdown - Redesign */
        .genre-dropdown-wrapper { min-width: 400px; max-width: 500px; }
        @media (max-width: 576px) { .genre-dropdown-wrapper { min-width: 280px; max-width: 95vw; } }
        .genre-dropdown-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #e9ecef; margin-bottom: 12px; }
        .genre-dropdown-header h6 { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin: 0; font-weight: 600; }
        .genre-dropdown-header .badge { font-size: 10px; padding: 4px 8px; border-radius: 20px; background: #e8f5e9; color: #1a6e35; }
        .genre-search-box { position: relative; margin-bottom: 12px; }
        .genre-search-box input { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #e9ecef; border-radius: 10px; font-size: 13px; transition: border-color 0.2s, box-shadow 0.2s; background: #f8f9fa; }
        .genre-search-box input:focus { outline: none; border-color: #1a6e35; box-shadow: 0 0 0 3px rgba(26, 110, 53, 0.1); background: white; }
        .genre-search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: 14px; }
        .genre-dropdown-body { max-height: 50vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #c1dbc8 transparent; }
        .genre-dropdown-body::-webkit-scrollbar { width: 6px; }
        .genre-dropdown-body::-webkit-scrollbar-track { background: transparent; }
        .genre-dropdown-body::-webkit-scrollbar-thumb { background: #c1dbc8; border-radius: 3px; }
        .genre-dropdown-body::-webkit-scrollbar-thumb:hover { background: #1a6e35; }
        .genre-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
        @media (max-width: 576px) { .genre-grid { grid-template-columns: 1fr; } }
        .genre-grid-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; cursor: pointer; transition: all 0.2s; font-size: 13px; color: #495057; text-decoration: none; background: #f8f9fa; border: 1px solid transparent; }
        .genre-grid-item:hover { background: #e8f5e9; border-color: #c8e6c9; color: #1a6e35; transform: translateX(3px); }
        .genre-grid-item.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: transparent; box-shadow: 0 4px 12px rgba(26, 110, 53, 0.3); }
        .genre-grid-item.active i { color: white; }
        .genre-grid-item i { color: #1a6e35; font-size: 16px; width: 20px; text-align: center; }
        .genre-dropdown-footer { padding-top: 12px; margin-top: 12px; border-top: 1px solid #e9ecef; text-align: center; }
        .genre-dropdown-footer a { font-size: 12px; color: #6c757d; text-decoration: none; transition: color 0.2s; }
        .genre-dropdown-footer a:hover { color: #1a6e35; }

        /* Dark mode support */
        body.dark-mode .genre-dropdown-header { border-color: #444; }
        body.dark-mode .genre-search-box input { background: #2d2d2d; border-color: #444; color: #e0e0e0; }
        body.dark-mode .genre-search-box input:focus { border-color: #27ae60; }
        body.dark-mode .genre-search-box i { color: #888; }
        body.dark-mode .genre-grid-item { background: #2d2d2d; color: #e0e0e0; }
        body.dark-mode .genre-grid-item:hover { background: #1a3d1a; border-color: #2d5c2d; color: #4ade80; }
        body.dark-mode .genre-dropdown-body { scrollbar-color: #444 transparent; }
        body.dark-mode .genre-dropdown-footer { border-color: #444; }
        .layanan-dropdown { position: absolute; top: calc(100% + 10px); left: 0; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 20px; min-width: 260px; display: none; z-index: 999; animation: fadeDown 0.2s ease; }
        .layanan-dropdown.show { display: block; }
        .penjaga-card { display: flex; align-items: center; gap: 15px; }
        .penjaga-card img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 3px solid #1a6e35; }
        .penjaga-card h6 { font-weight: 700; color: #222; margin-bottom: 3px; }
        .penjaga-card p { font-size: 12px; color: #666; margin: 0; }
        .profil-avatar { width: 38px; height: 38px; border-radius: 50%; border: 2px solid #1a6e35; cursor: pointer; object-fit: cover; }
        .nav-item { position: relative; }
        .hero { height: 30vh; min-height: 200px; background: url('/images/sekolah.jpg') center/cover no-repeat; position: relative; display: flex; align-items: center; justify-content: center; margin-top: 70px; }
        .hero::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.3); }
        .hero-content { position: relative; text-align: center; color: white; padding: 60px 20px 20px; }
        .hero-content h1 { font-size: 30px; font-weight: 700; margin-bottom: 8px; }
        .hero-content p { font-size: 15px; opacity: 0.85; }
        .page-section { padding: 20px 0 40px; }
        .book-grid { display: flex; flex-wrap: wrap; }
        .book-grid > [class*="col-"] { display: flex; margin-bottom: 0.75rem; }
        .book-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1); height: 100%; display: flex; flex-direction: column; width: 100%; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.12); }
        .book-card-cover { height: 160px; position: relative; overflow: hidden; }
        @media (max-width: 768px) { .book-card-cover { height: 140px; } }
        @media (max-width: 480px) { .book-card-cover { height: 130px; } }
        .book-card-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
        .book-card:hover .book-card-cover img { transform: scale(1.05); }
        .book-card-cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: rgba(255,255,255,0.5); }
        .book-card-body { padding: 8px 10px; display: flex; flex-direction: column; flex: 1; gap: 1px; }
        .book-card-body h5 { font-size: 12px; font-weight: 700; color: #1a1a2e; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2em; line-height: 1.25; }
        .book-card-meta { font-size: 11px; color: #888; margin-bottom: 0; display: flex; align-items: center; gap: 4px; }
        .book-card-genre { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 600; background: #f0f0f0; color: #666; margin-top: 3px; align-self: flex-start; }
        .badge-baru { position: absolute; top: 6px; left: 6px; background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; z-index: 5; }
        .btn-favorit { position: absolute; top: 6px; right: 6px; width: 30px; height: 30px; border-radius: 50%; border: none; background: rgba(255,255,255,0.92); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 5; transition: all 0.2s; }
        .btn-favorit:hover { transform: scale(1.12); }
        .btn-favorit i { font-size: 14px !important; }
        .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .status-ada { background: #d4edda; color: #1a6e35; }
        .status-habis { background: #f8d7da; color: #721c24; }
        .btn-detail { display: block; width: 100%; padding: 8px; background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border: none; border-radius: 10px; font-size: 12px; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.3s; margin-top: auto; }
        .btn-detail:hover { opacity: 0.9; transform: scale(1.02); color: white; }
        .btn-detail-disabled { background: #e9ecef; color: #aaa; cursor: not-allowed; pointer-events: none; }
        .cover-1 { background: linear-gradient(135deg, #4a5568, #718096); }
        .cover-2 { background: linear-gradient(135deg, #2c3e50, #3498db); }
        .cover-3 { background: linear-gradient(135deg, #8e44ad, #e056fd); }
        .cover-4 { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .cover-5 { background: linear-gradient(135deg, #d35400, #e67e22); }
        .cover-6 { background: linear-gradient(135deg, #16a085, #1abc9c); }
        .penerbit-scroll { display: flex; gap: 10px; overflow-x: auto; scroll-behavior: smooth; padding: 4px 0 16px; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .penerbit-scroll::-webkit-scrollbar { display: none; }
        .penerbit-chip { flex-shrink: 0; padding: 10px 22px; border-radius: 50px; border: 2px solid #e0e0e0; background: white; font-size: 13px; font-weight: 600; color: #555; cursor: pointer; transition: all 0.25s; white-space: nowrap; }
        .penerbit-chip:hover { border-color: #27ae60; color: #1a6e35; background: #f0faf4; }
        .penerbit-chip.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: #1a6e35; }
        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 15px; display: block; }
        .empty-state p { color: #aaa; font-size: 15px; }
        .pagination .page-link { color: #1a6e35; border-color: #dee2e6; }
        .pagination .page-item.active .page-link { background: linear-gradient(135deg, #1a6e35, #27ae60); border-color: #1a6e35; color: white; }
        .pagination .page-link:hover { background: #f0faf4; border-color: #1a6e35; }

        @keyframes fadeDownCenter { from { opacity: 0; transform: translate(-50%, -10px); } to { opacity: 1; transform: translate(-50%, 0); } }

        @media (max-width: 991px) { .navbar-brand span { font-size: 11px; } }
        @media (max-width: 768px) {
            .container { padding-left: 14px !important; padding-right: 14px !important; }
            .navbar { padding: 8px 0; }
            .navbar .container-fluid { padding-left: 12px !important; padding-right: 12px !important; }
            .navbar .container-fluid > div { flex-wrap: wrap; gap: 6px; }
            .nav-link { padding: 6px 8px !important; font-size: 12px; }
            .nav-text { display: none; }
            .navbar-brand img { width: 36px !important; height: 36px !important; }
            .navbar-brand span { font-size: 10px; }
            #genreDropdown, #layananDropdown, #profilDropdown { position: fixed; left: 50% !important; right: auto !important; transform: translateX(-50%); width: 92vw; max-width: 320px; max-height: calc(100vh - 80px); overflow-y: auto; animation: fadeDownCenter 0.2s ease; border-radius: 16px; }
            .hero { height: 20vh; min-height: 140px; margin-top: 56px; }
            .hero-content h1 { font-size: 20px; }
            .hero-content p { font-size: 12px; }
            .page-section { padding: 16px 0 30px; }
            .search-box { padding: 14px; margin-top: -16px; border-radius: 12px; }
            .search-input { padding: 12px 16px 12px 42px; font-size: 14px; }
            .penerbit-chip { padding: 8px 16px; font-size: 12px; }
            .pagination { flex-wrap: wrap; gap: 4px; }
            .pagination .page-link { padding: 6px 10px; font-size: 13px; }
            .floating-dropdown, .layanan-dropdown { padding: 14px !important; min-width: auto !important; }
            .penjaga-card img { width: 50px !important; height: 50px !important; }
            .penjaga-card h6 { font-size: 13px; }
            .penjaga-card p { font-size: 11px; }
        }

        @media (max-width: 480px) {
            .page-section { padding: 12px 0 24px; }
        }

        /* SEARCH BOX */
        .search-box { background: white; border-radius: 16px; padding: 20px 25px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); margin-top: -30px; position: relative; z-index: 10; }
        .search-input { border: 2px solid #eee; border-radius: 12px; padding: 14px 20px 14px 48px; font-size: 15px; width: 100%; outline: none; transition: border 0.3s, box-shadow 0.3s; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 16px center; }
        .search-input:focus { border-color: #27ae60; box-shadow: 0 0 0 3px rgba(39,174,96,0.15); }

        /* SCAN MODAL */
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

        /* SEARCH RESULTS */
        #searchResults { display: none; }
        #searchResults.active { display: block; }
        #mainSections.hidden { display: none; }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar, body.dark-mode .book-card, body.dark-mode .floating-dropdown, body.dark-mode .layanan-dropdown { background: #1e1e1e; }
        body.dark-mode .nav-link, body.dark-mode .book-card-body h5 { color: #ffffff !important; }
        body.dark-mode .book-card-meta { color: #bdbdbd; }
        body.dark-mode .penerbit-chip { background: #1e1e1e; border-color: #444; color: #ccc; }
        body.dark-mode .penerbit-chip:hover { border-color: #27ae60; color: #4ade80; }
        body.dark-mode .penerbit-chip.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: #1a6e35; }
        body.dark-mode .book-card-genre { background: #2a2a2a; color: #aaa; }
        body.dark-mode .btn-detail-disabled { background: #333; color: #666; }
        body.dark-mode .pagination .page-link { background: #2a2a2a; border-color: #444; color: #e0e0e0; }
        body.dark-mode .pagination .page-item.active .page-link { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        body.dark-mode .pagination .page-link:hover { background: #3a3a3a; }
        body.dark-mode .genre-list-item:hover { background: #2d2d2d; }
        body.dark-mode #profilDropdown { background: #1e1e1e !important; }
        body.dark-mode #profilDropdown h6, body.dark-mode #profilDropdown p { color: white !important; }
        body.dark-mode #profilDropdown > div[style*="background:#f9f9f9"] { background: #2a2a2a !important; }
        body.dark-mode #profilDropdown button[type="submit"] { background: #333 !important; color: white !important; }
        body.dark-mode .genre-list-item { color: #e0e0e0 !important; }
        body.dark-mode .penjaga-card h6 { color: #ffffff !important; }
        body.dark-mode .penjaga-card p { color: #bdbdbd !important; }
        body.dark-mode .layanan-dropdown p { color: #bdbdbd !important; }
        body.dark-mode #modalVip > div { background: #1e1e1e !important; color: white !important; }
        body.dark-mode #modalVip h5, body.dark-mode #modalVip p, body.dark-mode #modalVip div { color: white !important; }
        body.dark-mode #modalVip div[style*="background:#f8f9fa"] { background: #2a2a2a !important; }
        body.dark-mode #modalVip div[style*="#fff8e1"] { background: linear-gradient(135deg,#2d2412,#3d3118) !important; }
        body.dark-mode #modalVip div[style*="#fff3cd"] { background: linear-gradient(135deg,#2d2412,#3d3118) !important; }
        body.dark-mode #modalVip div[style*="color:#856404"] { color: #fbbf24 !important; }
        body.dark-mode #modalVip div[style*="color:#888"] { color: #d1d5db !important; }
        body.dark-mode span[style*="color:#222"], body.dark-mode h6[style*="color:#222"], body.dark-mode p[style*="color:#555"], body.dark-mode p[style*="color:#666"] { color: #ffffff !important; }
        body.dark-mode .search-box { background: #1e1e1e; }
        body.dark-mode .search-input { background: #2a2a2a; color: white; border-color: #444; }
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
        body.dark-mode .badge-baru { background: linear-gradient(135deg, #1a6e35, #27ae60); }
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
            <a href="{{ route('koleksi.index') }}" class="d-flex align-items-center gap-1 text-decoration-none d-md-none" style="padding:6px 12px;background:rgba(0,0,0,0.5);border-radius:20px;color:white;font-size:12px;font-weight:600">
                <i class="bi bi-arrow-left"></i> Koleksi
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
                                $adminAktif = \App\Models\User::where('role', 'admin')->where('is_on_duty', true)->first();
                                if (!$adminAktif) { $adminAktif = \App\Models\User::where('role', 'admin')->first(); }
                                $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
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
                        <div class="floating-dropdown genre-dropdown-wrapper" id="genreDropdown">
                            <div class="genre-dropdown-header">
                                <h6><i class="bi bi-collection-fill"></i> Pilih Genre</h6>
                                <span class="badge">{{ $genreList->count() }} genre</span>
                            </div>
                            <div class="genre-search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="genreSearchInput" placeholder="Cari genre..." autocomplete="off">
                            </div>
                            <div class="genre-dropdown-body">
                                <div class="genre-grid" id="genreGrid">
                                    <a href="{{ route('koleksi.terbaru', $penerbit ? ['penerbit' => $penerbit] : []) }}" class="genre-grid-item {{ !$genre ? 'active' : '' }}" data-genre="">
                                        <i class="bi bi-grid-fill"></i>
                                        <span>Semua</span>
                                    </a>
                                    @foreach($genreList as $g)
                                    <a href="{{ route('koleksi.terbaru', array_filter(['genre' => $g->id, 'penerbit' => $penerbit])) }}" class="genre-grid-item {{ $genre == $g->id ? 'active' : '' }}" data-genre="{{ strtolower($g->nama) }}">
                                        <i class="bi bi-tag-fill"></i>
                                        <span>{{ $g->nama }}</span>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="{{ route('favorit.index', ['from' => 'koleksi']) }}"><i class="bi bi-heart-fill"></i> <span class="nav-text">Favorit</span></a>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="#" onclick="openScanModal(event)"><i class="bi bi-qr-code-scan"></i> <span class="nav-text">Scan</span></a>
                    </li>
                </ul>
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-moon-fill"></i></button>
                @php
                    $userUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                <a href="{{ route('notifikasi.index') }}" class="btn btn-sm position-relative" title="Notifikasi" style="padding: 6px 10px; background: rgba(26, 110, 53, 0.1); border: none; border-radius: 8px;">
                    <i class="bi bi-bell" style="color: #1a6e35; font-size: 18px;"></i>
                    @if($userUnreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">
                            {{ $userUnreadCount > 99 ? '99+' : $userUnreadCount }}
                        </span>
                    @endif
                </a>
                <div class="nav-item">
                    <a href="#" onclick="toggleProfil(event)">
                      @if(auth()->user()->foto)
                        <img src="{{ asset(auth()->user()->foto) }}" class="profil-avatar" alt="Profil">
                      @else
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=1a6e35&color=fff" class="profil-avatar" alt="Profil">
                      @endif
                    </a>
                    <div class="floating-dropdown" id="profilDropdown" style="right:0;left:auto;min-width:250px">
                        <div style="text-align:center;margin-bottom:15px">
                           @if(auth()->user()->foto)
                            <img src="{{ asset(auth()->user()->foto) }}" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
                           @else
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=1a6e35&color=fff&size=200" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
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
                            {{ $vipAktif ? 'VIP Aktif · '.$sisaHari.' hari lagi' : 'Upgrade VIP' }}
                        </button>
                       <a href="{{ route('profil.index') }}" style="display:block;width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-align:center;text-decoration:none">
                       <i class="bi bi-person"></i> Lihat Profil
                       </a>
                         <form method="POST" action="{{ route('logout') }}" style="margin-top:8px" onsubmit="return confirm('Yakin ingin logout?');">
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
        <h1><i class="bi bi-clock-history"></i> Buku Terbaru</h1>
        <p>Koleksi buku terbaru yang baru ditambahkan ke perpustakaan</p>
    </div>
</div>

<!-- SEARCH BOX -->
<div class="container">
    <div class="search-box">
        <input type="text" id="searchInput" class="search-input" placeholder="Cari judul buku atau pengarang..." value="">
    </div>
</div>

<!-- MAIN SECTIONS (hidden when searching) -->
<div id="mainSections">

<a href="{{ route('koleksi.index') }}" class="d-none d-md-inline-flex" style="position:fixed;top:80px;right:20px;z-index:999;display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:white;text-decoration:none;padding:8px 18px;background:rgba(0,0,0,0.5);border-radius:20px;backdrop-filter:blur(6px);transition:all 0.2s;box-shadow:0 4px 15px rgba(0,0,0,0.2)" onmouseover="this.style.background='rgba(26,110,53,0.85)'" onmouseout="this.style.background='rgba(0,0,0,0.5)'"><i class="bi bi-arrow-left"></i> Koleksi</a>

<!-- PENERBIT FILTER -->
@if($penerbitList->count() > 0)
<div class="container" style="padding-top:24px">
    <div class="penerbit-scroll">
        <a href="{{ route('koleksi.terbaru', $genre ? ['genre' => $genre] : []) }}" class="penerbit-chip {{ !$penerbit ? 'active' : '' }}">Semua</a>
        @foreach($penerbitList as $p)
            <a href="{{ route('koleksi.terbaru', array_filter(['penerbit' => $p->id, 'genre' => $genre])) }}" class="penerbit-chip {{ $penerbit == $p->id ? 'active' : '' }}">{{ $p->nama }}</a>
        @endforeach
    </div>
</div>
@endif

<!-- GRID -->
<div class="page-section">
    <div class="container">
        @if($buku->count() > 0)
        <div class="row g-4 book-grid">
            @foreach($buku as $index => $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="book-card">
                    <div class="book-card-cover cover-{{ (($index + 3) % 6) + 1 }}">
                        <div class="badge-baru">Baru</div>
                        @if($item->sampul)
                            <img src="{{ asset($item->sampul) }}" alt="{{ $item->judul }}">
                        @else
                            <div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>
                        @endif
                        <button type="button" onclick="toggleFavorit({{ $item->id }}, this)" data-favorit="{{ in_array($item->id, $favoritIds ?? []) ? 'true' : 'false' }}" class="btn-favorit">
                            @if(in_array($item->id, $favoritIds ?? []))
                                <i class="bi bi-heart-fill" style="color:#e74c3c"></i>
                            @else
                                <i class="bi bi-heart" style="color:#999"></i>
                            @endif
                        </button>
                    </div>
                    <div class="book-card-body">
                        <h5>{{ $item->judul }}</h5>
                        <p class="book-card-meta"><i class="bi bi-person"></i> {{ $item->pengarang }}</p>
                        @if($item->genre)<span class="book-card-genre">{{ $item->genre }}</span>@endif
                        <div style="margin-top:auto;padding-top:4px">
                            <span class="status-badge {{ $item->stok > 0 ? 'status-ada' : 'status-habis' }}">
                                {{ $item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Dipinjam' }}
                            </span>
                        </div>
                        @if($item->stok > 0)
                        <a href="{{ route('buku.detail', $item->id) }}" class="btn-detail" style="margin-top:6px"><i class="bi bi-eye"></i> Detail</a>
                        @else
                        <a href="{{ route('buku.detail', $item->id) }}" class="btn-detail btn-detail-disabled" style="margin-top:6px"><i class="bi bi-eye"></i> Detail</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Buku terbaru tidak ditemukan{{ $genre ? ' untuk genre '.$genre : '' }}{{ $penerbit ? ' dari penerbit '.$penerbit : '' }}</p>
            @if($genre || $penerbit)
            <a href="{{ route('koleksi.terbaru') }}" class="btn btn-outline-success mt-3" style="border-radius:10px">Lihat Semua Buku Terbaru</a>
            @else
            <a href="{{ route('koleksi.index') }}" class="btn btn-outline-success mt-3" style="border-radius:10px">Lihat Semua Buku</a>
            @endif
        </div>
        @endif
    </div>
</div>

</div><!-- end mainSections -->

<!-- SEARCH RESULTS (shown only when searching) -->
<div id="searchResults" class="container" style="padding:32px 0 60px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <h3 style="font-size:20px;font-weight:800;color:#1a1a2e;display:flex;align-items:center;gap:10px"><i class="bi bi-search" style="color:#1a6e35;font-size:22px"></i> Hasil Pencarian</h3>
        <span id="searchCount" style="font-size:13px;color:#888"></span>
    </div>
    <div class="row g-4 book-grid" id="searchGrid"></div>
    <div id="searchEmpty" class="empty-state" style="display:none">
        <i class="bi bi-search"></i>
        <p>Tidak ada buku yang ditemukan.</p>
    </div>
</div>

{{-- MODAL VIP --}}
<div id="modalVip" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:3000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:24px;padding:30px;width:90%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative">
        <button onclick="document.getElementById('modalVip').style.display='none'" style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:20px;color:#aaa;cursor:pointer">&#10005;</button>
        <div style="text-align:center;margin-bottom:20px">
            <div style="font-size:40px;margin-bottom:8px">&#11088;</div>
            <h5 style="font-weight:800;color:#222;margin:0">Member VIP</h5>
            <p style="font-size:13px;color:#888;margin-top:4px">Akses semua e-book & fitur eksklusif</p>
        </div>
        @if($vipAktif)
        <div style="background:linear-gradient(135deg,#fff8e1,#fff3cd);border-radius:14px;padding:15px;text-align:center;margin-bottom:20px">
            <div style="font-size:13px;color:#856404;font-weight:600">VIP Aktif</div>
            <div style="font-size:12px;color:#888;margin-top:4px">Berakhir: {{ auth()->user()->vip_expired_at->format('d M Y') }}</div>
            <div style="font-size:20px;font-weight:800;color:#f59e0b;margin-top:6px">{{ $sisaHari }} Hari Lagi</div>
        </div>
        @endif
        <div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:20px">
            <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:10px">Keuntungan VIP:</div>
            <div style="font-size:12px;color:#555;line-height:2">
                <div>Akses semua e-book VIP</div>
                <div>Pinjam hingga 6 buku sekaligus</div>
                <div>Durasi pinjam hingga 14 hari</div>
                <div>Badge VIP eksklusif</div>
            </div>
        </div>
        @if(auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at))
        <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">Status VIP:</div>
        <button type="button" disabled style="width:100%;padding:13px;border-radius:12px;border:none;background:#e5e7eb;color:#6b7280;font-weight:700;font-size:14px;cursor:not-allowed">VIP Masih Aktif</button>
        @else
        <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">Upgrade VIP 7 hari — 100 Koin:</div>
        <form action="{{ route('vip.beli') }}" method="POST">
            @csrf
            <button type="submit" {{ (auth()->user()->coin ?? 0) < 100 ? 'disabled' : '' }} onclick="return confirm('Upgrade VIP 7 hari dengan 100 koin?')"
                style="width:100%;padding:13px;border-radius:12px;border:none;background:{{ (auth()->user()->coin ?? 0) >= 100 ? 'linear-gradient(135deg,#1a6e35,#27ae60)' : '#e5e7eb' }};color:{{ (auth()->user()->coin ?? 0) >= 100 ? 'white' : '#9ca3af' }};font-weight:700;font-size:14px;cursor:{{ (auth()->user()->coin ?? 0) >= 100 ? 'pointer' : 'not-allowed' }}">
                {{ (auth()->user()->coin ?? 0) >= 100 ? 'Upgrade dengan 100 Koin' : 'Koin Tidak Cukup ('.(auth()->user()->coin ?? 0).'/100)' }}
            </button>
        </form>
        @endif
    </div>
</div>

{{-- SCAN MODALS --}}
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// DROPDOWNS
function positionDropdown(el) {
    var navbar = document.querySelector('.navbar');
    if (window.innerWidth <= 768) {
        el.style.top = (navbar.offsetHeight + 10) + 'px';
    } else {
        el.style.top = '';
    }
}
function toggleLayanan(e) { e.preventDefault(); var el = document.getElementById('layananDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('genreDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); }
function toggleGenre(e) { e.preventDefault(); var el = document.getElementById('genreDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); if (el.classList.contains('show')) { setTimeout(function() { document.getElementById('genreSearchInput').focus(); }, 100); } }
function toggleProfil(e) { e.preventDefault(); var el = document.getElementById('profilDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('genreDropdown').classList.remove('show'); }
document.addEventListener('click', function(e) { if (!e.target.closest('.nav-item') && !e.target.closest('.profil-avatar')) { document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('genreDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); } });

// Genre search functionality
document.getElementById('genreSearchInput').addEventListener('input', function(e) {
    var query = e.target.value.toLowerCase().trim();
    var items = document.querySelectorAll('#genreGrid .genre-grid-item');
    items.forEach(function(item) {
        var genreName = item.getAttribute('data-genre');
        if (genreName === '' || genreName.includes(query)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
document.getElementById('genreSearchInput').addEventListener('click', function(e) { e.stopPropagation(); });

// FAVORIT
function toggleFavorit(bukuId, btn) {
    fetch('/buku/' + bukuId + '/favorit', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
    .then(function(res) { return res.json(); })
    .then(function(data) { var icon = btn.querySelector('i'); if (data.status === 'added') { icon.className = 'bi bi-heart-fill'; icon.style.color = '#e74c3c'; } else { icon.className = 'bi bi-heart'; icon.style.color = '#999'; } })
    .catch(function() { alert('Gagal mengubah favorit.'); });
}

// DARK MODE
var darkToggle = document.getElementById('darkModeToggle');
if(localStorage.getItem('darkMode') === 'enabled'){ document.body.classList.add('dark-mode'); darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>'; }
darkToggle.addEventListener('click', function() { document.body.classList.toggle('dark-mode'); if(document.body.classList.contains('dark-mode')){ localStorage.setItem('darkMode','enabled'); darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>'; } else { localStorage.setItem('darkMode','disabled'); darkToggle.innerHTML = '<i class="bi bi-moon-fill"></i>'; } });

// SEARCH
var allBooks = @json($buku->values());
var coverColors = ['cover-1','cover-2','cover-3','cover-4','cover-5','cover-6'];
var favoritIds = @json($favoritIds ?? []);
var searchInput = document.getElementById('searchInput');
var mainSections = document.getElementById('mainSections');
var searchResults = document.getElementById('searchResults');
var searchGrid = document.getElementById('searchGrid');
var searchEmpty = document.getElementById('searchEmpty');
var searchCount = document.getElementById('searchCount');
var searchTimeout = null;

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() { performSearch(); }, 200);
});

function performSearch() {
    var q = searchInput.value.trim().toLowerCase();
    if (q === '') {
        mainSections.style.display = '';
        searchResults.style.display = 'none';
        searchGrid.innerHTML = '';
        searchEmpty.style.display = 'none';
        return;
    }
    mainSections.style.display = 'none';
    searchResults.style.display = 'block';

    var results = allBooks.filter(function(b) {
        return b.judul.toLowerCase().indexOf(q) !== -1 ||
               b.pengarang.toLowerCase().indexOf(q) !== -1 ||
               (b.penerbit && b.penerbit.toLowerCase().indexOf(q) !== -1);
    });

    searchGrid.innerHTML = '';
    searchEmpty.style.display = results.length === 0 ? 'block' : 'none';
    searchCount.textContent = results.length + ' buku ditemukan';

    results.forEach(function(item, index) {
        var isFav = favoritIds.indexOf(item.id) !== -1;
        var sampulHtml = item.sampul ? '<img src="' + item.sampul + '" alt="' + item.judul + '">' : '<div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>';
        var badgeBaru = '<div class="badge-baru">Baru</div>';
        var genreHtml = item.genre ? '<span class="book-card-genre">' + item.genre + '</span>' : '';
        var statusClass = item.stok > 0 ? 'status-ada' : 'status-habis';
        var statusText = item.stok > 0 ? 'Tersedia (' + item.stok + ')' : 'Dipinjam';
        var btnHtml = item.stok > 0
            ? '<a href="/buku/' + item.id + '/detail" class="btn-detail" style="margin-top:6px"><i class="bi bi-eye"></i> Detail</a>'
            : '<a href="/buku/' + item.id + '/detail" class="btn-detail btn-detail-disabled" style="margin-top:6px">Tidak Tersedia</a>';
        var favIcon = isFav ? '<i class="bi bi-heart-fill" style="color:#e74c3c"></i>' : '<i class="bi bi-heart" style="color:#999"></i>';

        var html = '<div class="col-6 col-md-4 col-lg-3">' +
            '<div class="book-card">' +
            '<div class="book-card-cover ' + coverColors[(index + 3) % 6] + '">' + badgeBaru + sampulHtml +
            '<button type="button" onclick="toggleFavorit(' + item.id + ', this)" data-favorit="' + (isFav ? 'true' : 'false') + '" class="btn-favorit">' + favIcon + '</button>' +
            '</div>' +
            '<div class="book-card-body">' +
            '<h5>' + item.judul + '</h5>' +
            '<p class="book-card-meta"><i class="bi bi-person"></i> ' + item.pengarang + '</p>' +
            genreHtml +
            '<div style="margin-top:auto;padding-top:4px"><span class="status-badge ' + statusClass + '">' + statusText + '</span></div>' +
            btnHtml +
            '</div></div></div>';
        searchGrid.insertAdjacentHTML('beforeend', html);
    });
}

// SCAN MODAL
var scanScanner = null;
var scanCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
var scanIsVip = {{ auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at) ? 'true' : 'false' }};
var scanProcessing = false;

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
    if (typeof Html5Qrcode === 'undefined') { scanShowError('Library scanner gagal dimuat.', 'Pastikan koneksi internet stabil.'); return; }
    if (!window.isSecureContext) { scanShowError('Halaman harus dibuka dengan HTTPS.', 'Kamera memerlukan secure context.'); return; }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { scanShowError('Browser tidak mendukung akses kamera.', 'Gunakan browser modern.'); return; }
    scanScanner = new Html5Qrcode("scanReader");
    var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if (isMobile) { scanStartWithCamera({ facingMode: "environment" }); }
    else {
        Html5Qrcode.getCameras().then(function(cameras) {
            if (cameras && cameras.length > 0) {
                var selected = cameras.find(function(c) { return c.label && /back|rear|environment/i.test(c.label); }) || cameras[0];
                scanStartWithCamera({ deviceId: { exact: selected.id } });
            } else { scanStartWithCamera({ facingMode: "environment" }); }
        }).catch(function() { scanStartWithCamera({ facingMode: "environment" }); });
    }
}

function scanStartWithCamera(cameraConfig) {
    scanScanner.start(cameraConfig, { fps: 10, qrbox: 250 },
        function(code) { if (scanProcessing) return; scanProcessing = true; try { scanScanner.pause(true); } catch(e) {} setTimeout(function() { scanStopScanner(); }, 300); scanCloseFn(); scanProcessIsbn(code); },
        function() {}
    ).catch(function(err) {
        scanStopScanner();
        var msg = String(err.message || err || '');
        if (msg.indexOf('NotAllowedError') !== -1 || msg.indexOf('Permission') !== -1 || msg.indexOf('denied') !== -1) { scanShowError('Izin kamera ditolak.', 'Berikan izin kamera di pengaturan browser.'); }
        else if (msg.indexOf('NotFoundError') !== -1 || msg.indexOf('not found') !== -1) { scanShowError('Kamera tidak ditemukan.', 'Pastikan perangkat memiliki kamera.'); }
        else { scanShowError('Gagal mengakses kamera.', msg); }
    });
}

function scanShowError(title, detail) {
    var err = document.getElementById('scanError');
    var reader = document.getElementById('scanReader');
    if (err) err.style.display = 'block';
    document.getElementById('scanErrorMsg').textContent = title;
    document.getElementById('scanErrorDetail').textContent = detail || '';
    if (reader) reader.style.display = 'none';
}

function scanStopScanner() { if (!scanScanner) return; try { if (scanScanner.isScanning) { scanScanner.stop().catch(function() {}); } } catch(e) {} scanScanner = null; }

function scanManualSearch() {
    var isbn = document.getElementById('scanManualIsbn').value.trim();
    if (!isbn) return;
    scanStopScanner(); scanCloseFn(); scanProcessIsbn(isbn);
}

function scanProcessIsbn(isbn) {
    fetch('/barcode/cek-buku', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': scanCsrf }, body: JSON.stringify({ kode: isbn }) })
    .then(function(r) { return r.json().then(function(data) { return { status: r.status, data: data }; }); })
    .then(function(result) {
        if (result.status !== 200 || !result.data.buku || !result.data.buku.id) { alert('Buku tidak ditemukan untuk kode: ' + isbn); return; }
        window.location.href = '/buku/' + result.data.buku.id + '/detail';
    })
    .catch(function(err) { alert('Gagal mencari buku: ' + err.message); });
}

function scanToastFn(type, msg) {
    var t = document.getElementById('scanToast');
    t.className = 'scan-toast show ' + type;
    t.textContent = msg;
    setTimeout(function() { t.classList.remove('show'); }, 3000);
}

document.getElementById('scanManualBtn').addEventListener('click', scanManualSearch);
document.getElementById('scanManualIsbn').addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); scanManualSearch(); } });
document.getElementById('scanCloseBtn').addEventListener('click', scanCloseFn);

document.addEventListener('click', function(e) {
    var link = e.target.closest('.btn-detail, .btn-detail-disabled, a[href*="/detail"]');
    if (link) {
        sessionStorage.setItem('koleksiScrollY', window.scrollY);
        sessionStorage.setItem('koleksiFilterUrl', window.location.href);
    }
});
window.addEventListener('load', function() {
    var savedScroll = sessionStorage.getItem('koleksiScrollY');
    if (savedScroll !== null) {
        window.scrollTo(0, parseInt(savedScroll));
        sessionStorage.removeItem('koleksiScrollY');
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<x-crop-modal />
</body>
</html>
