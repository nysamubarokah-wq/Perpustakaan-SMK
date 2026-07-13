<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $buku->judul }} - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            max-width: 900px;
            margin: 90px auto 120px;
            padding: 0 20px;
        }

        .detail-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 35px;
            display: flex;
            gap: 35px;
        }

        /* Kiri - Sampul */
        .left-section {
            min-width: 200px;
            width: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sampul-img {
            width: 200px;
            height: 270px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .sampul-placeholder {
            width: 200px;
            height: 270px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: rgba(255,255,255,0.5);
        }

        /* Deskripsi di bawah foto */
        .deskripsi-box {
            margin-top: 20px;
            width: 100%;
        }

        .deskripsi-box h6 {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .deskripsi-box p {
            font-size: 13px;
            color: #666;
            line-height: 1.7;
        }

        /* Kanan - Info */
        .right-section {
            flex: 1;
        }

        /* Genre + Stok sejajar */
        .badges-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .genre-tag {
            padding: 5px 14px;
            background: #e8f5e9;
            color: #1a6e35;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stok-badge {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stok-ada { background: #d4edda; color: #1a6e35; }
        .stok-habis { background: #f8d7da; color: #721c24; }

        .judul {
            font-size: 26px;
            font-weight: 800;
            color: #111;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .pengarang {
            font-size: 15px;
            color: #666;
            margin-bottom: 20px;
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .meta-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .meta-item .label {
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .meta-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        /* Fixed bottom */
        .fixed-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 15px 25px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .fixed-bar .buku-title { font-size: 14px; font-weight: 700; color: #222; }
        .fixed-bar .buku-author { font-size: 12px; color: #888; }

        .btn-pinjam-fixed {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 35px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show { display: flex; }

        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: popIn 0.3s ease;
        }

        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-box h4 { font-size: 18px; font-weight: 700; color: #222; margin-bottom: 5px; }
        .modal-box .subtitle { font-size: 13px; color: #999; margin-bottom: 20px; }

        .modal-peminjam {
            background: #f0faf4;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #444;
            line-height: 1.8;
        }

        .modal-peminjam strong { color: #1a6e35; }

        .form-group { margin-bottom: 15px; }
        .form-group label { font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; display: block; }
        .form-group input {
            width: 100%;
            padding: 11px 15px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border 0.3s;
        }
        .form-group input:focus { border-color: #27ae60; }

        .btn-konfirmasi {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 5px;
        }

        .btn-batal {
            width: 100%;
            padding: 11px;
            background: #f8f9fa;
            color: #666;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
        }

        @keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-5px); }
}

@media (max-width: 768px) {
    .main-container {
        margin: 80px auto 100px;
        padding: 0 12px;
    }

    .detail-card {
        flex-direction: column;
        padding: 20px;
        gap: 20px;
    }

    .left-section {
        width: 100%;
        min-width: unset;
        align-items: center;
    }

    .sampul-img,
    .sampul-placeholder {
        width: 160px;
        height: 220px;
    }

    .deskripsi-box {
        width: 100%;
    }

    .judul { font-size: 20px; }

    .meta-grid {
        grid-template-columns: 1fr 1fr;
    }

    .meta-item[style*="grid-column"] {
        grid-column: span 2 !important;
    }

    .fixed-bar {
        padding: 12px 15px;
    }

    .fixed-bar .buku-title { font-size: 13px; }
    .fixed-bar .buku-author { font-size: 11px; }

    .btn-pinjam-fixed {
    padding: 10px 20px;
    font-size: 13px;
}

.fixed-bar form button {
    width: 40px !important;
    height: 40px !important;
}

    /* Peta rak biar bisa discroll horizontal kalau sempit */
    .detail-card [style*="max-width:400px"] {
        max-width: 100% !important;
    }
}

@media (max-width: 400px) {
    .meta-grid { grid-template-columns: 1fr; }
    .meta-item[style*="grid-column"] { grid-column: span 1 !important; }
}
body.dark-mode {
    background: #121212;
    color: white;
}

/* Navbar */
body.dark-mode .navbar {
    background: #1e1e1e;
}

body.dark-mode .navbar a,
body.dark-mode .navbar span {
    color: white !important;
}

/* Card utama */
body.dark-mode .detail-card {
    background: #1e1e1e;
    color: white;
}

/* Judul & teks */
body.dark-mode .judul,
body.dark-mode .pengarang,
body.dark-mode .deskripsi-box h6,
body.dark-mode .deskripsi-box p {
    color: white;
}

/* Meta */
body.dark-mode .meta-item {
    background: #2a2a2a;
}

body.dark-mode .meta-item .label {
    color: #aaa;
}

body.dark-mode .meta-item .value {
    color: white;
}

/* Divider */
body.dark-mode .divider {
    border-color: #444;
}

/* Lokasi Rak */
body.dark-mode div[style*="background:#f8f9fa"] {
    background: #2a2a2a !important;
}

body.dark-mode p[style*="color:#333"] {
    color: white !important;
}

body.dark-mode div[style*="color:#666"] {
    color: #ddd !important;
}

/* Fixed bar bawah */
body.dark-mode .fixed-bar {
    background: #1e1e1e;
}

body.dark-mode .fixed-bar .buku-title {
    color: white;
}

body.dark-mode .fixed-bar .buku-author {
    color: #bbb;
}

/* Modal */
body.dark-mode .modal-box {
    background: #1e1e1e;
}

body.dark-mode .modal-box h4,
body.dark-mode .subtitle,
body.dark-mode .form-group label {
    color: white;
}

body.dark-mode .modal-peminjam {
    background: #2a2a2a;
    color: white;
}

body.dark-mode .form-group input {
    background: #2a2a2a;
    border-color: #444;
    color: white;
}

body.dark-mode .btn-batal {
    background: #333;
    color: white;
}
body.dark-mode .fixed-bar form button {
    background: #2a2a2a !important;
}
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 4px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 26px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #f0932b;
}

body.dark-mode .star-rating label { color: #444; }
body.dark-mode .star-rating label { color: #444; }

body.dark-mode .star-rating input:checked ~ label,
body.dark-mode .star-rating label:hover,
body.dark-mode .star-rating label:hover ~ label {
    color: #f0932b;
}
body.dark-mode .detail-card[style*="flex-direction:column"] { background: #1e1e1e; }
body.dark-mode .detail-card[style*="flex-direction:column"] h5 { color: white !important; }
body.dark-mode .detail-card[style*="flex-direction:column"] form { background: #2a2a2a; }
body.dark-mode .detail-card[style*="flex-direction:column"] textarea {
    background: #1e1e1e;
    border-color: #444;
    color: white;
}
body.dark-mode .detail-card[style*="flex-direction:column"] div[style*="border-top"] {
    border-color: #333 !important;
}
body.dark-mode #rekomendasiSection > div > a > div {
    background: #2a2a2a !important;
}
body.dark-mode #rekomendasiSection div[style*="color:#222"] {
    color: #fff !important;
}
body.dark-mode #rekomendasiSection div[style*="color:#888"] {
    color: #bbb !important;
}

/* STREAMING-STYLE RECOMMENDATION CARDS */
.streaming-rec-section {
    flex-direction: column;
    margin-top: 20px;
}
.streaming-rec-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.streaming-rec-title {
    font-size: 18px;
    font-weight: 800;
    color: #1a1a2e;
    display: flex;
    align-items: center;
    gap: 8px;
}
.streaming-rec-title i {
    color: #1a6e35;
    font-size: 20px;
}
.streaming-rec-slider-wrapper {
    position: relative;
}
.streaming-rec-slider {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
    padding: 4px 0 12px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.streaming-rec-slider::-webkit-scrollbar {
    display: none;
}
.streaming-rec-slider::-moz-scrollbar {
    display: none;
}
.streaming-rec-card-wrapper {
    scroll-snap-align: start;
    flex-shrink: 0;
}
.streaming-rec-card {
    width: 140px;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: transform 0.3s cubic-bezier(0.25,0.8,0.25,1), box-shadow 0.3s;
    cursor: pointer;
    text-decoration: none;
    display: block;
}
.streaming-rec-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}
.streaming-rec-cover {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
}
.streaming-rec-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s cubic-bezier(0.25,0.8,0.25,1);
}
.streaming-rec-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px 10px;
    text-align: center;
    background: linear-gradient(145deg, #1a6e35 0%, #27ae60 50%, #1a6e35 100%);
    position: relative;
    overflow: hidden;
}
.streaming-rec-placeholder::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
        90deg,
        transparent,
        transparent 10px,
        rgba(0,0,0,0.03) 10px,
        rgba(0,0,0,0.03) 20px
    );
}
.streaming-rec-placeholder-icon {
    font-size: 28px;
    color: rgba(255,255,255,0.7);
    margin-bottom: 8px;
    position: relative;
}
.streaming-rec-placeholder-text {
    font-size: 8px;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    position: relative;
}
.streaming-rec-no-cover {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: transform 0.3s cubic-bezier(0.25,0.8,0.25,1), box-shadow 0.3s;
    cursor: pointer;
    text-decoration: none;
    display: block;
    width: 140px;
}
.streaming-rec-no-cover:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}
.streaming-rec-no-cover-cover {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
    background: linear-gradient(145deg, #1a6e35, #27ae60);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
}
.streaming-rec-no-cover-icon {
    font-size: 32px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 10px;
}
.streaming-rec-no-cover-title {
    font-size: 10px;
    font-weight: 700;
    color: white;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 4px;
}
.streaming-rec-no-cover-author {
    font-size: 8px;
    color: rgba(255,255,255,0.7);
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.streaming-rec-no-cover-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    padding: 2px 6px;
    border-radius: 20px;
    font-size: 8px;
    font-weight: 700;
    z-index: 5;
}
.streaming-rec-no-cover-favorit {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.92);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
    transition: all 0.2s;
}
.streaming-rec-no-cover-favorit:hover {
    transform: scale(1.12);
    background: white;
}
.streaming-rec-no-cover-body {
    padding: 8px 6px;
    background: white;
}
.streaming-rec-no-cover-body h6 {
    font-size: 10px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 2px 0;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.streaming-rec-no-cover-meta {
    font-size: 9px;
    color: #888;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.streaming-rec-favorit {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.92);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
    transition: all 0.2s;
}
.streaming-rec-favorit:hover {
    transform: scale(1.12);
    background: white;
}
.streaming-rec-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    z-index: 5;
}
.streaming-rec-badge.trending {
    background: linear-gradient(135deg, #ff5722, #ff9800);
    color: white;
}
.streaming-rec-badge.baru {
    background: linear-gradient(135deg, #1a6e35, #27ae60);
    color: white;
}
.streaming-rec-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 10px;
}
.streaming-rec-card:hover .streaming-rec-overlay {
    opacity: 1;
}
.streaming-rec-overlay-info {
    transform: translateY(10px);
    opacity: 0;
    transition: transform 0.3s ease 0.05s, opacity 0.3s ease 0.05s;
}
.streaming-rec-card:hover .streaming-rec-overlay-info {
    transform: translateY(0);
    opacity: 1;
}
.streaming-rec-overlay-title {
    font-size: 11px;
    font-weight: 700;
    color: white;
    margin-bottom: 3px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}
.streaming-rec-overlay-meta {
    font-size: 9px;
    color: rgba(255,255,255,0.8);
    margin-bottom: 1px;
    display: flex;
    align-items: center;
    gap: 3px;
}
.streaming-rec-overlay-meta i {
    font-size: 8px;
}
.streaming-rec-overlay-genre {
    display: inline-block;
    padding: 2px 5px;
    border-radius: 4px;
    font-size: 8px;
    font-weight: 600;
    background: rgba(39,174,96,0.25);
    color: #4ade80;
    border: 1px solid rgba(39,174,96,0.4);
    margin-top: 3px;
}
.streaming-rec-overlay-stok {
    font-size: 9px;
    color: rgba(255,255,255,0.7);
    margin-top: 3px;
}
.streaming-rec-body {
    padding: 8px 6px;
}
.streaming-rec-body h6 {
    font-size: 10px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.streaming-rec-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e0e0e0;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: all 0.25s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-size: 14px;
}
.streaming-rec-nav:hover {
    background: #1a6e35;
    color: white;
    border-color: #1a6e35;
}
.streaming-rec-slider-wrapper:hover .streaming-rec-nav {
    opacity: 1;
}
.streaming-rec-nav.prev {
    left: -5px;
}
.streaming-rec-nav.next {
    right: -5px;
}
@media (hover: hover) and (pointer: fine) {
    .streaming-rec-card:hover .streaming-rec-cover img {
        transform: scale(1.05);
    }
    .streaming-rec-card:hover .streaming-rec-overlay {
        opacity: 1;
    }
    .streaming-rec-card:hover .streaming-rec-overlay-info {
        transform: translateY(0);
        opacity: 1;
    }
}
@media (max-width: 768px) {
    .streaming-rec-card {
        width: 120px;
    }
    .streaming-rec-no-cover {
        width: 120px;
    }
    .streaming-rec-nav {
        display: none;
    }
    .streaming-rec-overlay {
        display: none;
    }
}
@media (max-width: 480px) {
    .streaming-rec-card {
        width: 110px;
    }
    .streaming-rec-no-cover {
        width: 110px;
    }
}

/* Dark mode streaming rec */
body.dark-mode .streaming-rec-title {
    color: #ffffff;
}
body.dark-mode .streaming-rec-card {
    background: #1e1e1e;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
body.dark-mode .streaming-rec-card .streaming-rec-body {
    background: #1e1e1e;
}
body.dark-mode .streaming-rec-card .streaming-rec-body h6 {
    color: #ffffff;
}
body.dark-mode .streaming-rec-no-cover {
    background: #1e1e1e;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
body.dark-mode .streaming-rec-no-cover-cover {
    background: linear-gradient(145deg, #1a3d1a, #1a4d2a);
}
body.dark-mode .streaming-rec-no-cover-body {
    background: #1e1e1e;
}
body.dark-mode .streaming-rec-no-cover-body h6 {
    color: #ffffff;
}
body.dark-mode .streaming-rec-no-cover-meta {
    color: #bdbdbd;
}
body.dark-mode .streaming-rec-nav {
    background: #2a2a2a;
    border-color: #444;
    color: #e0e0e0;
}
body.dark-mode .streaming-rec-nav:hover {
    background: #1a6e35;
    border-color: #1a6e35;
    color: white;
}

/* Ulasan section - dark mode */
body.dark-mode div[style*="border-top:1px solid #eee"] {
    border-top-color: #444 !important;
}

body.dark-mode div[style*="color:#222"] {
    color: #fff !important;
}

body.dark-mode p[style*="color:#555"] {
    color: #ccc !important;
}

body.dark-mode div[style*="background:#f0f9ff"] {
    background: #1a2744 !important;
    border-left-color: #60a5fa !important;
}

body.dark-mode div[style*="background:#f0f9ff"] span[style*="color:#1e40af"] {
    color: #93c5fd !important;
}

body.dark-mode div[style*="background:#f0f9ff"] p[style*="color:#475569"] {
    color: #cbd5e1 !important;
}

body.dark-mode div[style*="background:#f0f9ff"] i[style*="color:#3b82f6"] {
    color: #60a5fa !important;
}

/* Notifikasi dropdown - dark mode */
body.dark-mode #notifikasiDropdown {
    background: #1e1e1e !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
}

body.dark-mode #notifikasiDropdown > div:first-child {
    border-bottom-color: #444 !important;
    color: white !important;
}

body.dark-mode #notifikasiDropdown > div:not(:first-child) {
    background: #2a2a2a !important;
    border-bottom-color: #444 !important;
}

body.dark-mode #notifikasiDropdown div[style*="font-weight:700"][style*="color:#1a6e35"] {
    color: #4ade80 !important;
}

body.dark-mode #notifikasiDropdown div[style*="color:#555"] {
    color: #ccc !important;
}

body.dark-mode #notifikasiDropdown div[style*="color:#999"] {
    color: #888 !important;
}
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="#" onclick="kembaliKeKoleksi()" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="#" onclick="kembaliKeKoleksi()" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- MAIN -->
<div class="main-container">
    @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="detail-card">
        <!-- KIRI -->
        <div class="left-section">
            @if($buku->sampul)
                <img src="{{ asset($buku->sampul) }}" class="sampul-img" alt="{{ $buku->judul }}">
            @else
                <div class="sampul-placeholder"><i class="bi bi-book"></i></div>
            @endif

            <!-- Deskripsi di bawah foto -->
            <div class="deskripsi-box">
                <h6><i class="bi bi-file-text" style="color:#1a6e35"></i> Deskripsi</h6>
                <p>{{ $buku->deskripsi ?? 'Deskripsi belum tersedia.' }}</p>
            </div>
        </div>

        <!-- KANAN -->
        <div class="right-section">
            <!-- Genre + Stok sejajar -->
          <div class="badges-row">
    @if($buku->genre)
        <span class="genre-tag"><i class="bi bi-tag"></i> {{ $buku->genre }}</span>
    @endif
    <span class="stok-badge {{ $buku->stok > 0 ? 'stok-ada' : 'stok-habis' }}">
        {{ $buku->stok > 0 ? 'Tersedia ('.$buku->stok.')' : 'Tidak Tersedia' }}
    </span>
    @if($totalUlasan > 0)
        <span style="padding:5px 14px;background:#fff3cd;color:#856404;border-radius:20px;font-size:12px;font-weight:600">
            <i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }} ({{ $totalUlasan }} ulasan)
        </span>
    @endif
</div>

            <h1 class="judul">{{ $buku->judul }}</h1>
            <p class="pengarang"><i class="bi bi-person"></i> {{ $buku->pengarang }}</p>

            <hr class="divider">

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">Penerbit</div>
                    <div class="value">{{ $buku->penerbit }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Tahun Terbit</div>
                    <div class="value">{{ $buku->tahun_terbit }}</div>
                </div>
                <div class="meta-item" style="grid-column: span 2">
                    <div class="label">ISBN</div>
                    <div class="value">{{ $buku->isbn }}</div>
                </div>
            </div>
            @if($buku->lokasi)
<hr style="border:none;border-top:1px solid #eee;margin:20px 0">
<div>
    <p style="font-size:13px;font-weight:700;color:#333;margin-bottom:12px">
        <i class="bi bi-map" style="color:#1a6e35"></i> Lokasi Buku — Rak {{ $buku->lokasi }}
    </p>
    
    <!-- PETA RAK -->
    <div style="background:#f8f9fa;border-radius:12px;padding:15px;overflow-x:auto">
        
        <!-- Pintu masuk -->
        <div style="text-align:center;margin-bottom:10px">
            <div style="display:inline-block;background:#333;color:white;padding:5px 20px;border-radius:5px;font-size:11px;font-weight:600">🚪 PINTU MASUK</div>
        </div>

        <!-- Layout rak -->
        <div style="display:flex;flex-direction:column;gap:8px;max-width:400px;margin:0 auto">
            
            @php
                $rows = ['A' => ['A1','A2','A3'], 'B' => ['B1','B2','B3'], 'C' => ['C1','C2','C3'], 'D' => ['D1','D2','D3']];
            @endphp

            @foreach($rows as $rowLabel => $cols)
            <div style="display:flex;align-items:center;gap:8px">
                <div style="font-size:12px;font-weight:700;color:#666;width:20px">{{ $rowLabel }}</div>
                @foreach($cols as $col)
                @php $isTarget = $buku->lokasi === $col; @endphp
                <div style="
                    flex:1;
                    padding:10px 5px;
                    border-radius:8px;
                    text-align:center;
                    font-size:11px;
                    font-weight:700;
                    position:relative;
                    background:{{ $isTarget ? '#1a6e35' : 'white' }};
                    color:{{ $isTarget ? 'white' : '#555' }};
                    border:2px solid {{ $isTarget ? '#1a6e35' : '#ddd' }};
                    box-shadow:{{ $isTarget ? '0 4px 15px rgba(26,110,53,0.4)' : 'none' }};
                    transition:all 0.3s;
                ">
                    @if($isTarget)
                        <div style="position:absolute;top:-20px;left:50%;transform:translateX(-50%);font-size:18px;animation:bounce 1s infinite">📍</div>
                    @endif
                    Rak {{ $col }}
                </div>
                @endforeach
                <div style="font-size:12px;font-weight:700;color:#666;width:20px">{{ $rowLabel }}</div>
            </div>
            @endforeach

            <!-- Nomor kolom -->
            <div style="display:flex;gap:8px;padding-left:28px">
                <div style="flex:1;text-align:center;font-size:11px;color:#999;font-weight:600">1</div>
                <div style="flex:1;text-align:center;font-size:11px;color:#999;font-weight:600">2</div>
                <div style="flex:1;text-align:center;font-size:11px;color:#999;font-weight:600">3</div>
            </div>
        </div>

        <!-- Legend -->
        <div style="display:flex;align-items:center;gap:8px;margin-top:12px;justify-content:center">
            <div style="width:16px;height:16px;background:#1a6e35;border-radius:4px"></div>
            <span style="font-size:11px;color:#666">Lokasi buku ini (Rak {{ $buku->lokasi }})</span>
        </div>
    </div>
</div>
@endif
        </div>
    
</div>
@if($rekomendasi->count() > 0)
<div class="detail-card streaming-rec-section" id="rekomendasiSection">
    <div class="streaming-rec-header">
        <h5 class="streaming-rec-title">
            <i class="bi bi-collection"></i> Rekomendasi Buku Serupa
        </h5>
    </div>
    <div class="streaming-rec-slider-wrapper">
        <button class="streaming-rec-nav prev" onclick="slideRecSlider(-1)" aria-label="Sebelumnya">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="streaming-rec-nav next" onclick="slideRecSlider(1)" aria-label="Berikutnya">
            <i class="bi bi-chevron-right"></i>
        </button>
        <div class="streaming-rec-slider" id="recSlider">
            @foreach($rekomendasi as $rec)
            @if($rec->sampul)
            <div class="streaming-rec-card-wrapper">
                <a href="{{ route('buku.detail', $rec->id) }}" class="streaming-rec-card">
                    <div class="streaming-rec-cover cover-{{ ($loop->index % 6) + 1 }}">
                        @if($rec->created_at && $rec->created_at->diffInDays(now()) <= 30)
                        <div class="streaming-rec-badge baru">Baru</div>
                        @elseif(($rec->peminjaman_count ?? 0) >= 10)
                        <div class="streaming-rec-badge trending">Trending</div>
                        @endif
                        <img src="{{ asset($rec->sampul) }}" alt="{{ $rec->judul }}">
                        <button type="button" onclick="toggleFavorit({{ $rec->id }}, this); event.preventDefault(); event.stopPropagation();" data-favorit="{{ in_array($rec->id, $favoritIds ?? []) ? 'true' : 'false' }}" class="streaming-rec-favorit">
                            @if(in_array($rec->id, $favoritIds ?? []))
                                <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:13px"></i>
                            @else
                                <i class="bi bi-heart" style="color:#999;font-size:13px"></i>
                            @endif
                        </button>
                        <div class="streaming-rec-overlay">
                            <div class="streaming-rec-overlay-info">
                                <div class="streaming-rec-overlay-title">{{ $rec->judul }}</div>
                                <div class="streaming-rec-overlay-meta"><i class="bi bi-person"></i> {{ Str::limit($rec->pengarang, 18) }}</div>
                                @if($rec->penerbit)<div class="streaming-rec-overlay-meta"><i class="bi bi-building"></i> {{ Str::limit($rec->penerbit, 18) }}</div>@endif
                                @if($rec->genre)<div class="streaming-rec-overlay-genre">{{ $rec->genre }}</div>@endif
                                <div class="streaming-rec-overlay-stok">Stok: {{ $rec->stok }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="streaming-rec-body">
                        <h6>{{ $rec->judul }}</h6>
                    </div>
                </a>
            </div>
            @else
            <div class="streaming-rec-card-wrapper">
                <a href="{{ route('buku.detail', $rec->id) }}" class="streaming-rec-no-cover">
                    <div class="streaming-rec-no-cover-cover">
                        @if($rec->created_at && $rec->created_at->diffInDays(now()) <= 30)
                        <div class="streaming-rec-no-cover-badge" style="background: linear-gradient(135deg, #1a6e35, #27ae60); color: white;">Baru</div>
                        @elseif(($rec->peminjaman_count ?? 0) >= 10)
                        <div class="streaming-rec-no-cover-badge" style="background: linear-gradient(135deg, #ff5722, #ff9800); color: white;">Trending</div>
                        @endif
                        <i class="bi bi-book streaming-rec-no-cover-icon"></i>
                        <div class="streaming-rec-no-cover-title">{{ $rec->judul }}</div>
                        <div class="streaming-rec-no-cover-author">{{ $rec->pengarang }}</div>
                        <button type="button" onclick="toggleFavorit({{ $rec->id }}, this); event.preventDefault(); event.stopPropagation();" data-favorit="{{ in_array($rec->id, $favoritIds ?? []) ? 'true' : 'false' }}" class="streaming-rec-no-cover-favorit">
                            @if(in_array($rec->id, $favoritIds ?? []))
                                <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:11px"></i>
                            @else
                                <i class="bi bi-heart" style="color:#999;font-size:11px"></i>
                            @endif
                        </button>
                    </div>
                    <div class="streaming-rec-no-cover-body">
                        <h6>{{ $rec->judul }}</h6>
                        <p class="streaming-rec-no-cover-meta"><i class="bi bi-person"></i> {{ Str::limit($rec->pengarang, 15) }}</p>
                    </div>
                </a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- RATING & ULASAN -->

<!-- RATING & ULASAN -->
<div class="detail-card" style="flex-direction:column;margin-top:20px">
    <h5 style="font-weight:700;color:#222;margin-bottom:20px">
        <i class="bi bi-star-fill" style="color:#f0932b"></i> Rating & Ulasan
        @if($totalUlasan > 0)
            <span style="font-size:13px;color:#888;font-weight:400">({{ $totalUlasan }} ulasan, rata-rata {{ number_format($avgRating, 1) }})</span>
        @endif
    </h5>

    @if($bolehUlasan)
    <form method="POST" action="{{ route('ulasan.store', $buku->id) }}" style="background:#f8f9fa;border-radius:12px;padding:18px;margin-bottom:20px">
        @csrf
        <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:8px;display:block">
            {{ $ulasanSaya ? 'Ubah ulasan kamu' : 'Beri rating & ulasan' }}
        </label>

        <div class="star-rating" style="margin-bottom:12px">
            @for($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating', $ulasanSaya->rating ?? 0) == $i ? 'checked' : '' }} required>
                <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
            @endfor
        </div>

        <textarea name="komentar" rows="3" placeholder="Bagikan pendapatmu tentang buku ini (opsional)..." style="width:100%;padding:11px 15px;border:2px solid #eee;border-radius:10px;font-size:13px;outline:none;resize:vertical;font-family:inherit">{{ old('komentar', $ulasanSaya->komentar ?? '') }}</textarea>

        <button type="submit" style="margin-top:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;padding:10px 25px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-send"></i> {{ $ulasanSaya ? 'Update Ulasan' : 'Kirim Ulasan' }}
        </button>
    </form>
    @elseif(auth()->check())
        <div style="background:#f8f9fa;border-radius:12px;padding:15px;font-size:13px;color:#888;margin-bottom:20px">
            <i class="bi bi-info-circle"></i> Kamu perlu meminjam buku ini dulu sebelum bisa memberi ulasan.
        </div>
    @endif

   @forelse($ulasanList as $ulasan)
<div style="padding:12px 0;border-top:1px solid #eee">
    <div style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:8px">
            @if($ulasan->user->foto)
                <img src="{{ asset($ulasan->user->foto) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($ulasan->user->name) }}&background=1a6e35&color=fff" style="width:28px;height:28px;border-radius:50%;flex-shrink:0">
            @endif
            <div style="line-height:1.2">
                <div style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:700;color:#222">
    {{ $ulasan->user->name }}

    @if($ulasan->user->is_vip && $ulasan->user->vip_expired_at && now()->lt($ulasan->user->vip_expired_at))
        <span style="
            color:#f59e0b;
            font-size:14px;
            line-height:1;
        ">
            ⭐
        </span>
    @endif
</div>
                <div style="font-size:11px;color:#f0932b;line-height:1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $ulasan->rating ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        ...
    </div>
    @if($ulasan->komentar)
        <p style="font-size:13px;color:#555;margin:6px 0 0 36px;line-height:1.5">{{ $ulasan->komentar }}</p>
    @endif
    @if($ulasan->balasan_admin)
        <div style="margin:8px 0 0 36px;padding:10px 12px;background:#f0f9ff;border-left:3px solid #3b82f6;border-radius:8px">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                <i class="bi bi-shield-check" style="color:#3b82f6;font-size:14px"></i>
                <span style="font-size:12px;font-weight:700;color:#1e40af">Balasan Admin</span>
            </div>
            <p style="font-size:13px;color:#475569;margin:0;line-height:1.5">{{ $ulasan->balasan_admin }}</p>
        </div>
    @endif
</div>
    @empty
        <div style="text-align:center;padding:30px 0;color:#aaa">
            <i class="bi bi-chat-square-text" style="font-size:40px;display:block;margin-bottom:10px"></i>
            <p style="font-size:13px">Belum ada ulasan untuk buku ini.</p>
        </div>
    @endforelse
</div>
</div>

<!-- FIXED BAR -->

<!-- FIXED BAR -->
<!-- FIXED BAR -->
<div class="fixed-bar">
    <div>
        <div class="buku-title">{{ $buku->judul }}</div>
        <div class="buku-author">{{ $buku->pengarang }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        <form method="POST" action="{{ route('buku.favorit', $buku->id) }}">
            @csrf
            <button type="submit" style="width:46px;height:46px;border-radius:50%;border:2px solid {{ $isFavorit ? '#e74c3c' : '#eee' }};background:white;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0">
                @if($isFavorit)
                    <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:18px"></i>
                @else
                    <i class="bi bi-heart" style="color:#999;font-size:18px"></i>
                @endif
            </button>
        </form>

        @if($buku->stok > 0)
            <button onclick="showModal()" class="btn-pinjam-fixed">
                <i class="bi bi-bookmark-plus"></i> Pinjam Buku
            </button>
        @else
            <button disabled style="background:#eee;color:#aaa;border:none;border-radius:12px;padding:12px 35px;font-size:15px;font-weight:700;cursor:not-allowed">
                Tidak Tersedia
            </button>
        @endif
    </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalPinjam">
    <div class="modal-box">
        <h4><i class="bi bi-bookmark-plus" style="color:#1a6e35"></i> Form Peminjaman</h4>
        <p class="subtitle">Isi data peminjaman buku</p>

       <div class="modal-peminjam">
    <i class="bi bi-person-circle" style="color:#1a6e35"></i>
    Peminjam: <strong>{{ auth()->user()->name }}</strong><br>
    <i class="bi bi-book" style="color:#1a6e35"></i>
    Buku: <strong>{{ $buku->judul }}</strong><br>
    @php
        $isVip = auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at);
    @endphp
    <i class="bi bi-info-circle" style="color:#1a6e35"></i>
    Batas pinjam:
    @if($isVip)
        <strong>⭐ VIP — maks. 6 buku, 14 hari</strong>
    @else
        <strong>maks. 3 buku, 7 hari</strong>
        <span style="font-size:11px;color:#888"> (VIP: 6 buku, 14 hari)</span>
    @endif
</div>

        <form method="POST" action="{{ route('buku.pinjam', $buku->id) }}">
            @csrf
            <input type="hidden" name="redirect_url" id="redirectUrlInput">
            <div class="form-group">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" required
                       min="{{ date('Y-m-d') }}"
                       value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Tanggal Kembali</label>
                <input type="date" name="tanggal_kembali" required
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>
            <div class="form-group">
                <label>Jumlah Buku</label>
                <input type="number" name="jumlah" id="inputJumlah" required min="1" max="{{ $stokTersedia }}" value="1" onchange="updateMaxJumlah()" style="width:100%;padding:11px 15px;border:2px solid #eee;border-radius:10px;font-size:14px;outline:none;transition:border 0.3s" onfocus="this.style.borderColor='#27ae60'">
                <small style="color:#888;font-size:11px">Stok tersedia: <strong>{{ $stokTersedia }}</strong> eksemplar</small>
            </div>
            <button type="submit" class="btn-konfirmasi">
                <i class="bi bi-check-circle"></i> Konfirmasi Pinjam
            </button>
        </form>
        <script>
            function updateMaxJumlah() {
                var input = document.getElementById('inputJumlah');
                var max = {{ $stokTersedia }};
                if (parseInt(input.value) > max) {
                    input.value = max;
                }
                if (parseInt(input.value) < 1) {
                    input.value = 1;
                }
            }
        </script>
        <script>
            document.getElementById('redirectUrlInput').value = sessionStorage.getItem('koleksiFilterUrl') || '{{ route('koleksi.index') }}';
        </script>
        <button onclick="hideModal()" class="btn-batal">Batal</button>
    </div>
</div>

<script>
    function showModal() {
        document.getElementById('modalPinjam').classList.add('show');
    }
    function hideModal() {
        document.getElementById('modalPinjam').classList.remove('show');
    }
    document.getElementById('modalPinjam').addEventListener('click', function(e) {
        if (e.target === this) hideModal();
    });
</script>

<script>
// Notifikasi dropdown
function toggleNotifikasi() {
    const dropdown = document.getElementById('notifikasiDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notifikasiDropdown');
    const button = e.target.closest('[onclick="toggleNotifikasi()"]');
    if (!button && dropdown && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<script>
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
}

function kembaliKeKoleksi() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route('koleksi.index') }}';
    }
}

// Recommendation slider navigation
function slideRecSlider(direction) {
    var slider = document.getElementById('recSlider');
    if (!slider) return;
    var scrollAmount = slider.offsetWidth * 0.6;
    slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

function updateRecSliderButtons() {
    var slider = document.getElementById('recSlider');
    var wrapper = slider ? slider.closest('.streaming-rec-slider-wrapper') : null;
    if (!wrapper) return;
    var prevBtn = wrapper.querySelector('.streaming-rec-nav.prev');
    var nextBtn = wrapper.querySelector('.streaming-rec-nav.next');
    if (!prevBtn || !nextBtn) return;
    requestAnimationFrame(function() {
        prevBtn.disabled = slider.scrollLeft <= 5;
        nextBtn.disabled = slider.scrollLeft + slider.offsetWidth >= slider.scrollWidth - 5;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality
    var slider = document.getElementById('recSlider');
    if (slider) {
        updateRecSliderButtons();
        slider.addEventListener('scroll', function() { updateRecSliderButtons(); });
        window.addEventListener('resize', function() { updateRecSliderButtons(); });

        slider.addEventListener('wheel', function(e) {
            if (Math.abs(e.deltaX) < Math.abs(e.deltaY)) {
                e.preventDefault();
                slider.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    }
});
</script>

</body>
</html>