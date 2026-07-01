<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                @auth
                    <div style="position:relative">
                        <button onclick="toggleNotifikasi()" style="background:none;border:none;cursor:pointer;padding:6px;position:relative">
                            <i class="bi bi-bell" style="font-size:20px;color:#1a6e35"></i>
                            @if($notifikasiCount > 0)
                                <span style="position:absolute;top:0;right:0;background:#e74c3c;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center">{{ $notifikasiCount }}</span>
                            @endif
                        </button>
                        <div id="notifikasiDropdown" style="display:none;position:absolute;right:0;top:100%;width:320px;background:white;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);z-index:1000;max-height:400px;overflow-y:auto">
                            <div style="padding:14px 16px;border-bottom:1px solid #eee;font-weight:700;font-size:14px;color:#222">
                                <i class="bi bi-bell-fill" style="color:#1a6e35"></i> Notifikasi
                            </div>
                            @forelse($notifikasiList as $notif)
                                <div style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background:#f0fdf4">
                                    <div style="font-size:12px;font-weight:700;color:#1a6e35;margin-bottom:4px">{{ $notif->judul }}</div>
                                    <div style="font-size:12px;color:#555;line-height:1.4">{{ $notif->pesan }}</div>
                                    <div style="font-size:11px;color:#999;margin-top:4px">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            @empty
                                <div style="padding:30px 16px;text-align:center;color:#999;font-size:13px">
                                    <i class="bi bi-bell-slash" style="font-size:24px;display:block;margin-bottom:8px"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endauth
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
<div class="detail-card" id="rekomendasiSection" style="flex-direction:column;margin-top:20px">
    <h5 style="font-weight:700;color:#222;margin-bottom:20px">
        <i class="bi bi-collection" style="color:#1a6e35"></i> Rekomendasi Buku Serupa
    </h5>
    <div style="display:flex;gap:15px;overflow-x:auto;padding-bottom:5px">
        @foreach($rekomendasi as $rec)
        <a href="{{ route('buku.detail', $rec->id) }}" style="text-decoration:none;flex:0 0 160px;width:160px">
            <div style="background:#f8f9fa;border-radius:12px;overflow:hidden;transition:transform 0.3s;height:100%" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="height:200px;overflow:hidden">
                    @if($rec->sampul)
                        <img src="{{ asset($rec->sampul) }}" style="width:100%;height:100%;object-fit:cover" alt="{{ $rec->judul }}">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a6e35,#27ae60);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.5);font-size:36px">
                            <i class="bi bi-book"></i>
                        </div>
                    @endif
                </div>
                <div style="padding:10px">
                    <div style="font-size:12px;font-weight:700;color:#222;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $rec->judul }}</div>
                    <div style="font-size:11px;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $rec->pengarang }}</div>
                    <span style="display:inline-block;margin-top:6px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;background:{{ $rec->stok > 0 ? '#d4edda' : '#f8d7da' }};color:{{ $rec->stok > 0 ? '#1a6e35' : '#721c24' }}">
                        {{ $rec->stok > 0 ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>
            </div>
        </a>
        @endforeach
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
            <button type="submit" class="btn-konfirmasi">
                <i class="bi bi-check-circle"></i> Konfirmasi Pinjam
            </button>
        </form>
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
    const savedUrl = sessionStorage.getItem('koleksiFilterUrl') || '{{ route('koleksi.index') }}';
    window.location.href = savedUrl;
}
</script>

</body>
</html>