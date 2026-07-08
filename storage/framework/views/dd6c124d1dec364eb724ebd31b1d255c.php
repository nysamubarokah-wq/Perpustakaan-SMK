<?php $__env->startSection('header_title', 'Persetujuan Pengembalian'); ?>

<?php $__env->startSection('content'); ?>
<style>
    @media (max-width: 768px) {
        .embali-tbl-wrap { display: none; }
        .embali-mobile { display: block; }
        .kembali-header { flex-direction: column; align-items: flex-start !important; gap: 10px !important; }
        .kembali-header .btn { width: 100%; text-align: center; }
    }
    @media (min-width: 769px) {
        .embali-mobile { display: none; }
    }
    .embali-card {
        background: white;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .embali-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .embali-card-user {
        font-weight: 700;
        font-size: 14px;
        color: #222;
    }
    .embali-card-book {
        font-size: 13px;
        color: #555;
        margin-top: 2px;
    }
    .embali-card-meta {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: #888;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .embali-card-meta i { font-size: 12px; margin-right: 3px; }
    .embali-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f0f0f0;
    }
    .embali-card-footer .btn {
        padding: 8px 20px;
        font-size: 12px;
        border-radius: 8px;
        font-weight: 600;
    }
</style>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 kembali-header">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <i class="bi bi-arrow-counterclockwise fs-4 text-success"></i>
        <h5 class="fw-bold m-0" style="font-size:16px;color:#222">Daftar Persetujuan Pengembalian</h5>
        <?php if($persetujuan->count() > 0): ?>
            <span class="badge bg-warning text-dark rounded-pill px-3">
                <?php echo e($persetujuan->count()); ?> menunggu
            </span>
        <?php endif; ?>
    </div>
    <?php if($persetujuan->count() > 0): ?>
    <form action="<?php echo e(route('admin.pengembalian.setujuiSemua')); ?>" method="POST" onsubmit="return confirm('Setujui semua pengembalian?')">
        <?php echo csrf_field(); ?>
        <button type="submit"
                style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-check2-all"></i> Setujui Semua
        </button>
    </form>
    <?php endif; ?>
</div>


<div class="embali-tbl-wrap">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">Anggota</th>
                <th style="width: 25%">Buku</th>
                <th style="width: 12%">Tgl Pinjam</th>
                <th style="width: 12%">Tgl Kembali</th>
                <th style="width: 16%">Denda</th>
                <th style="width: 10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $persetujuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td class="text-capitalize"><?php echo e($item->anggota->nama ?? '-'); ?></td>
                <td><?php echo e($item->buku->judul ?? '-'); ?></td>
                <td><?php echo e($item->tanggal_pinjam); ?></td>
                <td><?php echo e($item->tanggal_kembali); ?></td>
                <td>
                    <?php if($item->taksiran_denda > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2 py-1 mb-1">Terlambat <?php echo e((int)$item->terlambat_hari); ?> Hari</span>
                        <div class="fw-bold text-danger" style="font-size: 13px;">Rp <?php echo e(number_format($item->taksiran_denda, 0, ',', '.')); ?></div>
                    <?php else: ?>
                        <span class="badge bg-success rounded-pill px-2 py-1">Tepat Waktu</span>
                        <div class="text-muted" style="font-size: 12px;">Tidak ada denda</div>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="<?php echo e(route('admin.pengembalian.setujui', $item->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menyetujui pengembalian buku ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <button type="submit"
                                style="padding:6px 14px;background:#d4edda;color:#1a6e35;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                            <i class="bi bi-check-lg"></i> Setujui
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center text-muted">Tidak ada data persetujuan pengembalian.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="embali-mobile" style="padding:12px">
    <?php $__empty_1 = true; $__currentLoopData = $persetujuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="embali-card">
        <div class="embali-card-top">
            <div>
                <div class="embali-card-user">
                    <i class="bi bi-person-fill" style="color:#1a6e35;font-size:13px"></i>
                    <?php echo e($item->anggota->nama ?? '-'); ?>

                </div>
                <div class="embali-card-book">
                    <i class="bi bi-book" style="font-size:12px"></i>
                    <?php echo e($item->buku->judul ?? '-'); ?>

                </div>
            </div>
            <div>
                <?php if($item->taksiran_denda > 0): ?>
                    <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#f8d7da;color:#721c24">
                        Terlambat <?php echo e((int)$item->terlambat_hari); ?>h
                    </span>
                <?php else: ?>
                    <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#d4edda;color:#1a6e35">Tepat Waktu</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="embali-card-meta">
            <span><i class="bi bi-calendar-event"></i> Pinjam: <?php echo e($item->tanggal_pinjam); ?></span>
            <span><i class="bi bi-calendar-check"></i> Kembali: <?php echo e($item->tanggal_kembali); ?></span>
        </div>

        <div class="embali-card-footer">
            <div>
                <?php if($item->taksiran_denda > 0): ?>
                    <div style="font-weight:700;color:#dc2626;font-size:14px">Rp <?php echo e(number_format($item->taksiran_denda, 0, ',', '.')); ?></div>
                <?php else: ?>
                    <div style="font-size:12px;color:#888">Tidak ada denda</div>
                <?php endif; ?>
            </div>
            <form action="<?php echo e(route('admin.pengembalian.setujui', $item->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menyetujui pengembalian buku ini?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <button type="submit" class="btn" style="background:#d4edda;color:#1a6e35;border:none;padding:8px 20px;font-size:12px;font-weight:600;border-radius:8px">
                    <i class="bi bi-check-lg"></i> Setujui
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:40px 20px;color:#aaa">
        <i class="bi bi-arrow-counterclockwise" style="font-size:48px;display:block;margin-bottom:12px"></i>
        <p style="font-size:14px">Tidak ada data persetujuan pengembalian.</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\pengembalian.blade.php ENDPATH**/ ?>