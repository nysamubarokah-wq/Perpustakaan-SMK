<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { overflow-x: hidden; width: 100%; }
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
            max-width: 1100px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            margin-bottom: 25px;
        }

        .buku-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.25,0.8,0.25,1);
            height: 100%;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .buku-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }

        .buku-cover {
            height: 160px;
            position: relative;
            overflow: hidden;
        }
        @media (max-width: 768px) { .buku-cover { height: 140px; } }
        @media (max-width: 480px) { .buku-cover { height: 130px; } }

        .buku-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .buku-card:hover .buku-cover img {
            transform: scale(1.05);
        }

        .buku-cover-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: rgba(255,255,255,0.5);
        }

        .cover-1 { background: linear-gradient(135deg, #1a6e35, #27ae60); }
        .cover-2 { background: linear-gradient(135deg, #2c3e50, #3498db); }
        .cover-3 { background: linear-gradient(135deg, #8e44ad, #e056fd); }
        .cover-4 { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .cover-5 { background: linear-gradient(135deg, #d35400, #e67e22); }
        .cover-6 { background: linear-gradient(135deg, #16a085, #1abc9c); }

        .buku-body {
            padding: 8px 10px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 1px;
        }

        .buku-body h5 {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2em;
            line-height: 1.25;
        }

        .buku-meta { font-size: 11px; color: #888; margin-bottom: 0; display: flex; align-items: center; gap: 4px; }

        .stok-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            align-self: flex-start;
        }

        .stok-ada { background: #d4edda; color: #1a6e35; }
        .stok-habis { background: #f8d7da; color: #721c24; }

        .btn-detail-link {
            display: block;
            width: 100%;
            padding: 8px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            margin-top: auto;
            transition: all 0.3s;
        }

        .btn-detail-link:hover {
            opacity: 0.9;
            transform: scale(1.02);
            color: white;
        }

        .empty-state { text-align: center; padding: 40px 0; }
        .empty-state i { font-size: 50px; color: #ddd; margin-bottom: 12px; display: block; }
        .empty-state p { color: #aaa; font-size: 14px; }
        .empty-state a {
            display: inline-block;
            margin-top: 12px;
            background: linear-gradient(135deg,#1a6e35,#27ae60);
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
        }

        @media (max-width: 992px) {
            .main-container { margin-top: 85px; padding: 0 16px; margin-bottom: 30px; }
        }

        @media (max-width: 768px) {
            .main-container { margin-top: 80px; padding: 0 12px; margin-bottom: 30px; }
            .page-title { font-size: 18px; }
            .buku-cover-placeholder { font-size: 36px; }
            .buku-body { padding: 8px 10px; }
            .buku-body h5 { font-size: 12px; min-height: 2em; }
            .buku-meta { font-size: 11px; }
            .stok-badge { font-size: 10px; padding: 2px 8px; }
            .btn-detail-link { padding: 8px; font-size: 12px; }
            .empty-state { padding: 40px 0; }
            .empty-state i { font-size: 45px; }
        }

        @media (max-width: 480px) {
            .main-container { margin-top: 75px; padding: 0 10px; margin-bottom: 25px; }
            .page-title { font-size: 16px; margin-bottom: 16px; }
            .buku-cover-placeholder { font-size: 32px; }
            .buku-body { padding: 6px 8px; }
            .buku-body h5 { font-size: 11px; min-height: 1.8em; }
            .buku-meta { font-size: 10px; }
            .stok-badge { font-size: 9px; padding: 2px 7px; }
            .btn-detail-link { padding: 6px; font-size: 11px; }
            .empty-state { padding: 30px 0; }
            .empty-state i { font-size: 40px; }
            .empty-state a { padding: 6px 16px; font-size: 11px; }
        }

        /* DARK MODE */
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar span { color: #1a6e35 !important; }
        body.dark-mode .buku-card { background: #1e1e1e; }
        body.dark-mode .page-title,
        body.dark-mode .buku-body h5 { color: #ffffff; }
        body.dark-mode .buku-meta { color: #bdbdbd; }
        body.dark-mode .empty-state i { color: #444; }
        body.dark-mode .empty-state p { color: #888; }
        body.dark-mode form[action*="favorit"] button {
            background: rgba(40,40,40,0.9) !important;
        }
    </style>
</head>
<body>

<?php
    if (request()->has('from')) {
        session(['favorit_dari' => request('from')]);
    }
    $kembaliUrl = session('favorit_dari') === 'profil' ? route('profil.index') : route('koleksi.index');
?>

<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="<?php echo e(route('koleksi.index')); ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?php echo e(asset('images/logo.jpg')); ?>" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <a href="<?php echo e($kembaliUrl); ?>" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="main-container">

    <?php if(session('success')): ?>
        <div class="alert alert-success mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <h1 class="page-title">
        <i class="bi bi-heart-fill" style="color:#e74c3c"></i> Buku Favorit Saya
        <?php if($favorit->count() > 0): ?>
            <span style="font-size:14px;color:#888;font-weight:400">(<?php echo e($favorit->count()); ?> buku)</span>
        <?php endif; ?>
    </h1>

    <?php if($favorit->count() > 0): ?>
    <div class="row g-3">
        <?php $__currentLoopData = $favorit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $item = $fav->buku; ?>
        <?php if($item): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="buku-card">
                <div class="buku-cover cover-<?php echo e(($index % 6) + 1); ?>">
                    <?php if($item->sampul): ?>
                        <img src="<?php echo e(asset($item->sampul)); ?>" alt="<?php echo e($item->judul); ?>">
                    <?php else: ?>
                        <div class="buku-cover-placeholder">
                            <i class="bi bi-book"></i>
                        </div>
                    <?php endif; ?>

                    
                    <form method="POST" action="<?php echo e(route('buku.favorit', $item->id)); ?>"
                        style="position:absolute;top:8px;right:8px;margin:0">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            style="width:32px;height:32px;border-radius:50%;border:none;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.15)">
                            <i class="bi bi-heart-fill" style="color:#e74c3c;font-size:15px"></i>
                        </button>
                    </form>
                </div>
                <div class="buku-body">
                    <h5><?php echo e($item->judul); ?></h5>
                    <p class="buku-meta"><i class="bi bi-person"></i> <?php echo e($item->pengarang); ?></p>
                    <?php if($item->genre): ?>
                    <span class="book-card-genre" style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:9px;font-weight:600;background:#f0f0f0;color:#666;margin-top:3px;align-self:flex-start"><?php echo e($item->genre); ?></span>
                    <?php endif; ?>
                    <div style="margin-top:auto;padding-top:4px">
                        <span class="stok-badge <?php echo e($item->stok > 0 ? 'stok-ada' : 'stok-habis'); ?>">
                            <?php echo e($item->stok > 0 ? 'Tersedia ('.$item->stok.')' : 'Tidak Tersedia'); ?>

                        </span>
                    </div>
                    <a href="<?php echo e(route('buku.detail', $item->id)); ?>"
                        class="btn-detail-link" style="margin-top:6px">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-heart"></i>
        <p>Kamu belum punya buku favorit</p>
        <a href="<?php echo e(route('koleksi.index')); ?>"><i class="bi bi-collection"></i> Jelajahi Koleksi</a>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
}
</script>


</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/favorit/index.blade.php ENDPATH**/ ?>