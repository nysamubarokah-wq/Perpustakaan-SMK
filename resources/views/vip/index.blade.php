<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background:#f5f7fa; font-family:'Segoe UI',sans-serif; overflow-x: hidden; }
        .navbar { background:white; box-shadow:0 2px 15px rgba(0,0,0,0.1); padding:12px 20px; position:fixed; width:100%; top:0; z-index:1000; display:flex; align-items:center; justify-content:space-between; }
        .vip-container { max-width:480px; margin:90px auto 60px; padding:0 20px; }
        .vip-card { background:white; border-radius:16px; padding:25px; box-shadow:0 3px 15px rgba(0,0,0,0.08); margin-bottom:20px; }
        .vip-badge { font-size:40px; margin-bottom:10px; }
        .vip-title { font-size:20px; font-weight:700; margin-bottom:5px; }
        .vip-subtitle { font-size:14px; opacity:0.9; }
        .vip-expired { font-size:12px; opacity:0.8; margin-top:5px; }
        .vip-benefit { display:flex; align-items:center; gap:10px; font-size:14px; color:#444; }
        .vip-check { color:#1a6e35; font-size:18px; }
        .vip-price-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px; }
        .vip-price-label { font-size:13px; color:#888; }
        .vip-price-value { font-size:22px; font-weight:700; }
        .vip-btn { width:100%; padding:14px; color:white; border:none; border-radius:12px; font-size:15px; font-weight:700; cursor:pointer; }
        .vip-btn-gold { background:linear-gradient(135deg,#f59e0b,#fbbf24); }
        .vip-btn-green { background:linear-gradient(135deg,#1a6e35,#27ae60); }
        .vip-btn-disabled { background:#e5e7eb; color:#9ca3af; cursor:not-allowed; }
        .vip-note { text-align:center; margin-top:10px; font-size:13px; color:#888; }

        @media (max-width: 768px) {
            .navbar { padding: 10px 14px; }
            .vip-container { margin: 76px auto 40px; padding: 0 12px; }
            .vip-card { padding: 20px; border-radius: 14px; }
            .vip-badge { font-size: 34px; }
            .vip-title { font-size: 18px; }
            .vip-price-value { font-size: 20px; }
            .vip-btn { padding: 12px; font-size: 14px; }
            .vip-benefit { font-size: 13px; }
        }

        @media (max-width: 400px) {
            .vip-container { padding: 0 8px; }
            .vip-card { padding: 16px; }
            .vip-price-row { flex-direction: column; align-items: flex-start; gap: 4px; }
        }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar span { color: #1a6e35 !important; }
        body.dark-mode .vip-card { background: #1e1e1e; }
        body.dark-mode .vip-title { color: #fff; }
        body.dark-mode .vip-price-label { color: #aaa; }
        body.dark-mode .vip-benefit { color: #ccc; }
        body.dark-mode .vip-note { color: #888; }
    </style>
</head>
<body>
@php
    $backUrl = (session('notifikasi_from_notif') || request()->query('from') === 'notif') ? '/notifikasi' : route('profil.index');
@endphp

<nav class="navbar">
    <a href="{{ route('koleksi.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="{{ asset('images/logo.jpg') }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
        <span style="font-size:13px;font-weight:700;color:#1a6e35;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
    </a>
    <a href="{{ $backUrl }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</nav>

<div class="vip-container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Status VIP --}}
    @if($isVip)
    <div class="vip-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);border-radius:20px;padding:30px;text-align:center;color:white;margin-bottom:20px;box-shadow:0 5px 25px rgba(245,158,11,0.3)">
        <div class="vip-badge">⭐</div>
        <div class="vip-title">Kamu sudah VIP!</div>
        <div class="vip-subtitle">Aktif {{ $sisaHari }} hari lagi</div>
        <div class="vip-expired">Expired: {{ auth()->user()->vip_expired_at->format('d M Y') }}</div>
    </div>
    @else
    <div class="vip-card" style="background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:20px;padding:30px;text-align:center;color:white;margin-bottom:20px;box-shadow:0 5px 25px rgba(26,110,53,0.3)">
        <div class="vip-badge">⭐</div>
        <div class="vip-title">Upgrade ke VIP</div>
        <div class="vip-subtitle">Akses semua e-book tanpa batas!</div>
    </div>
    @endif

    {{-- Info card --}}
    <div class="vip-card">
        <div style="font-weight:700;color:#222;margin-bottom:15px">Keuntungan VIP:</div>
        <div style="display:flex;flex-direction:column;gap:10px">
            <div class="vip-benefit">
                <span class="vip-check">✓</span> Akses semua e-book VIP
            </div>
            <div class="vip-benefit">
                <span class="vip-check">✓</span> Tidak perlu bayar koin per buku
            </div>
            <div class="vip-benefit">
                <span class="vip-check">✓</span> Berlaku 7 hari penuh
            </div>
            <div class="vip-benefit">
                <span class="vip-check">✓</span> Bisa diperpanjang kapan saja
            </div>
        </div>

        <hr style="border-color:#f0f0f0;margin:20px 0">

        <div class="vip-price-row">
            <div>
                <div class="vip-price-label">Harga</div>
                <div class="vip-price-value" style="color:#1a6e35">🪙 100 Koin</div>
            </div>
            <div style="text-align:right">
                <div class="vip-price-label">Koin kamu</div>
                <div class="vip-price-value" style="color:#222">🪙 {{ $user->coin ?? 0 }}</div>
            </div>
        </div>

        @if($isVip)
            <form action="{{ route('vip.beli') }}" method="POST">
                @csrf
                <button type="submit"
                        class="vip-btn vip-btn-gold">
                    ⭐ Perpanjang VIP 7 Hari
                </button>
            </form>
        @elseif(($user->coin ?? 0) >= 100)
            <form action="{{ route('vip.beli') }}" method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Yakin upgrade VIP 7 hari dengan 100 koin?')"
                        class="vip-btn vip-btn-green">
                    ⭐ Upgrade VIP Sekarang
                </button>
            </form>
        @else
            <button disabled
                    class="vip-btn vip-btn-disabled">
                🪙 Koin Tidak Cukup (kurang {{ 100 - ($user->coin ?? 0) }} koin)
            </button>
            <div class="vip-note">
                Mainkan mini game untuk dapat koin!
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@include('components.fab-scan')

</body>
</html>