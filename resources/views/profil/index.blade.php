@php
    $user = auth()->user();
@endphp
<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (sessionStorage.getItem('profilScrollPos') !== null) {
            document.documentElement.style.visibility = 'hidden';
        }
    </script>
    <title>Profil - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   

    <title>Profil - Perpustakaan SMK Maarif</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

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

            margin: 90px auto 60px;

            padding: 0 20px;

        }



        /* PROFIL CARD */

        .profil-card {

            background: white;

            border-radius: 20px;

            overflow: hidden;

            box-shadow: 0 5px 25px rgba(0,0,0,0.08);

            margin-bottom: 25px;

        }



        .profil-header {

            background: linear-gradient(135deg, #1a6e35, #27ae60);

            padding: 40px 35px;

            display: flex;

            align-items: center;

            gap: 25px;

        }



        .profil-avatar {

            width: 90px;

            height: 90px;

            border-radius: 50%;

            border: 4px solid rgba(255,255,255,0.8);

            object-fit: cover;

        }



        .profil-header-info h2 {

            color: white;

            font-size: 22px;

            font-weight: 700;

            margin-bottom: 5px;

        }



        .profil-header-info p {

            color: rgba(255,255,255,0.8);

            font-size: 14px;

            margin: 0;

        }



        .profil-body {

            padding: 30px 35px;

        }



        .info-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;

        }



        .info-item {

            background: #f8f9fa;

            border-radius: 12px;

            padding: 15px 18px;

        }



        .info-item .label {

            font-size: 11px;

            color: #aaa;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            margin-bottom: 5px;

        }



        .info-item .value {

            font-size: 15px;

            font-weight: 600;

            color: #333;

        }



        .info-item .value i { color: #1a6e35; margin-right: 6px; }



        /* RIWAYAT */

        .riwayat-card {

            background: white;

            border-radius: 20px;

            overflow: hidden;

            box-shadow: 0 5px 25px rgba(0,0,0,0.08);

        }



        .riwayat-header {

            padding: 20px 30px;

            border-bottom: 1px solid #eee;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }



        .riwayat-header h5 {

            font-weight: 700;

            color: #222;

            margin: 0;

        }



        .riwayat-item {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 18px 30px;

            border-bottom: 1px solid #f5f5f5;

            transition: background 0.2s;

        }



        .riwayat-item:hover { background: #f9f9f9; }

        .riwayat-item:last-child { border-bottom: none; }



        .riwayat-sampul {

            width: 50px;

            height: 65px;

            border-radius: 8px;

            object-fit: cover;

            flex-shrink: 0;

        }



        .riwayat-sampul-placeholder {

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



        .riwayat-info { flex: 1; }



        .riwayat-info h6 {

            font-weight: 700;

            color: #222;

            margin-bottom: 3px;

            font-size: 14px;

        }



        .riwayat-info p {

            font-size: 12px;

            color: #888;

            margin: 0;

        }



        .status-badge {

            padding: 5px 14px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 600;

            flex-shrink: 0;

        }



        .status-dipinjam { background: #fff3cd; color: #856404; }

        .status-dikembalikan { background: #d4edda; color: #1a6e35; }



        .empty-state {

            text-align: center;

            padding: 50px 0;

            color: #aaa;

        }



        .empty-state i { font-size: 50px; margin-bottom: 10px; display: block; }



        .btn-logout {

            background: rgba(255,255,255,0.2);

            color: white;

            border: 2px solid rgba(255,255,255,0.5);

            border-radius: 10px;

            padding: 8px 20px;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

            text-decoration: none;

            transition: all 0.3s;

        }



        .btn-logout:hover {

            background: white;

            color: #1a6e35;

        }
        @media (max-width: 768px) {
    .main-container { margin-top: 80px; padding: 0 12px; }

    .profil-header { padding: 25px 20px; }
    .profil-header > div[style*="z-index:2"] {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    .profil-header .ms-auto {
        margin-left: 0 !important;
        flex-direction: row !important;
        gap: 8px;
    }

    .info-grid { grid-template-columns: 1fr; }
    .info-item[style*="grid-column"] { grid-column: span 1 !important; }

    #tokoBackground > div[style*="grid-template-columns"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .riwayat-item {
        flex-wrap: wrap;
        padding: 15px 20px;
    }
    .riwayat-info { min-width: 140px; }
}

@media (max-width: 420px) {
    #tokoBackground > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
    .profil-avatar { width: 70px; height: 70px; }
}

body.dark-mode {
    background: #121212;
    color: #fff;
}

/* Navbar */
body.dark-mode .navbar {
    background: #1e1e1e;
}

body.dark-mode .navbar span,
body.dark-mode .navbar a {
    color: #fff !important;
}

/* Card Profil & Riwayat */
body.dark-mode .profil-card,
body.dark-mode .riwayat-card {
    background: #1e1e1e;
    color: #fff;
}

/* Isi card */
body.dark-mode .profil-body,
body.dark-mode .riwayat-header,
body.dark-mode .riwayat-item {
    background: #1e1e1e;
    color: #fff;
    border-color: #333;
}

/* Info item */
body.dark-mode .info-item {
    background: #2a2a2a;
}

body.dark-mode .info-item .value,
body.dark-mode .info-item .label,
body.dark-mode .riwayat-info h6,
body.dark-mode .riwayat-info p,
body.dark-mode .riwayat-header h5 {
    color: #fff;
}

/* Toko background */
body.dark-mode #tokoBackground {
    background: #1e1e1e !important;
}

body.dark-mode #tokoBackground > div > div {
    background: #2a2a2a !important;
}

body.dark-mode #tokoBackground h6,
body.dark-mode #tokoBackground div {
    color: #fff;
}

body.dark-mode .navbar {
    background: #1e1e1e;
}

body.dark-mode .card {
    background: #1e1e1e;
    color: white;
}

/* Toko Background */
body.dark-mode #tokoBackground {
    background: #1e1e1e !important;
}

body.dark-mode #tokoBackground > div > div {
    border-color: #333 !important;
}

/* Bagian bawah setiap card */
body.dark-mode #tokoBackground div[style*="background:white"] {
    background: #2a2a2a !important;
}

/* Tulisan dalam card */
body.dark-mode #tokoBackground div[style*="font-size:12px;font-weight:700"] {
    color: #fff !important;
}

body.dark-mode #tokoBackground div[style*="font-size:11px;color:#888"] {
    color: #ccc !important;
}
body.dark-mode a[href*="favorit"] > div {
    background: #1e1e1e !important;
}
body.dark-mode a[href*="favorit"] div[style*="color:#222"] {
    color: white !important;
}
    </style>


</head>

<body>



<!-- NAVBAR -->

<nav class="navbar">

    <div class="container-fluid px-4">

        <div class="d-flex align-items-center justify-content-between w-100">

            <a href="{{ route('koleksi.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">

                <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">

                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>

            </a>
                <a href="{{ route('koleksi.index') }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
    <i class="bi bi-arrow-left"></i> Kembali
</a>

        </div>

    </div>

</nav>



<!-- MAIN -->

<div class="main-container">


    @if(session('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    @if($totalDendaBelumBayar > 0)
    <div class="alert alert-danger mb-4 border-0 shadow-sm" style="border-radius: 15px;">
        <i class="bi bi-exclamation-triangle-fill"></i> 
        <strong>Perhatian!</strong> Anda memiliki total denda sebesar 
        <strong>Rp {{ number_format($totalDendaBelumBayar, 0, ',', '.') }}</strong>. 
        Segera selesaikan pembayaran ke petugas perpustakaan.
    </div>
@endif



    <!-- PROFIL CARD -->

    <div class="profil-card">
@php
    $bgList = \App\Helpers\Backgrounds::list();
    $activeBg = $bgList[$user->background ?? 'default'] ?? $bgList['default'];
@endphp

<div class="profil-header" style="position:relative;overflow:hidden">

    @if($activeBg['type'] === 'video')
        <video autoplay loop muted playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0">
            <source src="{{ asset($activeBg['value']) }}" type="video/mp4">
        </video>
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);z-index:1"></div>
    @elseif($activeBg['type'] === 'image')
        <div style="position:absolute;inset:0;background-image:url('{{ asset($activeBg['value']) }}');background-size:cover;background-position:center;z-index:0"></div>
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);z-index:1"></div>
    @else
        <div style="position:absolute;inset:0;{{ $activeBg['value'] }};z-index:0"></div>
    @endif

    <div style="position:relative;z-index:2;display:flex;align-items:center;gap:25px;width:100%">
        <div style="position:relative;display:inline-block">
            @if($user->foto)
                <img src="{{ asset($user->foto) }}" class="profil-avatar" alt="Avatar">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=1a6e35&size=200"
                     class="profil-avatar" alt="Avatar">
            @endif
            <label for="uploadFoto" style="position:absolute;bottom:0;right:0;background:#1a6e35;color:white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px">
                <i class="bi bi-camera"></i>
            </label>
            <input type="file" id="uploadFoto" style="display:none" accept="image/*" onchange="uploadFotoFunc(this)">
        </div>
        <div class="profil-header-info">
            <h2>{{ $user->name }}</h2>
            <p><i class="bi bi-person-badge"></i> NIS: {{ $user->nis }}</p>
            <p style="margin-top:5px">
                <span style="background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:20px;font-size:13px">
                    🪙 {{ $user->coin ?? 0 }} Coin
                </span>
            </p>
        </div>
        <div class="ms-auto d-flex flex-column gap-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
            <button onclick="toggleToko()" style="background:rgba(255,255,255,0.2);color:white;border:2px solid rgba(255,255,255,0.5);border-radius:10px;padding:8px 15px;font-size:12px;font-weight:600;cursor:pointer">
                🎨 Ganti Background
            </button>
        </div>
    </div>
</div>
<!-- TOKO BACKGROUND -->
<div id="tokoBackground" style="display:none;padding:25px 35px;border-bottom:1px solid #eee;background:#f8f9fa">
    <h6 style="font-weight:700;color:#222;margin-bottom:15px">🎨 Toko Background <span style="font-size:12px;color:#888;font-weight:400">— Coin kamu: 🪙 {{ $user->coin ?? 0 }}</span></h6>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach(\App\Helpers\Backgrounds::list() as $key => $bg)
        <div style="border-radius:12px;overflow:hidden;box-shadow:0 3px 10px rgba(0,0,0,0.1);{{ $user->background === $key ? 'border:3px solid #1a6e35' : 'border:3px solid transparent' }}">
            <div style="height:70px;position:relative;overflow:hidden;{{ $bg['type'] === 'color' ? $bg['value'] : '' }}">
    @if($bg['type'] === 'video')
        <video autoplay loop muted playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
            <source src="{{ asset($bg['value']) }}" type="video/mp4">
        </video>
    @elseif($bg['type'] === 'image')
        <img src="{{ asset($bg['value']) }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
    @endif

    @if($user->background === $key)
        <div style="position:absolute;top:5px;right:5px;background:#1a6e35;color:white;border-radius:10px;font-size:10px;padding:2px 8px;z-index:2">✓ Aktif</div>
    @endif
</div>
            <div style="padding:10px;background:white">
                <div style="font-size:12px;font-weight:700;color:#222;margin-bottom:5px">{{ $bg['nama'] }}</div>
                <div style="font-size:11px;color:#888;margin-bottom:8px">🪙 {{ $bg['harga'] == 0 ? 'Gratis' : $bg['harga'].' Coin' }}</div>
              @php
    $owned = $user->owned_backgrounds ?? [];
    $dimiliki = $bg['harga'] == 0 || in_array($key, $owned);
@endphp

@if($user->background === $key)
    <button disabled style="width:100%;padding:6px;background:#e8f5e9;color:#1a6e35;border:none;border-radius:8px;font-size:11px;font-weight:600">Dipakai</button>
@elseif($dimiliki)
    <form method="POST" action="{{ route('profil.background', $key) }}">
        @csrf
        <button type="submit" style="width:100%;padding:6px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer">Pakai</button>
    </form>
@elseif(($user->coin ?? 0) >= $bg['harga'])
    <form method="POST" action="{{ route('profil.background', $key) }}">
        @csrf
        <button type="submit" onclick="return confirm('Beli {{ $bg['nama'] }} seharga {{ $bg['harga'] }} coin?')" style="width:100%;padding:6px;background:linear-gradient(135deg,#f9ca24,#f0932b);color:white;border:none;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer">Beli & Pakai</button>
    </form>
@else
    <button disabled style="width:100%;padding:6px;background:#eee;color:#aaa;border:none;border-radius:8px;font-size:11px;font-weight:600">Coin Kurang</button>
@endif
            </div>
        </div>
        @endforeach
    </div>
</div>



        <div class="profil-body">

            <div class="info-grid">

                <div class="info-item">

                    <div class="label">Nama Lengkap</div>

                    <div class="value"><i class="bi bi-person"></i> {{ $user->name }}</div>

                </div>

                <div class="info-item">

                    <div class="label">NIS</div>

                    <div class="value"><i class="bi bi-person-badge"></i> {{ $user->nis ?? '-' }}</div>

                </div>

                <div class="info-item" style="grid-column: span 2">

                    <div class="label">Email</div>

                    <div class="value"><i class="bi bi-envelope"></i> {{ $user->email }}</div>

                </div>

            </div>

       </div>

    </div>

    <!-- FAVORIT CARD -->
<a href="{{ route('favorit.index', ['from' => 'profil']) }}" style="text-decoration:none;display:block;margin-bottom:25px">
    <div style="background:white;border-radius:20px;box-shadow:0 5px 25px rgba(0,0,0,0.08);padding:20px 25px;display:flex;align-items:center;gap:15px;transition:transform 0.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#e74c3c,#ff6b6b);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-heart-fill" style="color:white;font-size:20px"></i>
        </div>
        <div style="flex:1">
            <div style="font-weight:700;color:#222;font-size:15px">Buku Favorit Saya</div>
            <div style="font-size:12px;color:#888">{{ $totalFavorit ?? 0 }} buku tersimpan</div>
        </div>
        <i class="bi bi-chevron-right" style="color:#ccc"></i>
    </div>
</a>

   



    <!-- RIWAYAT PEMINJAMAN -->

    <div class="riwayat-card">

        <div class="riwayat-header">

            <h5><i class="bi bi-clock-history" style="color:#1a6e35"></i> Riwayat Peminjaman</h5>

            <span style="font-size:13px;color:#888">{{ count($riwayat) }} buku</span>

        </div>



        @if(count($riwayat) > 0)

           @foreach($riwayat as $item)
<div class="riwayat-item">
    @if($item->buku->sampul)
        <img src="{{ asset($item->buku->sampul) }}" class="riwayat-sampul" alt="{{ $item->buku->judul }}">
    @else
        <div class="riwayat-sampul-placeholder"><i class="bi bi-book"></i></div>
    @endif

    <div class="riwayat-info">
        <h6>{{ $item->buku->judul }}</h6>
        <div style="font-size: 10px; color: blue;">
 
</div>
        <p><i class="bi bi-person"></i> {{ $item->buku->pengarang }}</p>
        <p><i class="bi bi-calendar"></i> Pinjam: {{ $item->tanggal_pinjam }} &nbsp;|&nbsp; Kembali: {{ $item->tanggal_kembali }}</p>
        @if($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString())
   @php $hariTerlambat = intval(now()->diffInDays($item->tanggal_kembali)); @endphp
    <div style="background:#f8d7da;color:#721c24;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:600;margin-top:5px">
        ⚠️ Terlambat {{ $hariTerlambat }} hari! Segera kembalikan buku ini.
    </div>
@endif
        
        {{-- Tampilan Denda yang benar --}}
        @if($item->status == 'dipinjam' && isset($item->taksiran_denda) && $item->taksiran_denda > 0)
            <div class="mt-1 text-danger fw-bold" style="font-size: 11px;">
                <i class="bi bi-coin"></i> Denda Berjalan: Rp {{ number_format($item->taksiran_denda, 0, ',', '.') }} 
                <span class="text-muted">({{ $item->terlambat_hari }} hari terlambat)</span>
            </div>
            
        @endif
    </div>

    <div class="d-flex align-items-center gap-2">
        @if($item->status == 'dipinjam')
            <span class="status-badge status-dipinjam">Dipinjam</span>
            <form action="{{ route('peminjaman.kembalikan', $item->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-success text-white py-1 px-3" style="border-radius: 20px; font-size: 11px; font-weight: 600;" onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan
                </button>
            </form>
        @elseif($item->status == 'menunggu_konfirmasi')
            <span class="status-badge bg-info text-white">Menunggu Konfirmasi</span>
        @else
            <span class="status-badge status-dikembalikan">Dikembalikan</span>
        @endif
    </div>
</div>
@endforeach

        @else

            <div class="empty-state">

                <i class="bi bi-book"></i>

                <p>Belum ada riwayat peminjaman</p>

            </div>

        @endif

    </div>



</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<form id="formFoto" method="POST" action="{{ route('profil.foto') }}" enctype="multipart/form-data" style="display:none">
    @csrf
    <input type="file" name="foto" id="inputFoto">
</form>

<script>
function uploadFoto(input) {
    if (input.files && input.files[0]) {
        document.getElementById('inputFoto').files = input.files;
        document.getElementById('formFoto').submit();
    }
}
function toggleToko() {
    const toko = document.getElementById('tokoBackground');
    toko.style.display = toko.style.display === 'none' ? 'block' : 'none';
}

function uploadFotoFunc(input) {
    if (input.files && input.files[0]) {
        document.getElementById('inputFoto').files = input.files;
        document.getElementById('formFoto').submit();
    }
}
</script>
<form id="formFoto" method="POST" action="{{ route('profil.foto') }}" enctype="multipart/form-data" style="display:none">
    @csrf
    <input type="file" name="foto" id="inputFoto">
</form>
<script>
    @if(session('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
</script>
<script>
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
}
</script>
<script>
// Simpan posisi scroll sebelum submit form Kembalikan
document.querySelectorAll('form[action*="kembalikan"]').forEach(function(form) {
    form.addEventListener('submit', function() {
        sessionStorage.setItem('profilScrollPos', window.scrollY);
    });
});

// Balikin posisi scroll setelah halaman selesai dimuat, baru tampilkan halamannya
window.addEventListener('load', function() {
    const scrollPos = sessionStorage.getItem('profilScrollPos');
    if (scrollPos !== null) {
        window.scrollTo(0, parseInt(scrollPos));
        sessionStorage.removeItem('profilScrollPos');
    }
    document.documentElement.style.visibility = 'visible';
});
</script>
</body>

</html>