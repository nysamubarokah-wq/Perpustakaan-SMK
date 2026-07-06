<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Koleksi Buku - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
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
        .navbar-brand img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .navbar-brand span { font-size: 13px; font-weight: 700; color: #1a6e35; text-transform: uppercase; line-height: 1.3; }
        .nav-link { color: #333 !important; font-weight: 500; font-size: 14px; padding: 8px 15px !important; transition: color 0.3s; }
        .nav-link:hover { color: #1a6e35 !important; }


        /* Dropdown ngambang - Genre */
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

        /* Layanan dropdown */
        .layanan-dropdown { position: absolute; top: calc(100% + 10px); left: 0; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 20px; min-width: 260px; display: none; z-index: 999; animation: fadeDown 0.2s ease; }
        .layanan-dropdown.show { display: block; }
        .penjaga-card { display: flex; align-items: center; gap: 15px; }
        .penjaga-card img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 3px solid #1a6e35; }
        .penjaga-card h6 { font-weight: 700; color: #222; margin-bottom: 3px; }
        .penjaga-card p { font-size: 12px; color: #666; margin: 0; }

        /* Profil avatar */
        .profil-avatar { width: 38px; height: 38px; border-radius: 50%; border: 2px solid #1a6e35; cursor: pointer; object-fit: cover; }
        .nav-item { position: relative; }

        /* HERO */
        .hero { height: 40vh; min-height: 260px; background: url('/images/sekolah.jpg') center/cover no-repeat; position: relative; display: flex; align-items: center; justify-content: center; margin-top: 70px; }
        .hero::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.4); }
        .hero-content { position: relative; text-align: center; color: white; padding: 60px 20px 20px; }
        .hero-content h1 { font-size: 32px; font-weight: 700; margin-bottom: 8px; }
        .hero-content p { font-size: 15px; opacity: 0.85; }

        /* SEARCH BOX */
        .search-box { background: white; border-radius: 16px; padding: 20px 25px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); margin-top: -30px; position: relative; z-index: 10; }
        .search-input { border: 2px solid #eee; border-radius: 12px; padding: 14px 20px 14px 48px; font-size: 15px; width: 100%; outline: none; transition: border 0.3s, box-shadow 0.3s; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 16px center; }
        .search-input:focus { border-color: #27ae60; box-shadow: 0 0 0 3px rgba(39,174,96,0.15); }

        /* SECTION STYLES */
        .koleksi-section { padding: 32px 0 20px; }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .section-title { font-size: 20px; font-weight: 800; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
        .section-title i { color: #1a6e35; font-size: 22px; }
        .section-link { font-size: 13px; font-weight: 600; color: #1a6e35; text-decoration: none; display: flex; align-items: center; gap: 4px; transition: gap 0.2s; }
        .section-link:hover { gap: 8px; color: #14863e; }

        /* PENERBIT CHIPS */
        .penerbit-scroll { display: flex; gap: 10px; overflow-x: auto; scroll-behavior: smooth; padding: 4px 0 12px; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .penerbit-scroll::-webkit-scrollbar { display: none; }
        .penerbit-chip { flex-shrink: 0; padding: 10px 22px; border-radius: 50px; border: 2px solid #e0e0e0; background: white; font-size: 13px; font-weight: 600; color: #555; cursor: pointer; transition: all 0.25s; white-space: nowrap; }
        .penerbit-chip:hover { border-color: #27ae60; color: #1a6e35; background: #f0faf4; }
        .penerbit-chip.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: #1a6e35; box-shadow: 0 4px 15px rgba(26,110,53,0.3); }

        /* BOOK CARD */
        .book-grid { display: flex; flex-wrap: wrap; }
        .book-grid > [class*="col-"] { display: flex; margin-bottom: 1rem; }

        /* CAROUSEL - fixed compact widths */
        .carousel-scroll { display: flex; gap: 12px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; padding: 4px 0 16px; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .carousel-scroll::-webkit-scrollbar { display: none; }
        .carousel-scroll .book-card-wrapper { scroll-snap-align: start; flex-shrink: 0; width: 165px; }

        .book-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1); height: 100%; display: flex; flex-direction: column; position: relative; width: 100%; }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .book-card-cover { height: 220px; position: relative; overflow: hidden; }
        .book-card-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
        .book-card:hover .book-card-cover img { transform: scale(1.05); }
        .book-card-cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 50px; color: rgba(255,255,255,0.5); }
        .book-card-body { padding: 12px; display: flex; flex-direction: column; flex: 1; gap: 2px; }
        .book-card-body h5 { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.2em; line-height: 1.3; }
        .book-card-meta { font-size: 12px; color: #888; margin-bottom: 0; display: flex; align-items: center; gap: 5px; }
        .book-card-genre { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; background: #f0f0f0; color: #666; margin-top: 4px; align-self: flex-start; }

        /* Badges */
        .badge-trending { position: absolute; top: 10px; left: 10px; background: linear-gradient(135deg, #ff5722, #ff9800); color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; z-index: 5; box-shadow: 0 2px 8px rgba(255,87,34,0.4); }
        .badge-vip { position: absolute; top: 10px; left: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; z-index: 5; }
        .btn-favorit { position: absolute; top: 10px; right: 10px; width: 34px; height: 34px; border-radius: 50%; border: none; background: rgba(255,255,255,0.92); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.15); z-index: 5; transition: all 0.2s; }
        .btn-favorit:hover { transform: scale(1.15); background: white; }

        /* Status & Buttons */
        .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-ada { background: #d4edda; color: #1a6e35; }
        .status-habis { background: #f8d7da; color: #721c24; }
        .btn-detail { display: block; width: 100%; padding: 11px; background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border: none; border-radius: 12px; font-size: 13px; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.3s; margin-top: auto; }
        .btn-detail:hover { opacity: 0.9; transform: scale(1.02); color: white; }
        .btn-detail-disabled { background: #e9ecef; color: #aaa; cursor: not-allowed; pointer-events: none; }

        /* Cover colors */
        .cover-1 { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        .cover-2 { background: linear-gradient(135deg, #2c3e50, #3498db); }
        .cover-3 { background: linear-gradient(135deg, #8e44ad, #e056fd); }
        .cover-4 { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .cover-5 { background: linear-gradient(135deg, #d35400, #e67e22); }
        .cover-6 { background: linear-gradient(135deg, #16a085, #1abc9c); }

        /* GRID SEMUA BUKU */
        .grid-card { transition: all 0.35s cubic-bezier(0.25,0.8,0.25,1); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 15px; display: block; }
        .empty-state p { color: #aaa; font-size: 15px; }

        /* SEARCH RESULTS SECTION */
        #searchResults { display: none; }
        #searchResults.active { display: block; }
        #mainSections.hidden { display: none; }

        /* PAGINATION */
        .pagination .page-link { color: #1a6e35; border-color: #dee2e6; }
        .pagination .page-item.active .page-link { background: linear-gradient(135deg, #1a6e35, #27ae60); border-color: #1a6e35; color: white; }
        .pagination .page-link:hover { background: #f0faf4; border-color: #1a6e35; }

        /* Fade transition */
        .fade-section { animation: fadeInSection 0.3s ease; }
        @keyframes fadeInSection { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ============================================
           RESPONSIVE BREAKPOINTS
           ============================================ */
        @keyframes fadeDownCenter { from { opacity: 0; transform: translate(-50%, -10px); } to { opacity: 1; transform: translate(-50%, 0); } }

        @media (max-width: 991px) {
            .navbar-brand span { font-size: 11px; }
            .rekom-slide { min-height: 320px; }
            .rekom-cover { width: 150px; height: 215px; }
            .rekom-content { gap: 18px; padding: 20px 24px; }
            .rekom-judul { font-size: 24px; margin-bottom: 5px; }
            .rekom-pengarang { font-size: 13px; margin-bottom: 6px; }
            .rekom-btn-detail { padding: 5px 12px; font-size: 11px; }
        }

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

            .hero { height: 28vh; min-height: 180px; margin-top: 56px; }
            .hero-content h1 { font-size: 20px; }
            .hero-content p { font-size: 12px; padding: 0 16px; }

            .rekom-hero { margin-top: 90px !important; overflow: hidden; }
            .rekom-carousel { overflow: hidden; touch-action: pan-y; }
            .rekom-track { transition: transform 0.4s cubic-bezier(0.25,0.8,0.25,1); }
            .rekom-slide { min-height: 240px; max-height: 260px; }
            .rekom-bg { filter: brightness(0.55) !important; }
            .rekom-overlay { background: rgba(0,0,0,0.65) !important; }
            .rekom-content { flex-direction: row !important; align-items: center; gap: 10px; padding: 14px 12px; width: 100%; box-sizing: border-box; }
            .rekom-cover { flex: 0 0 auto; width: 95px; height: 140px; border-radius: 8px; box-shadow: 0 4px 14px rgba(0,0,0,0.4); overflow: hidden; }
            .rekom-cover img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
            .rekom-cover-placeholder { font-size: 36px; }
            .rekom-info { flex: 1; min-width: 0; overflow: hidden; align-items: flex-start; text-align: left; }
            .rekom-judul { font-size: 18px; font-weight: 700; line-height: 1.2; margin-bottom: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word; overflow-wrap: break-word; }
            .rekom-meta-line { justify-content: flex-start; margin-bottom: 2px; gap: 4px; flex-wrap: wrap; }
            .rekom-genre-badge { font-size: 8px; padding: 2px 6px; }
            .rekom-vip-badge { font-size: 7px; padding: 1px 5px; }
            .rekom-pengarang { font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 6px; display: block !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
            .rekom-btns { display: flex !important; gap: 4px; }
            .rekom-btn-detail { padding: 5px 10px; font-size: 10px; border-radius: 6px; gap: 3px; }
            .rekom-arrow { display: none !important; }
            .rekom-dots { bottom: 8px; gap: 5px; }
            .rekom-dot { width: 7px; height: 7px; }
            .rekom-dot.active { width: 18px; }

            .search-box { padding: 14px; margin-top: -14px; border-radius: 12px; }
            .search-input { padding: 12px 16px 12px 42px; font-size: 14px; }

            .koleksi-section { padding: 20px 0 12px; }
            .section-title { font-size: 17px; }
            .section-link { font-size: 12px; }

            .penerbit-chip { padding: 8px 16px; font-size: 12px; }

            .book-card-cover { height: 170px; }
            .book-card-body { padding: 12px; }
            .book-card-body h5 { font-size: 13px; min-height: 2.2em; }
            .book-card-meta { font-size: 11px; }

            .carousel-scroll { gap: 10px; }
            .carousel-scroll .book-card-wrapper { width: 165px; }

            .penjaga-card img { width: 50px !important; height: 50px !important; }
            .penjaga-card h6 { font-size: 13px; }
            .penjaga-card p { font-size: 11px; }
            .floating-dropdown, .layanan-dropdown { padding: 14px !important; min-width: auto !important; }
            .pagination { flex-wrap: wrap; gap: 4px; }
            .pagination .page-link { padding: 6px 10px; font-size: 13px; }

            #notifTerlambat, #notifH1, #notifH2 { left: 50% !important; right: auto !important; transform: translateX(-50%); max-width: calc(100vw - 32px) !important; width: calc(100vw - 32px); border-radius: 12px; padding: 12px 14px; font-size: 13px; }
            #modalPeraturan { padding: 12px !important; }
            #modalPeraturan > div { border-radius: 16px !important; max-height: 90vh !important; }
            #modalPeraturan > div > div:first-child { padding: 18px !important; }
            #modalPeraturan > div > div:first-child div:first-child { font-size: 28px !important; }
            #modalPeraturan > div > div:first-child div:nth-child(2) { font-size: 15px !important; }
            #modalPeraturan > div > div:nth-child(2) { padding: 14px 16px !important; }
            #modalPeraturan > div > div:last-child { padding: 14px 16px !important; }
            #modalVip > div { width: 94% !important; padding: 20px !important; border-radius: 18px !important; }
            .scan-modal-box { width: 94% !important; padding: 18px !important; border-radius: 14px !important; }
            .scan-manual-input { flex-direction: column; }
            .scan-manual-input button { width: 100%; }
        }

        @media (max-width: 480px) {
            .navbar .container-fluid { padding-left: 8px !important; padding-right: 8px !important; }
            .navbar-brand img { width: 30px !important; height: 30px !important; }
            .navbar-brand span { font-size: 9px; }
            .nav-link { padding: 5px 6px !important; font-size: 11px; }
            .hero { height: 22vh; min-height: 140px; margin-top: 50px; }
            .hero-content h1 { font-size: 17px; }
            .hero-content p { font-size: 11px; padding: 0 10px; }

            .rekom-hero { margin-top: 82px !important; }
            .rekom-slide { min-height: 220px; max-height: 240px; }
            .rekom-content { gap: 8px; padding: 12px 10px; }
            .rekom-cover { width: 90px; height: 130px; border-radius: 7px; }
            .rekom-cover-placeholder { font-size: 32px; }
            .rekom-judul { font-size: 16px; margin-bottom: 2px; }
            .rekom-meta-line { margin-bottom: 2px; gap: 3px; }
            .rekom-genre-badge { font-size: 7px; padding: 1px 5px; }
            .rekom-vip-badge { font-size: 7px; padding: 1px 4px; }
            .rekom-pengarang { font-size: 11px; margin-bottom: 4px; }
            .rekom-btns { gap: 3px; }
            .rekom-btn-detail { padding: 4px 8px; font-size: 9px; border-radius: 5px; }
            .rekom-dots { bottom: 6px; gap: 4px; }
            .rekom-dot { width: 6px; height: 6px; }
            .rekom-dot.active { width: 16px; }

            .search-box { padding: 10px; margin-top: -10px; }
            .search-input { padding: 10px 14px 10px 38px; font-size: 13px; }
            .koleksi-section { padding: 14px 0 8px; }
            .book-card-cover { height: 140px; }
            .book-card-body { padding: 10px; }
            .book-card-body h5 { font-size: 12px; min-height: 2em; }
            .book-card-meta { font-size: 10px; }
            .carousel-scroll { gap: 8px; }
            .carousel-scroll .book-card-wrapper { width: 155px; }
            #genreDropdown, #layananDropdown, #profilDropdown { width: 96vw; max-width: none; }
        }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar, body.dark-mode .search-box, body.dark-mode .book-card, body.dark-mode .floating-dropdown, body.dark-mode .layanan-dropdown { background: #1e1e1e; color: #e0e0e0; }
        body.dark-mode .nav-link, body.dark-mode .genre-list-item, body.dark-mode .book-card-body h5, body.dark-mode h5, body.dark-mode h6, body.dark-mode .section-title { color: #ffffff !important; }
        body.dark-mode .search-input { background: #2a2a2a; color: white; border-color: #444; }
        body.dark-mode .book-card-meta { color: #bdbdbd; }
        body.dark-mode .search-box { box-shadow: 0 5px 25px rgba(255,255,255,0.05); }
        body.dark-mode .genre-list-item:hover { background: #2d2d2d; }
        body.dark-mode .penerbit-chip { background: #1e1e1e; border-color: #444; color: #ccc; }
        body.dark-mode .penerbit-chip:hover { border-color: #27ae60; color: #4ade80; background: #1a2e1a; }
        body.dark-mode .penerbit-chip.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: #1a6e35; }
        body.dark-mode .book-card-genre { background: #2a2a2a; color: #aaa; }
        body.dark-mode .btn-detail-disabled { background: #333; color: #666; }
        body.dark-mode .pagination .page-link { background: #2a2a2a; border-color: #444; color: #e0e0e0; }
        body.dark-mode .pagination .page-item.active .page-link { background: linear-gradient(135deg, #1a6e35, #27ae60); border-color: #1a6e35; }
        body.dark-mode .pagination .page-link:hover { background: #3a3a3a; }

        body.dark-mode { background: #121212; color: white; transition: all .3s ease; }
        body.dark-mode .navbar, body.dark-mode .detail-card, body.dark-mode .profil-card, body.dark-mode .riwayat-card, body.dark-mode .modal-box { background: #1e1e1e; transition: all .3s ease; }
        body.dark-mode form[action*="favorit"] button { background: rgba(40,40,40,0.9) !important; }
        body.dark-mode span[style*="color:#222"], body.dark-mode h5[style*="color:#222"], body.dark-mode h6[style*="color:#222"], body.dark-mode p[style*="color:#555"], body.dark-mode p[style*="color:#666"], body.dark-mode div[style*="color:#444"] { color: #ffffff !important; }
        body.dark-mode a { color: #e0e0e0; }
        body.dark-mode #profilDropdown { background: #1e1e1e !important; color: white !important; }
        body.dark-mode #profilDropdown h6, body.dark-mode #profilDropdown p { color: white !important; }
        body.dark-mode #profilDropdown > div[style*="background:#f9f9f9"] { background: #2a2a2a !important; }
        body.dark-mode #profilDropdown button[type="submit"] { background: #333 !important; color: white !important; }
        body.dark-mode #modalVip > div { background: #1e1e1e !important; color: white !important; }
        body.dark-mode #modalVip h5, body.dark-mode #modalVip p, body.dark-mode #modalVip div { color: white !important; }
        body.dark-mode #modalVip div[style*="background:#f8f9fa"] { background: #2a2a2a !important; }
        body.dark-mode #modalVip button[onclick*="modalVip"] { color: #ddd !important; }
        body.dark-mode #modalVip div[style*="#fff8e1"] { background: linear-gradient(135deg,#2d2412,#3d3118) !important; }
        body.dark-mode #modalVip div[style*="#fff3cd"] { background: linear-gradient(135deg,#2d2412,#3d3118) !important; }
        body.dark-mode #modalVip div[style*="color:#856404"] { color: #fbbf24 !important; }
        body.dark-mode #modalVip div[style*="color:#888"] { color: #d1d5db !important; }
        body.dark-mode #modalPeraturan > div { background:#1e1e1e !important; }
        body.dark-mode #modalPeraturan div[style*="overflow-y:auto"] { background:#1e1e1e !important; }
        body.dark-mode #modalPeraturan div[style*="background:#fafafa"] { background:#252525 !important; border-top:1px solid #444 !important; }
        body.dark-mode #modalPeraturan p { color:#d1d5db !important; }
        body.dark-mode #modalPeraturan div[style*="color:#444"] { color:#f3f4f6 !important; }
        body.dark-mode #modalPeraturan div[style*="color:#666"] { color:#d1d5db !important; }
        body.dark-mode #modalPeraturan div[style*="color:#888"] { color:#cbd5e1 !important; }

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

        /* ===== REKOMENDASI CAROUSEL ===== */
        .rekom-hero { position: relative; }
        .rekom-carousel { position: relative; overflow: hidden; }
        .rekom-track { display: flex; transition: transform 0.6s cubic-bezier(0.25,0.8,0.25,1), opacity 0.4s ease; }
        .rekom-slide { min-width: 100%; position: relative; display: flex; align-items: center; min-height: 340px; }
        .rekom-bg { position: absolute; inset: 0; background-size: cover; background-position: center; filter: brightness(0.6); z-index: 0; }
        .rekom-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); z-index: 1; }
        .rekom-content { position: relative; z-index: 2; display: flex; align-items: center; gap: 20px; padding: 22px 28px; width: 100%; box-sizing: border-box; }
        .rekom-cover { flex-shrink: 0; width: clamp(80px, 22vw, 160px); aspect-ratio: 7/10; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .rekom-cover img { width: 100%; height: 100%; object-fit: cover; }
        .rekom-cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: clamp(28px, 5vw, 50px); color: rgba(255,255,255,0.4); }
        .rekom-info { flex: 1; min-width: 0; color: white; display: flex; flex-direction: column; overflow: hidden; }
        .rekom-judul { font-size: clamp(16px, 3.5vw, 28px); font-weight: 800; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: clamp(3px, 0.5vw, 6px); word-break: break-word; overflow-wrap: break-word; -webkit-box-orient: vertical; }
        .rekom-meta-line { display: flex; align-items: center; gap: clamp(4px, 0.8vw, 6px); flex-wrap: wrap; margin-bottom: clamp(3px, 0.5vw, 6px); }
        .rekom-genre-badge { display: inline-flex; align-items: center; gap: 3px; padding: clamp(1px, 0.3vw, 2px) clamp(6px, 1.2vw, 10px); border-radius: 20px; font-size: clamp(8px, 1.3vw, 10px); font-weight: 600; background: rgba(39,174,96,0.25); color: #4ade80; border: 1px solid rgba(39,174,96,0.4); }
        .rekom-vip-badge { display: inline-flex; align-items: center; gap: 3px; padding: clamp(1px, 0.3vw, 2px) clamp(5px, 1vw, 8px); border-radius: 20px; font-size: clamp(7px, 1.2vw, 9px); font-weight: 700; background: linear-gradient(135deg,#f59e0b,#d97706); color: white; }
        .rekom-pengarang { font-size: clamp(11px, 1.8vw, 14px); color: rgba(255,255,255,0.7); margin-bottom: clamp(4px, 0.8vw, 8px); display: block !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
        .rekom-btns { display: flex; gap: clamp(4px, 0.8vw, 6px); }
        .rekom-btn-detail { padding: clamp(4px, 0.8vw, 6px) clamp(10px, 1.8vw, 14px); background: linear-gradient(135deg,#1a6e35,#27ae60); color: white; border: none; border-radius: 8px; font-size: clamp(10px, 1.5vw, 12px); font-weight: 600; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .rekom-btn-detail:hover { opacity: 0.9; color: white; }

        .rekom-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 5; width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.25s; font-size: 18px; }
        .rekom-arrow:hover { background: rgba(255,255,255,0.3); }
        .rekom-arrow-left { left: 12px; }
        .rekom-arrow-right { right: 12px; }

        .rekom-dots { position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 5; }
        .rekom-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.3); cursor: pointer; transition: all 0.3s; border: none; }
        .rekom-dot.active { background: linear-gradient(135deg,#1a6e35,#27ae60); width: 26px; border-radius: 5px; }

    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="<?php echo e(route('dashboard')); ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?php echo e(asset('images/logo.jpg')); ?>" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <ul class="navbar-nav flex-row gap-1 mb-0">
                    <li class="nav-item">
                       <a class="nav-link" href="<?php echo e(route('dashboard')); ?>"><i class="bi bi-house"></i> <span class="nav-text">Home</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleLayanan(event)">
                            <i class="bi bi-person-workspace"></i> <span class="nav-text">Layanan</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
                        </a>
                        <div class="layanan-dropdown" id="layananDropdown">
                            <p style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Penjaga Perpustakaan</p>
                            <?php
                                $adminAktif = \App\Models\User::where('role', 'admin')->where('is_on_duty', true)->first();
                                if (!$adminAktif) { $adminAktif = \App\Models\User::where('role', 'admin')->first(); }
                                $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
                                $noHpAdmin = $anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '';
                                $waLink = $noHpAdmin ? 'https://wa.me/62' . ltrim(preg_replace('/[^0-9]/', '', $noHpAdmin), '0') : '#';
                            ?>
                            <div class="penjaga-card">
                                <?php if($adminAktif?->foto): ?>
                                    <img src="<?php echo e(asset($adminAktif->foto)); ?>" alt="Penjaga" style="width:65px;height:65px;border-radius:50%;object-fit:cover;border:3px solid #1a6e35">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($adminAktif?->name ?? 'Admin')); ?>&background=1a6e35&color=fff" alt="Penjaga">
                                <?php endif; ?>
                                <div>
                                    <h6><?php echo e($adminAktif?->name ?? 'Nama Penjaga'); ?></h6>
                                    <p><i class="bi bi-telephone"></i> <?php echo e($anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '-'); ?></p>
                                    <p><i class="bi bi-clock"></i> <?php echo e(now()->locale('id')->isoFormat('dddd')); ?>, <?php echo e(now()->format('H:i')); ?> WIB</p>
                                </div>
                            </div>
                            <?php if($noHpAdmin): ?>
                            <a href="<?php echo e($waLink); ?>" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px;padding:8px 16px;background:#25D366;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:background 0.2s" onmouseover="this.style.background='#1ebe57'" onmouseout="this.style.background='#25D366'">
                                <i class="bi bi-whatsapp" style="font-size:16px"></i> Chat Admin
                            </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleGenre(event)">
                            <i class="bi bi-collection-fill"></i> <span class="nav-text">Genre Buku</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
                        </a>
                        <div class="floating-dropdown genre-dropdown-wrapper" id="genreDropdown">
                            <div class="genre-dropdown-header">
                                <h6><i class="bi bi-collection-fill"></i> Pilih Genre</h6>
                                <span class="badge"><?php echo e($genreList->count()); ?> genre</span>
                            </div>
                            <div class="genre-search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="genreSearchInput" placeholder="Cari genre..." autocomplete="off">
                            </div>
                            <div class="genre-dropdown-body">
                                <div class="genre-grid" id="genreGrid">
                                    <a href="<?php echo e(route('koleksi.index')); ?>" class="genre-grid-item <?php echo e(!$genre ? 'active' : ''); ?>" data-genre="">
                                        <i class="bi bi-grid-fill"></i>
                                        <span>Semua</span>
                                    </a>
                                    <?php $__currentLoopData = $genreList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('koleksi.index', ['genre' => $g->id])); ?>" class="genre-grid-item <?php echo e($genre == $g->id ? 'active' : ''); ?>" data-genre="<?php echo e(strtolower($g->nama)); ?>">
                                        <i class="bi bi-tag-fill"></i>
                                        <span><?php echo e($g->nama); ?></span>
                                    </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="<?php echo e(route('favorit.index', ['from' => 'koleksi'])); ?>"><i class="bi bi-heart-fill"></i> <span class="nav-text">Favorit</span></a>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="#" onclick="openScanModal(event)"><i class="bi bi-qr-code-scan"></i> <span class="nav-text">Scan</span></a>
                    </li>
                </ul>
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-moon-fill"></i></button>
                <div class="nav-item">
                    <a href="#" onclick="toggleProfil(event)">
                      <?php if(auth()->user()->foto): ?>
                        <img src="<?php echo e(asset(auth()->user()->foto)); ?>" class="profil-avatar" alt="Profil">
                      <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff" class="profil-avatar" alt="Profil">
                      <?php endif; ?>
                    </a>
                    <div class="floating-dropdown" id="profilDropdown" style="right:0;left:auto;min-width:250px">
                        <div style="text-align:center;margin-bottom:15px">
                           <?php if(auth()->user()->foto): ?>
                            <img src="<?php echo e(asset(auth()->user()->foto)); ?>" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
                           <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff&size=200" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
                           <?php endif; ?>
                            <h6 style="font-weight:700;color:#222;margin:0"><?php echo e(auth()->user()->name); ?></h6>
                        </div>
                        <div style="background:#f9f9f9;border-radius:10px;padding:12px;margin-bottom:12px">
                            <p style="font-size:12px;color:#555;margin-bottom:6px"><i class="bi bi-person-badge" style="color:#1a6e35"></i> NIS: <?php echo e(auth()->user()->nis); ?></p>
                            <p style="font-size:12px;color:#555;margin:0"><i class="bi bi-envelope" style="color:#1a6e35"></i> <?php echo e(auth()->user()->email); ?></p>
                        </div>
                        <?php
                            $vipAktif = auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at);
                            $sisaHari = $vipAktif ? (int) now()->diffInDays(auth()->user()->vip_expired_at) + 1 : 0;
                        ?>
                        <button onclick="document.getElementById('profilDropdown').classList.remove('show');document.getElementById('modalVip').style.display='flex'"
                            style="width:100%;padding:10px;background:<?php echo e($vipAktif ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,#374151,#1f2937)'); ?>;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:8px;text-align:left">
                            <?php echo e($vipAktif ? 'VIP Aktif · '.$sisaHari.' hari lagi' : 'Upgrade VIP'); ?>

                        </button>
                       <a href="<?php echo e(route('profil.index')); ?>" style="display:block;width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-align:center;text-decoration:none">
                       <i class="bi bi-person"></i> Lihat Profil
                       </a>
                         <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin-top:8px" onsubmit="return confirm('Yakin ingin logout?');">
                             <?php echo csrf_field(); ?>
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

<!-- REKOMENDASI / HERO -->
<?php if($rekomendasi && $rekomendasi->count() > 0): ?>
<div class="rekom-hero" style="margin-top:70px">
    <div class="rekom-carousel" id="rekomCarousel">
        <div class="rekom-track" id="rekomTrack">
            <?php $__currentLoopData = $rekomendasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rekom-slide">
                <?php if($item->rekom_bg): ?>
                    <div class="rekom-bg" style="background-image:url('<?php echo e(asset($item->rekom_bg)); ?>')"></div>
                <?php elseif($item->sampul): ?>
                    <div class="rekom-bg" style="background-image:url('<?php echo e(asset($item->sampul)); ?>')"></div>
                <?php else: ?>
                    <div class="rekom-bg" style="background:linear-gradient(135deg,#1a6e35,#27ae60)"></div>
                <?php endif; ?>
                <div class="rekom-overlay"></div>
                <div class="rekom-content">
                    <div class="rekom-cover cover-<?php echo e(($idx % 6) + 1); ?>">
                        <?php if($item->sampul): ?>
                            <img src="<?php echo e(asset($item->sampul)); ?>" alt="<?php echo e($item->judul); ?>">
                        <?php else: ?>
                            <div class="rekom-cover-placeholder"><i class="bi bi-book"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="rekom-info">
                        <div class="rekom-judul"><?php echo e($item->judul); ?></div>
                        <div class="rekom-meta-line">
                            <?php if($item->genre): ?><span class="rekom-genre-badge"><i class="bi bi-tag"></i> <?php echo e($item->genre); ?></span><?php endif; ?>

                        </div>
                        <div class="rekom-pengarang"><i class="bi bi-person"></i> <?php echo e($item->pengarang); ?></div>
                        <div class="rekom-btns">
                            <a href="<?php echo e(route('buku.detail', $item->id)); ?>" class="rekom-btn-detail"><i class="bi bi-eye"></i> Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($rekomendasi->count() > 1): ?>
        <button class="rekom-arrow rekom-arrow-left" onclick="rekomSlide(-1)"><i class="bi bi-chevron-left"></i></button>
        <button class="rekom-arrow rekom-arrow-right" onclick="rekomSlide(1)"><i class="bi bi-chevron-right"></i></button>
        <div class="rekom-dots" id="rekomDots">
            <?php $__currentLoopData = $rekomendasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button class="rekom-dot <?php echo e($idx === 0 ? 'active' : ''); ?>" onclick="rekomGoTo(<?php echo e($idx); ?>)"></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="hero">
    <div class="hero-content">
        <h1><i class="bi bi-collection"></i> Koleksi Buku</h1>
        <p>Temukan buku favoritmu di perpustakaan SMK Maarif Walisongo Kajoran</p>
    </div>
</div>
<?php endif; ?>

<!-- SEARCH BOX -->
<div class="container">
    <div class="search-box">
        <input type="text" id="searchInput" class="search-input" placeholder="Cari judul buku atau pengarang..." value="<?php echo e($search ?? ''); ?>">
    </div>
</div>

<!-- MAIN SECTIONS (hidden when searching) -->
<div id="mainSections" <?php if($search): ?> class="hidden" <?php endif; ?>>

    <!-- PENERBIT -->
    <?php if($penerbitList->count() > 0): ?>
    <div class="container koleksi-section">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-building"></i> Penerbit</h3>
        </div>
        <div class="penerbit-scroll" id="penerbitScroll">
            <a href="<?php echo e(route('koleksi.index', $genre ? ['genre' => $genre] : [])); ?>" class="penerbit-chip <?php echo e(!$penerbit ? 'active' : ''); ?>">Semua</a>
            <?php $__currentLoopData = $penerbitList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('koleksi.index', array_filter(['penerbit' => $p->id, 'genre' => $genre]))); ?>" class="penerbit-chip <?php echo e($penerbit == $p->id ? 'active' : ''); ?>"><?php echo e($p->nama); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- BUKU POPULER -->
    <div class="container koleksi-section" id="sectionPopuler">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-fire"></i> Buku Populer</h3>
            <a href="<?php echo e(route('koleksi.populer', array_filter(['genre' => $genre, 'penerbit' => $penerbit]))); ?>" class="section-link">Lihat Semua <i class="bi bi-chevron-right"></i></a>
        </div>
        <?php if($bukuPopuler->count() > 0): ?>
        <div class="carousel-scroll" id="carouselPopuler">
            <?php $__currentLoopData = $bukuPopuler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="book-card-wrapper" data-penerbit="<?php echo e($item->penerbit); ?>">
                <div class="book-card">
                    <div class="book-card-cover cover-<?php echo e(($index % 6) + 1); ?>">
                        <?php if(($item->peminjaman_count ?? 0) >= 10): ?>
                        <div class="badge-trending">Trending</div>
                        <?php endif; ?>
                        <?php if($item->sampul): ?>
                            <img src="<?php echo e(asset($item->sampul)); ?>" alt="<?php echo e($item->judul); ?>">
                        <?php else: ?>
                            <div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>
                        <?php endif; ?>
                        <button type="button" onclick="toggleFavorit(<?php echo e($item->id); ?>, this)" data-favorit="<?php echo e(in_array($item->id, $favoritIds ?? []) ? 'true' : 'false'); ?>" class="btn-favorit">
                            <?php if(in_array($item->id, $favoritIds ?? [])): ?>
                                <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>
                            <?php else: ?>
                                <i class="bi bi-heart" style="color:#999;font-size:15px"></i>
                            <?php endif; ?>
                        </button>
                    </div>
                    <div class="book-card-body">
                        <h5><?php echo e($item->judul); ?></h5>
                        <p class="book-card-meta"><i class="bi bi-person"></i> <?php echo e($item->pengarang); ?></p>
                        <p class="book-card-meta"><i class="bi bi-building"></i> <?php echo e($item->penerbit); ?></p>
                        <?php if($item->genre): ?><span class="book-card-genre"><?php echo e($item->genre); ?></span><?php endif; ?>
                        <div style="margin-top:8px">
                            <span class="status-badge <?php echo e($item->stok > 0 ? 'status-ada' : 'status-habis'); ?>">
                                <?php echo e($item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Dipinjam'); ?>

                            </span>
                        </div>
                        <a href="<?php echo e(route('buku.detail', $item->id)); ?>" class="btn-detail" style="margin-top:10px">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:30px 0">
            <i class="bi bi-inbox" style="font-size:40px"></i>
            <p>Buku populer tidak ditemukan<?php echo e($genre ? ' untuk genre '.$genre : ''); ?><?php echo e($penerbit ? ' dari penerbit '.$penerbit : ''); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- BUKU TERBARU -->
    <div class="container koleksi-section" id="sectionTerbaru">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-clock-history"></i> Buku Terbaru</h3>
            <a href="<?php echo e(route('koleksi.terbaru', array_filter(['genre' => $genre, 'penerbit' => $penerbit]))); ?>" class="section-link">Lihat Semua <i class="bi bi-chevron-right"></i></a>
        </div>
        <?php if($bukuTerbaru->count() > 0): ?>
        <div class="carousel-scroll" id="carouselTerbaru">
            <?php $__currentLoopData = $bukuTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="book-card-wrapper" data-penerbit="<?php echo e($item->penerbit); ?>">
                <div class="book-card">
                    <div class="book-card-cover cover-<?php echo e((($index + 3) % 6) + 1); ?>">
                        <?php if($item->created_at && $item->created_at->diffInDays(now()) <= 30): ?>
                        <div class="badge-vip" style="background:linear-gradient(135deg,#1a6e35,#27ae60)">Baru</div>
                        <?php endif; ?>
                        <?php if($item->sampul): ?>
                            <img src="<?php echo e(asset($item->sampul)); ?>" alt="<?php echo e($item->judul); ?>">
                        <?php else: ?>
                            <div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>
                        <?php endif; ?>
                        <button type="button" onclick="toggleFavorit(<?php echo e($item->id); ?>, this)" data-favorit="<?php echo e(in_array($item->id, $favoritIds ?? []) ? 'true' : 'false'); ?>" class="btn-favorit">
                            <?php if(in_array($item->id, $favoritIds ?? [])): ?>
                                <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>
                            <?php else: ?>
                                <i class="bi bi-heart" style="color:#999;font-size:15px"></i>
                            <?php endif; ?>
                        </button>
                    </div>
                    <div class="book-card-body">
                        <h5><?php echo e($item->judul); ?></h5>
                        <p class="book-card-meta"><i class="bi bi-person"></i> <?php echo e($item->pengarang); ?></p>
                        <p class="book-card-meta"><i class="bi bi-building"></i> <?php echo e($item->penerbit); ?></p>
                        <?php if($item->genre): ?><span class="book-card-genre"><?php echo e($item->genre); ?></span><?php endif; ?>
                        <div style="margin-top:8px">
                            <span class="status-badge <?php echo e($item->stok > 0 ? 'status-ada' : 'status-habis'); ?>">
                                <?php echo e($item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Dipinjam'); ?>

                            </span>
                        </div>
                        <a href="<?php echo e(route('buku.detail', $item->id)); ?>" class="btn-detail" style="margin-top:10px">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:30px 0">
            <i class="bi bi-inbox" style="font-size:40px"></i>
            <p>Buku terbaru tidak ditemukan<?php echo e($genre ? ' untuk genre '.$genre : ''); ?><?php echo e($penerbit ? ' dari penerbit '.$penerbit : ''); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- SEMUA BUKU -->
    <div class="container koleksi-section" id="sectionSemuaBuku">
        <div class="section-header">
            <h3 class="section-title"><i class="bi bi-grid-3x3-gap"></i> Semua Buku</h3>
            <span style="font-size:13px;color:#888"><?php echo e($buku->count()); ?> buku</span>

        </div>

        <?php if($buku->count() > 0): ?>
        <div class="row g-3">
            <?php $__currentLoopData = $buku; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="book-card" style="height:100%" data-penerbit="<?php echo e($item->penerbit); ?>">
                    <div class="book-card-cover cover-<?php echo e(($index % 6) + 1); ?>">
                        <?php if(($item->peminjaman_count ?? 0) >= 10): ?>
                        <div class="badge-trending">Trending</div>
                        <?php endif; ?>
                        <?php if($item->sampul): ?>
                            <img src="<?php echo e(asset($item->sampul)); ?>" alt="<?php echo e($item->judul); ?>">
                        <?php else: ?>
                            <div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>
                        <?php endif; ?>
                        <button type="button" onclick="toggleFavorit(<?php echo e($item->id); ?>, this)" data-favorit="<?php echo e(in_array($item->id, $favoritIds ?? []) ? 'true' : 'false'); ?>" class="btn-favorit">
                            <?php if(in_array($item->id, $favoritIds ?? [])): ?>
                                <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>
                            <?php else: ?>
                                <i class="bi bi-heart" style="color:#999;font-size:15px"></i>
                            <?php endif; ?>
                        </button>
                    </div>
                    <div class="book-card-body">
                        <h5><?php echo e($item->judul); ?></h5>
                        <p class="book-card-meta"><i class="bi bi-person"></i> <?php echo e($item->pengarang); ?></p>
                        <p class="book-card-meta"><i class="bi bi-building"></i> <?php echo e($item->penerbit); ?></p>
                        <?php if($item->genre): ?><span class="book-card-genre"><?php echo e($item->genre); ?></span><?php endif; ?>
                        <div style="margin-top:auto;margin-bottom:10px">
                            <span class="status-badge <?php echo e($item->stok > 0 ? 'status-ada' : 'status-habis'); ?>">
                                <?php echo e($item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Dipinjam'); ?>

                            </span>
                        </div>
                        <?php if($item->stok > 0): ?>
                        <a href="<?php echo e(route('buku.detail', $item->id)); ?>" class="btn-detail">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <?php else: ?>
                        <a href="<?php echo e(route('buku.detail', $item->id)); ?>" class="btn-detail btn-detail-disabled">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada buku dalam koleksi</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- SEARCH RESULTS (shown only when searching) -->
<div id="searchResults" class="container koleksi-section" <?php if($search): ?> style="display:block" <?php endif; ?>>
    <div class="section-header">
        <h3 class="section-title"><i class="bi bi-search"></i> Hasil Pencarian</h3>
        <span id="searchCount" style="font-size:13px;color:#888"></span>
    </div>
    <div class="row g-4" id="searchGrid"></div>
    <div id="searchEmpty" class="empty-state" style="display:none">
        <i class="bi bi-search"></i>
        <p>Tidak ada buku yang ditemukan.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== REKOMENDASI CAROUSEL =====
var rekomCurrent = 0;
var rekomTotal = document.querySelectorAll('#rekomTrack .rekom-slide').length;
var rekomInterval = null;

function rekomGoTo(idx) {
    if (rekomTotal <= 1) return;
    rekomCurrent = ((idx % rekomTotal) + rekomTotal) % rekomTotal;
    var track = document.getElementById('rekomTrack');
    if (track) {
        track.style.transform = 'translateX(-' + (rekomCurrent * 100) + '%)';
    }
    var dots = document.querySelectorAll('#rekomDots .rekom-dot');
    dots.forEach(function(d, i) { d.classList.toggle('active', i === rekomCurrent); });
}

function rekomSlide(dir) {
    rekomGoTo(rekomCurrent + dir);
    clearInterval(rekomInterval);
    rekomStartAuto();
}

function rekomStartAuto() {
    if (rekomTotal <= 1) return;
    rekomInterval = setInterval(function() { rekomSlide(1); }, 6000);
}
rekomStartAuto();

// ===== SWIPE SUPPORT MOBILE =====
(function() {
    var carousel = document.getElementById('rekomCarousel');
    if (!carousel) return;

    var startX = 0;
    var startY = 0;
    var diffX = 0;
    var isDragging = false;
    var threshold = 50;

    carousel.addEventListener('touchstart', function(e) {
        if (window.innerWidth > 768) return;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        var track = document.getElementById('rekomTrack');
        if (track) track.style.transition = 'none';
    }, { passive: true });

    carousel.addEventListener('touchmove', function(e) {
        if (!isDragging || window.innerWidth > 768) return;
        var currentX = e.touches[0].clientX;
        var currentY = e.touches[0].clientY;
        diffX = currentX - startX;
        var diffY = currentY - startY;
        if (Math.abs(diffY) > Math.abs(diffX)) {
            isDragging = false;
            return;
        }
        e.preventDefault();
        var track = document.getElementById('rekomTrack');
        if (track) {
            var baseOffset = -rekomCurrent * 100;
            var dragPercent = (diffX / carousel.offsetWidth) * 100;
            track.style.transform = 'translateX(' + (baseOffset + dragPercent) + '%)';
        }
    }, { passive: false });

    carousel.addEventListener('touchend', function(e) {
        if (!isDragging || window.innerWidth > 768) return;
        isDragging = false;
        var track = document.getElementById('rekomTrack');
        if (track) track.style.transition = '';
        if (diffX < -threshold) {
            rekomSlide(1);
        } else if (diffX > threshold) {
            rekomSlide(-1);
        } else {
            rekomGoTo(rekomCurrent);
        }
        diffX = 0;
    }, { passive: true });

    carousel.addEventListener('touchcancel', function() {
        isDragging = false;
        var track = document.getElementById('rekomTrack');
        if (track) track.style.transition = '';
        rekomGoTo(rekomCurrent);
        diffX = 0;
    }, { passive: true });
})();

// ===== DROPDOWN FUNCTIONS =====
function positionDropdown(el) {
    const navbar = document.querySelector('.navbar');
    if (window.innerWidth <= 768) {
        el.style.top = (navbar.offsetHeight + 10) + 'px';
    } else {
        el.style.top = '';
    }
}
function toggleLayanan(e) { e.preventDefault(); const el = document.getElementById('layananDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('genreDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); }
function toggleGenre(e) { e.preventDefault(); const el = document.getElementById('genreDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); if (el.classList.contains('show')) { setTimeout(function() { document.getElementById('genreSearchInput').focus(); }, 100); } }

// Genre search functionality
document.getElementById('genreSearchInput').addEventListener('input', function(e) {
    var query = e.target.value.toLowerCase().trim();
    var items = document.querySelectorAll('#genreGrid .genre-grid-item');
    var matchCount = 0;
    items.forEach(function(item) {
        var genreName = item.getAttribute('data-genre');
        if (genreName === '' || genreName.includes(query)) {
            item.style.display = '';
            matchCount++;
        } else {
            item.style.display = 'none';
        }
    });
});

// Prevent dropdown from closing when clicking search input
document.getElementById('genreSearchInput').addEventListener('click', function(e) { e.stopPropagation(); });
function toggleProfil(e) { e.preventDefault(); const el = document.getElementById('profilDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('genreDropdown').classList.remove('show'); }
document.addEventListener('click', function(e) {
    if (!e.target.closest('.nav-item') && !e.target.closest('.profil-avatar')) {
        document.getElementById('layananDropdown').classList.remove('show');
        document.getElementById('genreDropdown').classList.remove('show');
        document.getElementById('profilDropdown').classList.remove('show');
    }
});

// ===== SEARCH FUNCTIONALITY =====
var allBooks = [
    <?php $__currentLoopData = $allBuku; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    { id: <?php echo e($item->id); ?>, judul: <?php echo json_encode($item->judul, 15, 512) ?>, pengarang: <?php echo json_encode($item->pengarang, 15, 512) ?>, penerbit: <?php echo json_encode($item->penerbit, 15, 512) ?>, penerbit_id: <?php echo json_encode($item->penerbit_id, 15, 512) ?>, genre: <?php echo json_encode($item->genre, 15, 512) ?>, genre_id: <?php echo json_encode($item->genre_id, 15, 512) ?>, stok: <?php echo e($item->stok); ?>, sampul: <?php echo json_encode($item->sampul ? asset($item->sampul) : null, 15, 512) ?>, peminjaman_count: <?php echo e($item->peminjaman_count ?? 0); ?> },
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
];
// Deduplicate by id
var seenIds = {};
allBooks = allBooks.filter(function(b) { if (seenIds[b.id]) return false; seenIds[b.id] = true; return true; });

var coverColors = ['cover-1','cover-2','cover-3','cover-4','cover-5','cover-6'];
var favoritIds = <?php echo json_encode($favoritIds ?? [], 15, 512) ?>;
var filterGenre = <?php echo json_encode($genre, 15, 512) ?>;
var filterPenerbit = <?php echo json_encode($penerbit, 15, 512) ?>;

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
        mainSections.classList.remove('hidden');
        searchResults.style.display = 'none';
        searchGrid.innerHTML = '';
        searchEmpty.style.display = 'none';
        return;
    }

    mainSections.classList.add('hidden');
    searchResults.style.display = 'block';

    var results = allBooks.filter(function(b) {
        // Filter by genre if active
        if (filterGenre && b.genre_id != filterGenre) return false;
        // Filter by penerbit if active
        if (filterPenerbit && b.penerbit_id != filterPenerbit) return false;
        // Search by text
        return b.judul.toLowerCase().indexOf(q) !== -1 ||
               b.pengarang.toLowerCase().indexOf(q) !== -1 ||
               (b.penerbit && b.penerbit.toLowerCase().indexOf(q) !== -1);
    });

    searchGrid.innerHTML = '';
    searchEmpty.style.display = results.length === 0 ? 'block' : 'none';
    searchCount.textContent = results.length + ' buku ditemukan';

    results.forEach(function(item, index) {
        var isFav = favoritIds.indexOf(item.id) !== -1;
        var sampulHtml = item.sampul
            ? '<img src="' + item.sampul + '" alt="' + item.judul + '">'
            : '<div class="book-card-cover-placeholder"><i class="bi bi-book"></i></div>';
        var trendingBadge = item.peminjaman_count >= 10 ? '<div class="badge-trending">Trending</div>' : '';
        var genreHtml = item.genre ? '<span class="book-card-genre">' + item.genre + '</span>' : '';
        var statusClass = item.stok > 0 ? 'status-ada' : 'status-habis';
        var statusText = item.stok > 0 ? 'Tersedia (' + item.stok + ')' : 'Dipinjam';
        var btnHtml = item.stok > 0
            ? '<a href="/buku/' + item.id + '/detail" class="btn-detail" style="margin-top:10px"><i class="bi bi-bookmark-plus"></i> Pinjam</a>'
            : '<a href="/buku/' + item.id + '/detail" class="btn-detail btn-detail-disabled" style="margin-top:10px">Tidak Tersedia</a>';
        var favIcon = isFav
            ? '<i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>'
            : '<i class="bi bi-heart" style="color:#999;font-size:15px"></i>';

        var html = '<div class="col-6 col-md-4 col-lg-3 grid-card fade-section">' +
            '<div class="book-card">' +
            '<div class="book-card-cover ' + coverColors[index % 6] + '">' +
            trendingBadge +
            sampulHtml +
            '<button type="button" onclick="toggleFavorit(' + item.id + ', this)" data-favorit="' + (isFav ? 'true' : 'false') + '" class="btn-favorit">' + favIcon + '</button>' +
            '</div>' +
            '<div class="book-card-body">' +
            '<h5>' + item.judul + '</h5>' +
            '<p class="book-card-meta"><i class="bi bi-person"></i> ' + item.pengarang + '</p>' +
            '<p class="book-card-meta"><i class="bi bi-building"></i> ' + (item.penerbit || '-') + '</p>' +
            genreHtml +
            '<div style="margin-top:8px"><span class="status-badge ' + statusClass + '">' + statusText + '</span></div>' +
            btnHtml +
            '</div></div></div>';

        searchGrid.insertAdjacentHTML('beforeend', html);
    });
}

// ===== PENERBIT FILTER =====
var activePenerbit = '<?php echo e($penerbit); ?>';
// ===== FAVORIT =====
function toggleFavorit(bukuId, btn) {
    fetch('/buku/' + bukuId + '/favorit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        var icon = btn.querySelector('i');
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
    .catch(function() { alert('Gagal mengubah favorit, coba lagi.'); });
}

// ===== DARK MODE =====
var darkToggle = document.getElementById('darkModeToggle');
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
    darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
}
darkToggle.addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    if(document.body.classList.contains('dark-mode')){
        localStorage.setItem('darkMode','enabled');
        darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        localStorage.setItem('darkMode','disabled');
        darkToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
    }
});

// ===== SCROLL RESTORE =====
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

// ===== NOTIFIKASI =====
document.addEventListener('DOMContentLoaded', function() {
    var notifShown = sessionStorage.getItem('notifShown');
    if (notifShown) {
        var n1 = document.getElementById('notifTerlambat');
        var n2 = document.getElementById('notifH1');
        var n3 = document.getElementById('notifH2');
        if (n1) n1.style.display = 'none';
        if (n2) n2.style.display = 'none';
        if (n3) n3.style.display = 'none';
    } else {
        sessionStorage.setItem('notifShown', 'true');
    }
});
</script>

<?php
    $anggotaLogin = \App\Models\Anggota::where('email', auth()->user()->email)->first();
    $bukuTerlambat = 0;
    if ($anggotaLogin) {
        $bukuTerlambat = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)
            ->where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->count();
    }
?>

<?php if($bukuTerlambat > 0): ?>
<div id="notifTerlambat" style="position:fixed;bottom:20px;right:20px;background:#e74c3c;color:white;padding:15px 20px;border-radius:15px;box-shadow:0 10px 30px rgba(231,76,60,0.4);z-index:9999;max-width:300px;animation:slideIn 0.5s ease">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">&#9888;&#65039;</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Buku Terlambat!</div>
            <div style="font-size:12px;opacity:0.9">Kamu punya <strong><?php echo e($bukuTerlambat); ?> buku</strong> yang melewati tanggal kembali. Segera kembalikan!</div>
            <a href="<?php echo e(route('profil.index')); ?>" style="display:inline-block;margin-top:8px;background:white;color:#e74c3c;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">Lihat Sekarang</a>
        </div>
        <button onclick="document.getElementById('notifTerlambat').style.display='none'" style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">&#10005;</button>
    </div>
</div>
<style>@keyframes slideIn { from { transform: translateX(150px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>
<?php endif; ?>

<?php
    $bukuH1 = 0; $bukuH2 = 0;
    if ($anggotaLogin) {
        $besok = now()->addDay()->toDateString();
        $lusaDari = now()->addDays(2)->toDateString();
        $bukuH1 = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)->where('status', 'dipinjam')->whereDate('tanggal_kembali', $besok)->count();
        $bukuH2 = \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)->where('status', 'dipinjam')->whereDate('tanggal_kembali', $lusaDari)->count();
    }
?>

<?php if($bukuH1 > 0): ?>
<div id="notifH1" style="position:fixed;bottom:<?php echo e($bukuTerlambat > 0 ? '110px' : '20px'); ?>;right:20px;background:#e67e22;color:white;padding:15px 20px;border-radius:15px;box-shadow:0 10px 30px rgba(230,126,34,0.4);z-index:9998;max-width:300px;animation:slideIn 0.5s ease">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">&#128276;</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Segera Kembalikan Buku!</div>
            <div style="font-size:12px;opacity:0.9">Kamu punya <strong><?php echo e($bukuH1); ?> buku</strong> yang harus dikembalikan <strong>besok</strong>!</div>
            <a href="<?php echo e(route('profil.index')); ?>" style="display:inline-block;margin-top:8px;background:white;color:#e67e22;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">Lihat Sekarang</a>
        </div>
        <button onclick="document.getElementById('notifH1').style.display='none'" style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">&#10005;</button>
    </div>
</div>
<?php endif; ?>

<?php if($bukuH2 > 0): ?>
<div id="notifH2" style="position:fixed;bottom:<?php echo e($bukuTerlambat > 0 && $bukuH1 > 0 ? '200px' : ($bukuTerlambat > 0 || $bukuH1 > 0 ? '110px' : '20px')); ?>;right:20px;background:#f39c12;color:white;padding:15px 20px;border-radius:15px;box-shadow:0 10px 30px rgba(243,156,18,0.4);z-index:9997;max-width:300px;animation:slideIn 0.6s ease">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <div style="font-size:24px">&#128197;</div>
        <div>
            <div style="font-weight:700;font-size:14px;margin-bottom:3px">Buku Akan Jatuh Tempo</div>
            <div style="font-size:12px;opacity:0.9">Kamu punya <strong><?php echo e($bukuH2); ?> buku</strong> yang akan jatuh tempo dalam <strong>2 hari</strong>.</div>
            <a href="<?php echo e(route('profil.index')); ?>" style="display:inline-block;margin-top:8px;background:white;color:#f39c12;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none">Lihat Sekarang</a>
        </div>
        <button onclick="document.getElementById('notifH2').style.display='none'" style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0">&#10005;</button>
    </div>
</div>
<?php endif; ?>


<?php if(auth()->guard()->check()): ?>
<?php if(!auth()->user()->agreed_rules): ?>
<div id="modalPeraturan" style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:20px;max-width:480px;width:100%;max-height:85vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
        <div style="background:linear-gradient(135deg,#1a6e35,#27ae60);padding:25px;text-align:center">
            <div style="font-size:35px;margin-bottom:8px">&#128203;</div>
            <div style="color:white;font-size:18px;font-weight:700">Peraturan Perpustakaan</div>
            <div style="color:rgba(255,255,255,0.8);font-size:13px;margin-top:4px">SMK Maarif Walisongo Kajoran</div>
        </div>
        <div style="padding:20px 25px;overflow-y:auto;flex:1">
            <p style="font-size:13px;color:#666;margin-bottom:15px">Dengan menggunakan layanan perpustakaan digital ini, kamu wajib mematuhi peraturan berikut:</p>
            <div style="display:flex;flex-direction:column;gap:12px">
                <?php $__currentLoopData = ['Kartu anggota perpustakaan wajib dibawa setiap kali berkunjung.','Buku yang dipinjam wajib dikembalikan tepat waktu sesuai batas peminjaman.','Keterlambatan pengembalian buku dikenakan denda Rp 1.000 per hari.','Buku yang rusak atau hilang wajib diganti sesuai harga buku.','Dilarang membawa makanan dan minuman ke dalam area perpustakaan.','Jaga ketenangan dan ketertiban selama berada di perpustakaan.','Gunakan fasilitas perpustakaan dengan bertanggung jawab.','E-book hanya boleh dibaca melalui platform ini, tidak untuk disebarluaskan.']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;gap:12px;align-items:flex-start">
                    <div style="min-width:28px;height:28px;background:#1a6e35;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0"><?php echo e($i + 1); ?></div>
                    <div style="font-size:13px;color:#444;line-height:1.6;padding-top:4px"><?php echo e($p); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div style="padding:20px 25px;border-top:1px solid #f0f0f0;background:#fafafa">
            <p style="font-size:12px;color:#888;text-align:center;margin-bottom:15px">Dengan menekan tombol di bawah, kamu menyatakan telah membaca dan menyetujui seluruh peraturan di atas.</p>
            <button onclick="setujuPeraturan()" style="width:100%;padding:14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">Saya Setuju & Mengerti</button>
        </div>
    </div>
</div>
<script>
var loginToken = '<?php echo e(auth()->user()->rules_session_token); ?>';
var rulesKey = 'rules_<?php echo e(auth()->id()); ?>_' + loginToken;
if (localStorage.getItem(rulesKey)) {
    var mp = document.getElementById('modalPeraturan');
    if (mp) mp.style.display = 'none';
} else {
    Object.keys(localStorage).forEach(function(k) {
        if (k.startsWith('rules_<?php echo e(auth()->id()); ?>_') && k !== rulesKey) localStorage.removeItem(k);
    });
    localStorage.setItem(rulesKey, '1');
}
function setujuPeraturan() {
    fetch('<?php echo e(route("setuju.peraturan")); ?>', { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json' } })
    .then(function(res) { return res.json(); })
    .then(function() { document.getElementById('modalPeraturan').style.display = 'none'; })
    .catch(function() { document.getElementById('modalPeraturan').style.display = 'none'; });
}
</script>
<?php endif; ?>
<?php endif; ?>


<div id="modalVip" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:3000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:24px;padding:30px;width:90%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative">
        <button onclick="document.getElementById('modalVip').style.display='none'" style="position:absolute;top:15px;right:15px;background:none;border:none;font-size:20px;color:#aaa;cursor:pointer">&#10005;</button>
        <div style="text-align:center;margin-bottom:20px">
            <div style="font-size:40px;margin-bottom:8px">&#11088;</div>
            <h5 style="font-weight:800;color:#222;margin:0">Member VIP</h5>
            <p style="font-size:13px;color:#888;margin-top:4px">Akses semua e-book & fitur eksklusif</p>
        </div>
        <?php if($vipAktif): ?>
        <div style="background:linear-gradient(135deg,#fff8e1,#fff3cd);border-radius:14px;padding:15px;text-align:center;margin-bottom:20px">
            <div style="font-size:13px;color:#856404;font-weight:600">VIP Aktif</div>
            <div style="font-size:12px;color:#888;margin-top:4px">Berakhir: <?php echo e(auth()->user()->vip_expired_at->format('d M Y')); ?></div>
            <div style="font-size:20px;font-weight:800;color:#f59e0b;margin-top:6px"><?php echo e($sisaHari); ?> Hari Lagi</div>
        </div>
        <?php endif; ?>
        <div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:20px">
            <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:10px">Keuntungan VIP:</div>
            <div style="font-size:12px;color:#555;line-height:2">
                <div>Akses semua e-book VIP</div>
                <div>Pinjam hingga 6 buku sekaligus</div>
                <div>Durasi pinjam hingga 14 hari</div>
                <div>Badge VIP eksklusif</div>
            </div>
        </div>
        <?php if(auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at)): ?>
        <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">Status VIP:</div>
        <button type="button" disabled style="width:100%;padding:13px;border-radius:12px;border:none;background:#e5e7eb;color:#6b7280;font-weight:700;font-size:14px;cursor:not-allowed">VIP Masih Aktif</button>
        <?php else: ?>
        <div style="font-size:12px;font-weight:700;color:#333;margin-bottom:12px">Upgrade VIP 7 hari — 100 Koin:</div>
        <form action="<?php echo e(route('vip.beli')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" <?php echo e((auth()->user()->coin ?? 0) < 100 ? 'disabled' : ''); ?> onclick="return confirm('Upgrade VIP 7 hari dengan 100 koin?')"
                style="width:100%;padding:13px;border-radius:12px;border:none;background:<?php echo e((auth()->user()->coin ?? 0) >= 100 ? 'linear-gradient(135deg,#1a6e35,#27ae60)' : '#e5e7eb'); ?>;color:<?php echo e((auth()->user()->coin ?? 0) >= 100 ? 'white' : '#9ca3af'); ?>;font-weight:700;font-size:14px;cursor:<?php echo e((auth()->user()->coin ?? 0) >= 100 ? 'pointer' : 'not-allowed'); ?>">
                <?php echo e((auth()->user()->coin ?? 0) >= 100 ? 'Upgrade dengan 100 Koin' : 'Koin Tidak Cukup ('.(auth()->user()->coin ?? 0).'/100)'); ?>

            </button>
        </form>
        <?php endif; ?>
    </div>
</div>


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
var scanIsVip = <?php echo e(auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at) ? 'true' : 'false'); ?>;

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
        scanShowError('Library scanner gagal dimuat.', 'Pastikan koneksi internet stabil dan muat ulang halaman.');
        return;
    }
    if (!window.isSecureContext) {
        scanShowError('Halaman harus dibuka dengan HTTPS.', 'Kamera memerlukan secure context.');
        return;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        scanShowError('Browser tidak mendukung akses kamera.', 'Gunakan browser modern seperti Chrome, Firefox, atau Edge.');
        return;
    }
    scanScanner = new Html5Qrcode("scanReader");
    var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    var cameraConfig;
    if (isMobile) {
        cameraConfig = { facingMode: "environment" };
        scanStartWithCamera(cameraConfig);
    } else {
        Html5Qrcode.getCameras().then(function(cameras) {
            if (cameras && cameras.length > 0) {
                var selected = cameras.find(function(c) { return c.label && /back|rear|environment/i.test(c.label); }) || cameras[0];
                cameraConfig = { deviceId: { exact: selected.id } };
            } else {
                cameraConfig = { facingMode: "environment" };
            }
            scanStartWithCamera(cameraConfig);
        }).catch(function() {
            cameraConfig = { facingMode: "environment" };
            scanStartWithCamera(cameraConfig);
        });
    }
}

function scanStartWithCamera(cameraConfig) {
    scanScanner.start(cameraConfig, { fps: 10, qrbox: 250 },
        function(code) {
            if (scanProcessing) return;
            scanProcessing = true;
            try { scanScanner.pause(true); } catch(e) {}
            setTimeout(function() { scanStopScanner(); }, 300);
            scanCloseFn();
            scanProcessIsbn(code);
        },
        function() {}
    ).catch(function(err) {
        scanStopScanner();
        var msg = String(err.message || err || '');
        if (msg.indexOf('NotAllowedError') !== -1 || msg.indexOf('Permission') !== -1 || msg.indexOf('permission') !== -1 || msg.indexOf('denied') !== -1) {
            scanShowError('Izin kamera ditolak.', 'Berikan izin kamera di pengaturan browser.');
        } else if (msg.indexOf('NotFoundError') !== -1 || msg.indexOf('not found') !== -1) {
            scanShowError('Kamera tidak ditemukan.', 'Pastikan perangkat memiliki kamera.');
        } else if (msg.indexOf('NotReadableError') !== -1 || msg.indexOf('in use') !== -1) {
            scanShowError('Kamera sedang digunakan.', 'Tutup aplikasi lain yang menggunakan kamera.');
        } else {
            scanShowError('Gagal mengakses kamera.', msg);
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
    try { if (scanScanner.isScanning) { scanScanner.stop().catch(function() {}); } } catch(e) {}
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
    fetch('/barcode/cek-buku', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': scanCsrf },
        body: JSON.stringify({ kode: isbn })
    })
    .then(function(r) { return r.json().then(function(data) { return { status: r.status, data: data }; }); })
    .then(function(result) {
        if (result.status !== 200 || !result.data.buku || !result.data.buku.id) {
            alert('Buku tidak ditemukan untuk kode: ' + isbn);
            return;
        }
        window.location.href = '/buku/' + result.data.buku.id + '/detail';
    })
    .catch(function(err) { alert('Gagal mencari buku: ' + err.message); });
}

function scanShowPinjam(data) {
    var maxDurasi = scanIsVip ? 14 : 7;
    var today = new Date().toISOString().split('T')[0];
    var minKembali = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    document.getElementById('scanPinjamContent').innerHTML =
        '<div class="scan-modal-info">' +
            '<p><i class="bi bi-person"></i> Peminjam: <strong><?php echo e(auth()->user()->name); ?></strong></p>' +
            '<p><i class="bi bi-book"></i> Buku: <strong>' + data.buku.judul + '</strong></p>' +
            '<p><i class="bi bi-star-fill"></i> Status: <strong>' + (scanIsVip ? 'VIP - maks. 6 buku, 14 hari' : 'Reguler - maks. 3 buku, 7 hari') + '</strong></p>' +
        '</div>' +
        (data.buku.sampul ? '<img src="' + data.buku.sampul + '" class="scan-cover" style="margin:0 auto 16px;display:block">' : '') +
        '<div style="margin-bottom:12px"><label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="scan-label">Tanggal Pinjam</label><input type="date" id="scanTglPinjam" min="' + today + '" value="' + today + '" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="scan-date-input"></div>' +
        '<div style="margin-bottom:16px"><label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="scan-label">Tanggal Kembali</label><input type="date" id="scanTglKembali" min="' + minKembali + '" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="scan-date-input"></div>' +
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
    fetch('<?php echo e(route("barcode.pinjam")); ?>', {
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
            '<p><i class="bi bi-person"></i> Peminjam: <strong><?php echo e(auth()->user()->name); ?></strong></p>' +
            '<p><i class="bi bi-book"></i> Buku: <strong>' + data.buku.judul + '</strong></p>' +
            '<p><i class="bi bi-calendar"></i> Batas kembali: <strong>' + data.peminjaman_aktif.tanggal_kembali + '</strong></p>' +
        '</div>' +
        (data.buku.sampul ? '<img src="' + data.buku.sampul + '" class="scan-cover" style="margin:0 auto 16px;display:block">' : '') +
        '<button class="scan-btn-primary" onclick="scanSubmitKembali(' + data.peminjaman_aktif.id + ')"><i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian</button>' +
        '<button class="scan-btn-secondary" onclick="document.getElementById(\'scanKembaliModal\').classList.remove(\'show\')">Batal</button>';
    document.getElementById('scanKembaliModal').classList.add('show');
}

function scanSubmitKembali(peminjamanId) {
    fetch('<?php echo e(route("barcode.kembali")); ?>', {
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<?php if (isset($component)) { $__componentOriginalc341f611a63b85a7efee481957438a0f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc341f611a63b85a7efee481957438a0f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.crop-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('crop-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc341f611a63b85a7efee481957438a0f)): ?>
<?php $attributes = $__attributesOriginalc341f611a63b85a7efee481957438a0f; ?>
<?php unset($__attributesOriginalc341f611a63b85a7efee481957438a0f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc341f611a63b85a7efee481957438a0f)): ?>
<?php $component = $__componentOriginalc341f611a63b85a7efee481957438a0f; ?>
<?php unset($__componentOriginalc341f611a63b85a7efee481957438a0f); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/koleksi/index.blade.php ENDPATH**/ ?>