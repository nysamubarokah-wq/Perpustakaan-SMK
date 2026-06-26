<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f5f7fa; font-family:'Segoe UI',sans-serif; }
        .navbar { background:white; box-shadow:0 2px 15px rgba(0,0,0,0.1); padding:12px 20px; position:fixed; width:100%; top:0; z-index:1000; display:flex; align-items:center; justify-content:space-between; }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('koleksi.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="{{ asset('images/logo.jpg') }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
        <span style="font-size:13px;font-weight:700;color:#1a6e35;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
    </a>
    <a href="{{ route('profil.index') }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</nav>

<div style="max-width:480px;margin:90px auto 60px;padding:0 20px">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Status VIP --}}
    @if($isVip)
    <div style="background:linear-gradient(135deg,#f59e0b,#fbbf24);border-radius:20px;padding:30px;text-align:center;color:white;margin-bottom:20px;box-shadow:0 5px 25px rgba(245,158,11,0.3)">
        <div style="font-size:40px;margin-bottom:10px">⭐</div>
        <div style="font-size:20px;font-weight:700;margin-bottom:5px">Kamu sudah VIP!</div>
        <div style="font-size:14px;opacity:0.9">Aktif {{ $sisaHari }} hari lagi</div>
        <div style="font-size:12px;opacity:0.8;margin-top:5px">Expired: {{ auth()->user()->vip_expired_at->format('d M Y') }}</div>
    </div>
    @else
    <div style="background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:20px;padding:30px;text-align:center;color:white;margin-bottom:20px;box-shadow:0 5px 25px rgba(26,110,53,0.3)">
        <div style="font-size:40px;margin-bottom:10px">⭐</div>
        <div style="font-size:20px;font-weight:700;margin-bottom:5px">Upgrade ke VIP</div>
        <div style="font-size:14px;opacity:0.9">Akses semua e-book tanpa batas!</div>
    </div>
    @endif

    {{-- Info card --}}
    <div style="background:white;border-radius:16px;padding:25px;box-shadow:0 3px 15px rgba(0,0,0,0.08);margin-bottom:20px">
        <div style="font-weight:700;color:#222;margin-bottom:15px">Keuntungan VIP:</div>
        <div style="display:flex;flex-direction:column;gap:10px">
            <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#444">
                <span style="color:#1a6e35;font-size:18px">✓</span> Akses semua e-book VIP
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#444">
                <span style="color:#1a6e35;font-size:18px">✓</span> Tidak perlu bayar koin per buku
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#444">
                <span style="color:#1a6e35;font-size:18px">✓</span> Berlaku 7 hari penuh
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#444">
                <span style="color:#1a6e35;font-size:18px">✓</span> Bisa diperpanjang kapan saja
            </div>
        </div>

        <hr style="border-color:#f0f0f0;margin:20px 0">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px">
            <div>
                <div style="font-size:13px;color:#888">Harga</div>
                <div style="font-size:22px;font-weight:700;color:#1a6e35">🪙 100 Koin</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:13px;color:#888">Koin kamu</div>
                <div style="font-size:22px;font-weight:700;color:#222">🪙 {{ $user->coin ?? 0 }}</div>
            </div>
        </div>

        @if($isVip)
            <form action="{{ route('vip.beli') }}" method="POST">
                @csrf
                <button type="submit"
                        style="width:100%;padding:14px;background:linear-gradient(135deg,#f59e0b,#fbbf24);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">
                    ⭐ Perpanjang VIP 7 Hari
                </button>
            </form>
        @elseif(($user->coin ?? 0) >= 100)
            <form action="{{ route('vip.beli') }}" method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Yakin upgrade VIP 7 hari dengan 100 koin?')"
                        style="width:100%;padding:14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">
                    ⭐ Upgrade VIP Sekarang
                </button>
            </form>
        @else
            <button disabled
                    style="width:100%;padding:14px;background:#e5e7eb;color:#9ca3af;border:none;border-radius:12px;font-size:15px;font-weight:700">
                🪙 Koin Tidak Cukup (kurang {{ 100 - ($user->coin ?? 0) }} koin)
            </button>
            <div style="text-align:center;margin-top:10px;font-size:13px;color:#888">
                Mainkan mini game untuk dapat koin!
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>