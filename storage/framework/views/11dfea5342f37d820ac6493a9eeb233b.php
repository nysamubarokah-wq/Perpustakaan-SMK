<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($ebook->judul); ?> - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }

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

        .detail-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 35px;
            display: flex;
            gap: 35px;
        }

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
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: rgba(255,255,255,0.5);
        }

        .sinopsis-box {
            margin-top: 20px;
            width: 100%;
        }

        .sinopsis-box h6 {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .sinopsis-box p {
            font-size: 13px;
            color: #666;
            line-height: 1.7;
        }

        .right-section { flex: 1; min-width: 0; }

        .judul {
            font-size: 26px;
            font-weight: 800;
            color: #111;
            margin-bottom: 8px;
            line-height: 1.3;
            word-break: break-word;
        }

        .penulis {
            font-size: 15px;
            color: #666;
            margin-bottom: 20px;
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        .btn-baca {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-baca:hover { opacity: 0.9; color: white; }

        .btn-disabled {
            display: block;
            width: 100%;
            padding: 14px;
            background: #e5e7eb;
            color: #9ca3af;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .main-container { margin: 80px auto 40px; padding: 0 12px; }
            .detail-card { flex-direction: column; padding: 20px; gap: 20px; border-radius: 16px; }
            .left-section { width: 100%; min-width: unset; align-items: center; }
            .sampul-img, .sampul-placeholder { width: 160px; height: 220px; }
            .judul { font-size: 20px; }
            .penulis { font-size: 14px; margin-bottom: 15px; }
            .sinopsis-box p { font-size: 12px; }
            .btn-baca, .btn-disabled { padding: 12px; font-size: 14px; border-radius: 10px; }
            .divider { margin: 15px 0; }
        }

        @media (max-width: 400px) {
            .main-container { padding: 0 8px; }
            .detail-card { padding: 15px; border-radius: 14px; }
            .sampul-img, .sampul-placeholder { width: 140px; height: 190px; }
            .judul { font-size: 18px; }
        }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: white; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar span { color: #1a6e35 !important; }
        body.dark-mode .navbar a { color: #1a6e35 !important; }
        body.dark-mode .detail-card { background: #1e1e1e; color: white; }
        body.dark-mode .judul { color: white; }
        body.dark-mode .penulis { color: #bbb; }
        body.dark-mode .sinopsis-box h6 { color: white; }
        body.dark-mode .sinopsis-box p { color: #aaa; }
        body.dark-mode .divider { border-color: #444; }
        body.dark-mode .info-box { background: #2a2a2a !important; }
        body.dark-mode .info-label { color: #aaa !important; }
        body.dark-mode .info-value { color: white !important; }
        body.dark-mode .alert-vip { background: #2a2200 !important; }
        body.dark-mode .alert-koin { background: #1a2a1a !important; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="<?php echo e(route('koleksi.index')); ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?php echo e(asset('images/logo.jpg')); ?>" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <a href="<?php echo e(route('ebook.index')); ?>" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="main-container">

    <?php if(session('error')): ?>
        <div class="alert alert-danger mb-3" style="border-radius:12px"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

  <div class="detail-card">
    
    <div class="left-section">
        <?php if($ebook->cover): ?>
            <img src="<?php echo e(asset($ebook->cover)); ?>" class="sampul-img" alt="<?php echo e($ebook->judul); ?>">
        <?php else: ?>
            <div class="sampul-placeholder">
                <i class="bi bi-book-half"></i>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="right-section">
        
        <div style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap">
            <?php if($ebook->is_vip): ?>
                <span style="padding:5px 14px;background:#fff3cd;color:#856404;border-radius:20px;font-size:12px;font-weight:600">
                    ⭐ Konten VIP
                </span>
            <?php elseif($ebook->harga_koin > 0): ?>
                <span style="padding:5px 14px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:12px;font-weight:600">
                    🪙 <?php echo e($ebook->harga_koin); ?> Koin
                </span>
            <?php else: ?>
                <span style="padding:5px 14px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:12px;font-weight:600">
                    ✓ Gratis
                </span>
            <?php endif; ?>
        </div>

        <h1 class="judul"><?php echo e($ebook->judul); ?></h1>
        <p class="penulis"><i class="bi bi-person"></i> <?php echo e($ebook->penulis); ?></p>

        
        <?php if($ebook->sinopsis): ?>
        <div class="sinopsis-box" style="margin-bottom: 20px;">
            <h6><i class="bi bi-file-text" style="color:#7c3aed"></i> Sinopsis</h6>
            <p><?php echo e($ebook->sinopsis); ?></p>
        </div>
        <?php endif; ?>

        <hr class="divider">

        
        <div style="background:#f8f9fa;border-radius:12px;padding:15px 18px;margin-bottom:20px" class="info-box">
            <div style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px" class="info-label">Koin Kamu</div>
            <div style="font-size:18px;font-weight:700;color:#333" class="info-value">🪙 <?php echo e(auth()->user()->coin ?? 0); ?> Coin</div>
        </div>

        
        <?php if($bisaDiakses): ?>
            <a href="<?php echo e(route('ebook.baca', $ebook->id)); ?>" class="btn-baca">
                <i class="bi bi-book-open"></i> Baca Sekarang
            </a>
        <?php else: ?>
            
            <?php if($ebook->is_vip): ?>
                <div style="background:#fff8e1;border-radius:12px;padding:15px;text-align:center;margin-bottom:15px" class="alert-vip">
                    <div style="font-size:13px;color:#888;margin-bottom:5px">E-book ini khusus member VIP</div>
                    <div style="font-weight:700;color:#f59e0b">⭐ Hubungi admin untuk upgrade VIP</div>
                </div>
                <div class="btn-disabled">🔒 Khusus Member VIP</div>
            <?php elseif($ebook->harga_koin > 0): ?>
                <div style="background:#f0fdf4;border-radius:12px;padding:15px;text-align:center;margin-bottom:15px" class="alert-koin">
                    <div style="font-size:13px;color:#888;margin-bottom:5px">Butuh <strong>🪙 <?php echo e($ebook->harga_koin); ?> koin</strong> untuk membaca</div>
                    <?php if((auth()->user()->coin ?? 0) < $ebook->harga_koin): ?>
                        <div style="font-weight:700;color:#e74c3c;font-size:13px">Koin kamu tidak cukup</div>
                    <?php endif; ?>
                </div>
                <?php if((auth()->user()->coin ?? 0) >= $ebook->harga_koin): ?>
                    <form action="<?php echo e(route('ebook.beli', $ebook->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-baca">
                            🪙 Baca dengan <?php echo e($ebook->harga_koin); ?> Koin
                        </button>
                    </form>
                <?php else: ?>
                    <div class="btn-disabled">🪙 Koin Tidak Cukup</div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
}
</script>

</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/ebook/show.blade.php ENDPATH**/ ?>