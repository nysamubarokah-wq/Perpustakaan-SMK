<?php
    $user = auth()->user();
    $filters = ['semua' => 'Semua', 'dipinjam' => 'Sedang Dipinjam', 'dikembalikan' => 'Sudah Dikembalikan', 'terlambat' => 'Terlambat', 'menunggu' => 'Menunggu Konfirmasi'];
    $currentFilter = request('filter', 'semua');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { overflow-x: hidden; -webkit-text-size-adjust: 100%; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
        img { max-width: 100%; height: auto; }
        button, a { touch-action: manipulation; }

        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .navbar .container-fluid { max-width: 100%; }

        .main-container {
            max-width: 900px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .profil-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .profil-header {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            padding: 30px 35px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profil-header h2 {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .profil-body { padding: 25px 30px; }

        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .filter-tab:hover { border-color: #1a6e35; color: #1a6e35; }
        .filter-tab.active { background: #1a6e35; border-color: #1a6e35; color: white; }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-bar input:focus { border-color: #1a6e35; }
        .search-bar button {
            padding: 12px 20px;
            background: #1a6e35;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
        }

        .history-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 18px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .history-item:hover { background: #f9f9f9; }
        .history-item:last-child { border-bottom: none; }

        .history-cover {
            width: 50px;
            height: 65px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .cover-placeholder {
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

        .history-info { flex: 1; min-width: 0; }

        .history-title {
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .history-code {
            font-family: monospace;
            font-size: 11px;
            color: #1a6e35;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .history-meta {
            font-size: 11px;
            color: #888;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .history-meta span { display: flex; align-items: center; gap: 4px; }
        .history-meta i { font-size: 10px; }

        .history-right { text-align: right; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-dipinjam { background: #fff3cd; color: #856404; }
        .status-dikembalikan { background: #d4edda; color: #1a6e35; }
        .status-terlambat { background: #f8d7da; color: #721c24; }
        .status-menunggu { background: #e0e7ff; color: #4338ca; }

        .fine-info {
            font-size: 11px;
            color: #dc3545;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 50px 0;
            color: #aaa;
        }

        .empty-state i { font-size: 50px; margin-bottom: 10px; display: block; }
        .empty-state p { font-size: 14px; }

        .pagination-wrap { margin-top: 25px; text-align: center; }

        .pagination-wrap .pagination { gap: 5px; justify-content: center; display: inline-flex; }
        .pagination-wrap .page-link {
            border-radius: 10px;
            border: none;
            color: #333;
            font-weight: 600;
            padding: 8px 14px;
            background: #f0f0f0;
        }
        .pagination-wrap .page-item.active .page-link { background: #1a6e35; color: white; }
        .pagination-wrap .page-link:hover { background: #e8f5ec; }

        @media (max-width: 768px) {
            .main-container { margin-top: 80px; padding: 0 12px; }
            .profil-card { border-radius: 15px; }
            .profil-header { padding: 20px 20px; gap: 15px; }
            .profil-header h2 { font-size: 18px; }
            .profil-body { padding: 20px 15px; }
            .history-item { padding: 15px; gap: 12px; }
            .history-cover, .cover-placeholder { width: 45px; height: 58px; }
            .history-title { font-size: 13px; }
        }

        @media (max-width: 420px) {
            .main-container { margin-top: 70px; padding: 0 10px; margin-bottom: 50px; }
            .profil-card { border-radius: 12px; }
            .profil-header { padding: 15px; }
            .profil-body { padding: 15px; }
            .history-item { padding: 12px; gap: 10px; }
            .history-cover, .cover-placeholder { width: 40px; height: 52px; }
            .history-title { font-size: 12px; }
            .history-meta { font-size: 10px; gap: 6px; }
            .filter-tab { padding: 6px 12px; font-size: 12px; }
        }

        body.dark-mode { background: #121212; color: #fff; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .profil-card { background: #1e1e1e; }
        body.dark-mode .profil-header { background: #1e1e1e; }
        body.dark-mode .profil-header h2, body.dark-mode .history-title { color: #fff; }
        body.dark-mode .profil-body { background: #1e1e1e; }
        body.dark-mode .history-item { border-color: #333; }
        body.dark-mode .history-item:hover { background: #2a2a2a; }
        body.dark-mode .filter-tab { border-color: #333; background: #1e1e1e; color: #aaa; }
        body.dark-mode .filter-tab:hover { border-color: #27ae60; color: #27ae60; }
        body.dark-mode .filter-tab.active { background: #27ae60; border-color: #27ae60; }
        body.dark-mode .search-bar input { background: #2a2a2a; border-color: #333; color: #fff; }
        body.dark-mode .history-meta { color: #888; }
        body.dark-mode .empty-state { color: #666; }
        body.dark-mode .page-link { background: #2a2a2a; color: #ccc; }
        body.dark-mode .pagination-wrap .page-item.active .page-link { background: #27ae60; }
        body.dark-mode .navbar span, body.dark-mode .navbar a { color: #fff !important; }
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
                <a href="<?php echo e(route('profil.index')); ?>" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="profil-card">
            <div class="profil-header">
                <a href="<?php echo e(route('profil.index')); ?>" style="background:rgba(255,255,255,0.2);border:none;color:white;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:background 0.2s;flex-shrink:0">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2><i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman</h2>
            </div>
            <div class="profil-body">
                <div class="filter-tabs">
                    <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('profil.riwayat', array_merge(request()->except('filter'), ['filter' => $key]))); ?>"
                           class="filter-tab <?php echo e($currentFilter === $key ? 'active' : ''); ?>"><?php echo e($label); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <form method="GET" action="<?php echo e(route('profil.riwayat')); ?>" class="search-bar">
                    <input type="hidden" name="filter" value="<?php echo e($currentFilter); ?>">
                    <input type="text" name="cari" placeholder="Cari judul atau kode buku..." value="<?php echo e(request('cari')); ?>">
                    <button type="submit"><i class="bi bi-search"></i></button>
                    <?php if(request('cari')): ?>
                        <a href="<?php echo e(route('profil.riwayat', ['filter' => $currentFilter])); ?>" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>

                <?php if($peminjaman->count() > 0): ?>
                    <?php $__currentLoopData = $peminjaman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badgeClass = 'status-dipinjam';
                            $badgeText = 'Dipinjam';
                            if ($item->status === 'dikembalikan') {
                                $badgeClass = 'status-dikembalikan';
                                $badgeText = 'Dikembalikan';
                            } elseif ($item->status === 'terlambat') {
                                $badgeClass = 'status-terlambat';
                                $badgeText = 'Terlambat';
                            } elseif (in_array($item->status, ['menunggu_konfirmasi', 'menunggu_pengembalian'])) {
                                $badgeClass = 'status-menunggu';
                                $badgeText = $item->tipe_konfirmasi === 'kembali' ? 'Menunggu Konfirmasi' : 'Menunggu Konfirmasi';
                            } elseif ($item->status === 'dipinjam' && $item->terlambat_hari > 0) {
                                $badgeClass = 'status-terlambat';
                                $badgeText = 'Terlambat';
                            }
                        ?>
                        <a href="<?php echo e(route('profil.riwayat.detail', $item->id)); ?>" class="history-item">
                            <?php if($item->buku && $item->buku->sampul): ?>
                                <img src="<?php echo e(asset($item->buku->sampul)); ?>" class="history-cover" alt="<?php echo e($item->buku->judul); ?>">
                            <?php else: ?>
                                <div class="cover-placeholder"><i class="bi bi-book"></i></div>
                            <?php endif; ?>
                            <div class="history-info">
                                <div class="history-title"><?php echo e($item->buku->judul ?? 'Buku tidak ditemukan'); ?></div>
                                <div class="history-code">
                                    <?php if($item->eksemplar): ?><?php echo e($item->eksemplar->kode_buku); ?><?php endif; ?>
                                </div>
                                <div class="history-meta">
                                    <span><i class="bi bi-calendar"></i> <?php echo e($item->tanggal_pinjam); ?></span>
                                    <span><i class="bi bi-calendar-x"></i> <?php echo e($item->tanggal_kembali); ?></span>
                                    <?php if($item->tanggal_dikembalikan): ?>
                                        <span><i class="bi bi-check-circle"></i> <?php echo e($item->tanggal_dikembalikan); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if($item->terlambat_hari > 0): ?>
                                    <div class="fine-info mt-1"><i class="bi bi-exclamation-triangle"></i> Terlambat <?php echo e($item->terlambat_hari); ?> hari</div>
                                <?php endif; ?>
                                <?php if($item->denda): ?>
                                    <div class="fine-info"><i class="bi bi-currency-dollar"></i> Denda: Rp <?php echo e(number_format($item->denda->jumlah_denda ?? $item->denda, 0, ',', '.')); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="history-right">
                                <span class="status-badge <?php echo e($badgeClass); ?>"><?php echo e($badgeText); ?></span>
                                <?php if($item->terlambat_hari > 0): ?>
                                    <div class="fine-info">Rp <?php echo e(number_format($item->taksiran_denda, 0, ',', '.')); ?></div>
                                <?php endif; ?>
                                <?php if($item->status == 'dipinjam'): ?>
                                    <form action="<?php echo e(route('peminjaman.kembalikan', $item->id)); ?>" method="POST" class="d-inline mt-1">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button type="submit" class="btn btn-sm btn-success text-white py-1 px-3" style="border-radius: 20px; font-size: 11px; font-weight: 600;" onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Kembalikan
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="pagination-wrap">
                        <?php echo e($peminjaman->withQueryString()->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-journal-x"></i>
                        <p>Tidak ada riwayat peminjaman</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        if(localStorage.getItem('darkMode') === 'enabled'){
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\profil\riwayat.blade.php ENDPATH**/ ?>