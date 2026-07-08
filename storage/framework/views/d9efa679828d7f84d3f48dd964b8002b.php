<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Hapus dark mode dari admin - paksa selalu light mode
        document.documentElement.classList.remove('dark-mode');
        document.body.classList.remove('dark-mode');
        localStorage.removeItem('adminDarkMode');
    </script>
    <title>Admin - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: 240px;
            background: linear-gradient(180deg, #1a6e35, #27ae60);
            padding: 25px 0;
            z-index: 200;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            padding: 0 20px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }

        .sidebar-brand img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-brand span {
            font-size: 12px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            line-height: 1.3;
            white-space: nowrap;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 12px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .sidebar-menu li a i { font-size: 18px; width: 20px; }

        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 15px 12px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 150;
        }

        .sidebar-toggle-btn {
            display: none;
            background: #1a6e35;
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            font-size: 18px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* MAIN */
        .main-content {
            margin-left: 240px;
            padding: 30px;
            min-height: 100vh;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #222;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 8px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .admin-info .avatar-init {
            width: 35px;
            height: 35px;
            background: #1a6e35;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .admin-info span { font-size: 13px; font-weight: 600; color: #333; }

        .card-admin-body { overflow-x: auto; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar-toggle-btn {
                display: flex;
            }
            .page-header h1 {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .admin-info span { display: none; }
            .sidebar-brand { padding: 0 15px 20px; }
            .sidebar-brand img { width: 36px; height: 36px; }
            .sidebar-brand span { font-size: 11px; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="<?php echo e(asset('images/logo.jpg')); ?>" alt="Logo">
        <span>SMK Maarif<br>Walisongo</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(Route::is('admin.dashboard') ? 'active' : ''); ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="<?php echo e(route('admin.scanner')); ?>" class="<?php echo e(Route::is('admin.scanner*') ? 'active' : ''); ?>"><i class="bi bi-qr-code-scan"></i> Scan Buku</a></li>
        <hr class="sidebar-divider">
        <li><a href="<?php echo e(route('buku.index')); ?>" class="<?php echo e(Route::is('buku.*') ? 'active' : ''); ?>"><i class="bi bi-book"></i> Kelola Buku</a></li>
        <li><a href="<?php echo e(route('anggota.index')); ?>" class="<?php echo e(Route::is('anggota.index') ? 'active' : ''); ?>"><i class="bi bi-mortarboard"></i> Daftar Siswa</a></li>
        <li><a href="<?php echo e(route('anggota.admin')); ?>" class="<?php echo e(Route::is('anggota.admin') ? 'active' : ''); ?>"><i class="bi bi-shield-check"></i> Daftar Admin</a></li>
        <li><a href="<?php echo e(route('peminjaman.index')); ?>" class="<?php echo e(Route::is('peminjaman.*') ? 'active' : ''); ?>"><i class="bi bi-journal-check"></i> Peminjaman</a></li>
        <li>
    <a href="<?php echo e(route('admin.pengembalian.index')); ?>" class="<?php echo e(Route::is('admin.pengembalian.*') ? 'active' : ''); ?>">
        <i class="bi bi-arrow-counterclockwise"></i> Persetujuan Kembali
        <?php
            $pendingReturns = \App\Models\Peminjaman::where(function ($q) {
                    $q->where('status', 'menunggu_pengembalian')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'menunggu_konfirmasi')
                             ->where('tipe_konfirmasi', 'kembali');
                      });
                })->count();
        ?>
        <?php if($pendingReturns > 0): ?>
            <span style="background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px"><?php echo e($pendingReturns); ?></span>
        <?php endif; ?>
    </a>
</li>
        <li><a href="<?php echo e(route('background.index')); ?>" class="<?php echo e(Route::is('background.*') ? 'active' : ''); ?>"><i class="bi bi-image"></i> Kelola Background</a></li>
        <li><a href="<?php echo e(route('admin.ulasan.index')); ?>" class="<?php echo e(Route::is('admin.ulasan.*') ? 'active' : ''); ?>"><i class="bi bi-star-fill"></i> Kelola Ulasan</a></li>
<li>
    <a href="<?php echo e(route('admin.pinjam.index')); ?>" class="<?php echo e(Route::is('admin.pinjam.*') ? 'active' : ''); ?>">
        <i class="bi bi-bookmark-check"></i> Konfirmasi Pinjam
        <?php
            $pendingBorrows = \App\Models\Peminjaman::where('status', 'menunggu_konfirmasi')
                ->where('tipe_konfirmasi', 'pinjam')->count();
        ?>
        <?php if($pendingBorrows > 0): ?>
            <span style="background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:4px"><?php echo e($pendingBorrows); ?></span>
        <?php endif; ?>
    </a>
</li>
<li>
    <a href="<?php echo e(route('admin.ebook.index')); ?>" class="<?php echo e(Route::is('admin.ebook.*') ? 'active' : ''); ?>">
        <i class="bi bi-book-half"></i> Kelola E-book
    </a>
</li>
<li>
    <a href="<?php echo e(route('admin.vip.index')); ?>" class="<?php echo e(Route::is('admin.vip.*') ? 'active' : ''); ?>">
        <i class="bi bi-star-fill"></i> Kelola VIP
    </a>
</li>
<li>
    <a href="<?php echo e(route('admin.denda.index')); ?>" class="<?php echo e(Route::is('admin.denda.*') ? 'active' : ''); ?>">
        <i class="bi bi-currency-dollar"></i> Kelola Denda
    </a>
</li>
        <hr class="sidebar-divider">
        <li>
            <form method="POST" action="<?php echo e(route('logout')); ?>" onsubmit="return confirm('Yakin ingin logout?');">
                <?php echo csrf_field(); ?>
                <button type="submit" style="background:none;border:none;width:100%;text-align:left;padding:0">
                    <a style="color:rgba(255,255,255,0.8);cursor:pointer"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h1><?php echo $__env->yieldContent('header_title'); ?> <?php echo $__env->yieldContent('title'); ?></h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php
                $adminUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
            ?>
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'admin'): ?>
                    <a href="<?php echo e(route('notifikasi.index')); ?>" class="btn btn-sm btn-light position-relative" title="Notifikasi" style="padding: 6px 10px;">
                        <i class="bi bi-bell"></i>
                        <?php if($adminUnreadCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">
                                <?php echo e($adminUnreadCount > 99 ? '99+' : $adminUnreadCount); ?>

                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <div class="admin-info">
                <?php if(auth()->user()->foto): ?>
                    <img src="<?php echo e(asset(auth()->user()->foto)); ?>" alt="Foto"
                         style="width:35px;height:35px;border-radius:50%;object-fit:cover;flex-shrink:0">
                <?php else: ?>
                    <div class="avatar-init">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?>

                    </div>
                <?php endif; ?>
                <span><?php echo e(auth()->user()->name); ?></span>
            </div>
        </div>
    </div>

    <?php echo $__env->yieldContent('content'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Hapus dark mode dari admin (paksa selalu light mode)
document.body.classList.remove('dark-mode');
localStorage.removeItem('adminDarkMode');

function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>

<script>
// Simpan scroll position sidebar sebelum pindah halaman
document.querySelectorAll('.sidebar-menu li a').forEach(link => {
    link.addEventListener('click', function() {
        sessionStorage.setItem('sidebarScroll', document.getElementById('adminSidebar').scrollTop);
        sessionStorage.setItem('mainScroll', window.scrollY);
    });
});

// Restore scroll position setelah halaman dimuat
window.addEventListener('load', function() {
    // Restore sidebar scroll
    const sidebarScroll = sessionStorage.getItem('sidebarScroll');
    if (sidebarScroll !== null) {
        document.getElementById('adminSidebar').scrollTop = parseInt(sidebarScroll);
        sessionStorage.removeItem('sidebarScroll');
    }

    // Restore main content scroll
    const mainScroll = sessionStorage.getItem('mainScroll');
    if (mainScroll !== null) {
        window.scrollTo(0, parseInt(mainScroll));
        sessionStorage.removeItem('mainScroll');
    }
});
</script>
<?php echo $__env->yieldContent('style'); ?>
<?php echo $__env->yieldContent('script'); ?>
</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\layouts\admin.blade.php ENDPATH**/ ?>