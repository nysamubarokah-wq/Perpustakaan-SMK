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
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

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

        .buku-body { padding: 18px; }

        .buku-body h5 {
            font-size: 14px;
            font-weight: 700;
            color: #222;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .buku-meta {
            font-size: 12px;
            color: #888;
            margin-bottom: 4px;
        }

        .stok-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin: 8px 0 12px;
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

        @media (max-width: 768px) {
    .navbar .container-fluid > div {
        flex-wrap: wrap;
        gap: 8px;
    }

    .navbar-search { display: none; }

    .nav-link {
        padding: 8px 10px !important;
        font-size: 13px;
    }

    .nav-text { display: none; }

  @keyframes fadeDownCenter {
    from { opacity: 0; transform: translate(-50%, -10px); }
    to   { opacity: 1; transform: translate(-50%, 0); }
}

#genreDropdown,
#layananDropdown,
#profilDropdown {
    position: fixed;
    left: 50% !important;
    right: auto !important;
    transform: translateX(-50%);
    width: 90vw;
    max-width: 320px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    animation: fadeDownCenter 0.2s ease;
}

    .hero { height: 35vh; min-height: 220px; }
    .hero-content h1 { font-size: 22px; }
    .hero-content p { font-size: 13px; padding: 0 20px; }

    .search-box {
        padding: 15px;
        margin-top: -20px;
    }
    .search-box form > .d-flex {
        flex-direction: column;
        gap: 10px !important;
    }
    .btn-search { width: 100%; }
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
    if(auth()->user()->role === 'admin') {
        $adminAktif = auth()->user();
    } else {
        $adminAktif = \App\Models\User::where('role', 'admin')->latest('updated_at')->first();
    }
    $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
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
                            ] as $genre)
                            <a href="{{ route('koleksi.index', ['genre' => $genre['nama']]) }}" class="genre-list-item">
    <i class="bi {{ $genre['icon'] }}"></i> {{ $genre['nama'] }}
</a>
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="{{ route('favorit.index', ['from' => 'koleksi']) }}"><i class="bi bi-heart-fill"></i> <span class="nav-text">Favorit</span></a>
                    </li>
                </ul>

                <!-- Search -->
                <form method="GET" action="{{ route('koleksi.index') }}" class="d-flex">
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
                        <span class="stok-badge {{ $item->stok > 0 ? 'stok-ada' : 'stok-habis' }}">
                            {{ $item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Tidak Tersedia' }}
                        </span>
                    
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
</body>
</html>