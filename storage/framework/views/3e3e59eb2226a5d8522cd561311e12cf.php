<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="bi bi-book"></i> Perpustakaan Digital</a>
            <div class="navbar-nav ms-auto">
                <?php if(auth()->guard()->check()): ?>
                    <a class="nav-link" href="<?php echo e(route('buku.index')); ?>"><i class="bi bi-book"></i> Buku</a>
                    <a class="nav-link" href="<?php echo e(route('anggota.index')); ?>"><i class="bi bi-people"></i> Anggota</a>
                    <a class="nav-link" href="<?php echo e(route('peminjaman.index')); ?>"><i class="bi bi-journal-check"></i> Peminjaman</a>
                    <?php
                        $userUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    ?>
                    <a class="nav-link position-relative" href="<?php echo e(route('notifikasi.index')); ?>">
                        <i class="bi bi-bell"></i>
                        <?php if($userUnreadCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">
                                <?php echo e($userUnreadCount > 99 ? '99+' : $userUnreadCount); ?>

                            </span>
                        <?php endif; ?>
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline" onsubmit="return confirm('Yakin ingin logout?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-link nav-link">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\layouts\app.blade.php ENDPATH**/ ?>