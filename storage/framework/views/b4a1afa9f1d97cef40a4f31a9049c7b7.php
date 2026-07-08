<?php $__env->startSection('title', 'Kelola Denda'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <h5 class="mb-0">Total Denda:</h5>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <form action="<?php echo e(route('admin.denda.index')); ?>" method="GET" class="d-flex align-items-center gap-2">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari anggota/buku..."
                   style="padding:8px 14px;border:1px solid #ddd;border-radius:10px;font-size:13px;width:180px">
            <select name="filter_pengembalian" onchange="this.form.submit()"
                    style="padding:8px 14px;border:1px solid #ddd;border-radius:10px;font-size:13px;background:white">
                <option value="">Semua Status</option>
                <option value="belum" <?php echo e(request('filter_pengembalian') === 'belum' ? 'selected' : ''); ?>>Belum Kembali</option>
                <option value="sudah" <?php echo e(request('filter_pengembalian') === 'sudah' ? 'selected' : ''); ?>>Sudah Kembali</option>
            </select>
            <button type="submit"
                    style="padding:8px 14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;cursor:pointer">
                <i class="bi bi-search"></i>
            </button>
        </form>
        <?php if($dendas->where('status', 'belum_dibayar')->count() > 0): ?>
        <form action="<?php echo e(route('admin.denda.lunasi-semua')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit"
                    style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer"
                    onclick="return confirm('Lunasi semua denda?')">
                <i class="bi bi-check-all"></i> Lunasi Semua
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card-stat danger h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px">Total Belum Bayar</p>
                        <h3 class="mb-0" style="color:#e74c3c">Rp <?php echo e(number_format($totalBelumBayar, 0, ',', '.')); ?></h3>
                    </div>
                    <div class="icon-stat" style="background:#fce4e4;color:#e74c3c">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card-stat success h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px">Total Sudah Bayar</p>
                        <h3 class="mb-0" style="color:#27ae60">Rp <?php echo e(number_format($totalSudahBayar, 0, ',', '.')); ?></h3>
                    </div>
                    <div class="icon-stat" style="background:#e8f5e9;color:#27ae60">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form id="formHapusBanyak" action="<?php echo e(route('admin.denda.hapus-banyak')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="d-flex justify-content-end mb-2">
            <button type="submit" id="btnHapusBanyak" class="btn btn-sm btn-danger" style="display:none" onclick="return confirm('Yakin hapus denda yang dipilih?')">
                <i class="bi bi-trash"></i> Hapus Terpilih (<span id="jumlahTerpilih">0</span>)
            </button>
        </div>
    </form>

    <div class="card border-0 shadow-sm" style="border-radius:15px">
        <div class="card-body p-3 p-md-4">
            <h5 class="mb-3 mb-md-4">Daftar Denda</h5>
            <div class="table-responsive">
                <table class="table table-denda table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap"><input type="checkbox" id="checkAll"></th>
                            <th class="text-nowrap">No</th>
                            <th class="text-nowrap">Anggota</th>
                            <th class="text-nowrap">Buku</th>
                            <th class="text-nowrap">Terlambat</th>
                            <th class="text-nowrap">Jumlah</th>
                            <th class="text-nowrap">Status Bayar</th>
                            <th class="text-nowrap">Pengembalian</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $dendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?php echo e($d->id); ?>" class="checkbox-hapus"></td>
                            <td class="text-muted"><?php echo e($i + 1); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo e($d->peminjaman->anggota->nama ?? '-'); ?></div>
                            </td>
                            <td>
                                <div style="max-width:150px" class="text-truncate" title="<?php echo e($d->peminjaman->buku->judul ?? '-'); ?>"><?php echo e($d->peminjaman->buku->judul ?? '-'); ?></div>
                            </td>
                            <td>
                                <?php
                                    $tglKembali = \Carbon\Carbon::parse($d->peminjaman->tanggal_kembali)->startOfDay();
                                    if ($d->peminjaman->tanggal_dikembalikan) {
                                        $tglDikembalikan = \Carbon\Carbon::parse($d->peminjaman->tanggal_dikembalikan)->startOfDay();
                                    } elseif ($d->peminjaman->status === 'dikembalikan') {
                                        $tglDikembalikan = $tglKembali->copy();
                                    } else {
                                        $tglDikembalikan = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
                                    }
                                    $hariTerlambat = $tglDikembalikan->gt($tglKembali) ? (int) $tglKembali->diffInDays($tglDikembalikan) : 0;
                                ?>
                                <span class="badge rounded-pill bg-warning text-dark px-3"><?php echo e($hariTerlambat); ?> hari</span>
                            </td>
<td>
    <span class="fw-bold text-danger d-none d-md-inline">Rp <?php echo e(number_format($d->jumlah_denda, 0, ',', '.')); ?></span>
    <div class="d-inline d-md-none">
        <div class="fw-bold text-danger" style="font-size:11px">Rp</div>
        <div class="fw-bold text-danger" style="font-size:13px"><?php echo e(number_format($d->jumlah_denda, 0, ',', '.')); ?></div>
    </div>
</td>
                            <td>
                                <?php if($d->status === 'belum_dibayar'): ?>
                                    <span class="badge rounded-pill bg-danger px-3">Belum Bayar</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-success px-3">Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($d->peminjaman->status === 'dikembalikan'): ?>
                                    <span class="badge rounded-pill bg-info px-3">Sudah Kembali</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary px-3">Belum Kembali</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($d->status === 'belum_dibayar' && $d->peminjaman->status === 'dikembalikan'): ?>
                                <form action="<?php echo e(route('admin.denda.lunasi', $d->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            style="padding:6px 14px;background:#d4edda;color:#1a6e35;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                        <i class="bi bi-check-lg"></i> <span class="d-none d-md-inline">Lunasi</span>
                                    </button>
                                </form>
                                <?php elseif($d->status === 'sudah_dibayar'): ?>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-check me-1"></i><?php echo e(\Carbon\Carbon::parse($d->tanggal_bayar)->format('d M Y')); ?>

                                </small>
                                <?php else: ?>
                                <small class="text-secondary">
                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                </small>
                                <?php endif; ?>
                                <?php if($d->peminjaman->status === 'dikembalikan'): ?>
                                <form action="<?php echo e(route('admin.denda.destroy', $d->id)); ?>" method="POST" class="d-inline ms-1">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                            style="padding:6px 10px;background:#f8d7da;color:#721c24;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer"
                                            onclick="return confirm('Yakin hapus denda ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-check-circle" style="font-size:50px;color:#27ae60"></i>
                                    <p class="mt-3 mb-1 fw-semibold">Tidak ada denda</p>
                                    <small class="text-muted">Semua peminjaman dikembalikan tepat waktu</small>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('style'); ?>
<style>
.card-stat {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.icon-stat {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.table-denda th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    color: #666;
    border: none;
    white-space: nowrap;
}

.table-denda td {
    border-color: #f0f0f0;
    vertical-align: middle;
}

.table-denda tbody tr:hover {
    background: #fafbfc;
}

@media (max-width: 768px) {
    .card-stat {
        padding: 15px;
    }
    .icon-stat {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    .table-denda th,
    .table-denda td {
        padding: 10px 8px;
        font-size: 13px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.querySelectorAll('form[action*="lunasi"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Yakin ingin melunasi denda ini?')) {
            e.preventDefault();
        }
    });
});

document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.checkbox-hapus').forEach(cb => cb.checked = this.checked);
    updateJumlah();
});

document.querySelectorAll('.checkbox-hapus').forEach(cb => {
    cb.addEventListener('change', updateJumlah);
});

function updateJumlah() {
    const checked = document.querySelectorAll('.checkbox-hapus:checked').length;
    document.getElementById('jumlahTerpilih').textContent = checked;
    document.getElementById('btnHapusBanyak').style.display = checked > 0 ? 'inline-block' : 'none';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\denda\index.blade.php ENDPATH**/ ?>