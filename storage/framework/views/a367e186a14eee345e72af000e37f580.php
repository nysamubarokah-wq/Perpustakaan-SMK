

<?php $__env->startSection('header_title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>

<style>
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px 25px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
    }
    .stat-card h3 { font-size: 28px; font-weight: 800; color: #222; margin: 0; }
    .stat-card p { color: #888; font-size: 13px; margin: 0; }
    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        margin-bottom: 25px;
    }
    .chart-card h5 { font-weight: 700; color: #222; margin-bottom: 20px; font-size: 16px; }
    .table-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    }
    .table-card-header {
        padding: 18px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .table-card-header h5 { font-weight: 700; color: #222; margin: 0; font-size: 16px; }
    .table-card td, .table-card th { padding: 12px 25px !important; font-size: 13px; }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .status-dipinjam { background: #fff3cd; color: #856404; }
    .status-dikembalikan { background: #d4edda; color: #1a6e35; }
    .status-terlambat { background: #f8d7da; color: #721c24; }
    .status-konfirmasi { background: #cff4fc; color: #055160; }
    .chart-container { position: relative; width: 100%; }

    @media (max-width: 768px) {
        .stat-card {
            padding: 14px 16px;
            gap: 12px;
            border-radius: 12px;
        }
        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            font-size: 18px;
        }
        .stat-card h3 { font-size: 20px; font-weight: 700; }
        .stat-card p { font-size: 11px; }
        .chart-card {
            padding: 16px;
            margin-bottom: 16px;
            border-radius: 12px;
        }
        .chart-card h5 { font-size: 14px; margin-bottom: 12px; }
        .table-card { border-radius: 12px; overflow-x: auto; }
        .table-card-header { padding: 14px 16px; }
        .table-card-header h5 { font-size: 14px; }
        .table-card td, .table-card th { padding: 10px 14px !important; font-size: 12px; }
        .dashboard-subheader { flex-direction: column; align-items: stretch !important; }
        .dashboard-subheader p { font-size: 13px !important; }
        .btn-export-pdf { width: 100%; text-align: center; justify-content: center; display: flex; }
        .table-card td {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    @media (max-width: 380px) {
        .stat-card { padding: 12px; gap: 10px; }
        .stat-icon { width: 38px; height: 38px; font-size: 16px; }
        .stat-card h3 { font-size: 18px; }
        .stat-card p { font-size: 10px; }
        .chart-card { padding: 12px; }
        .table-card td, .table-card th { padding: 8px 10px !important; font-size: 11px; }
    }
</style>

<!-- SUBHEADER -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 dashboard-subheader">
    <p style="color:#888;font-size:14px;margin:0">Selamat datang, <?php echo e(auth()->user()->name); ?>! 👋</p>
    <a href="<?php echo e(route('admin.laporan.pdf')); ?>"
       class="btn-export-pdf"
       style="padding:8px 18px;background:linear-gradient(135deg,#c0392b,#e74c3c);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
        <i class="bi bi-file-pdf"></i> Export PDF
    </a>
</div>

<!-- STAT CARDS -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#1a6e35,#27ae60)">
                <i class="bi bi-book"></i>
            </div>
            <div>
                <h3><?php echo e($totalBuku); ?></h3>
                <p>Total Buku</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#2c3e50,#3498db)">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <h3><?php echo e($totalAnggota); ?></h3>
                <p>Total Anggota</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#8e44ad,#e056fd)">
                <i class="bi bi-journal-check"></i>
            </div>
            <div>
                <h3><?php echo e($totalPeminjaman); ?></h3>
                <p>Total Peminjaman</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#d35400,#e67e22)">
                <i class="bi bi-bookmark-check"></i>
            </div>
            <div>
                <h3><?php echo e($sedangDipinjam); ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#c0392b,#e74c3c)">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <h3><?php echo e($terlambat); ?></h3>
                <p>Terlambat</p>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK -->
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-md-8">
        <div class="chart-card">
            <h5><i class="bi bi-bar-chart" style="color:#1a6e35"></i> Peminjaman Per Bulan</h5>
            <div class="chart-container" style="max-height:260px">
                <canvas id="chartPeminjaman"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <h5><i class="bi bi-pie-chart" style="color:#1a6e35"></i> Status Peminjaman</h5>
            <div class="chart-container" style="max-height:220px">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TABEL BAWAH -->
<div class="row g-2 g-md-3">
    <div class="col-md-5">
        <div class="table-card">
            <div class="table-card-header">
                <h5><i class="bi bi-trophy" style="color:#1a6e35"></i> Buku Terpopuler</h5>
            </div>
            <div style="overflow-x:auto">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bukuPopuler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $buku): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e(Str::limit($buku->judul, 25)); ?></td>
                            <td><span class="badge" style="background:#1a6e35"><?php echo e($buku->peminjaman_count); ?>x</span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="table-card">
            <div class="table-card-header">
                <h5><i class="bi bi-clock-history" style="color:#1a6e35"></i> Peminjaman Terbaru</h5>
                <a href="<?php echo e(route('peminjaman.index')); ?>" style="font-size:13px;color:#1a6e35;text-decoration:none">Lihat semua →</a>
            </div>
            <div style="overflow-x:auto">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa">
    <tr>
        <th>Anggota</th>
        <th>Buku</th>
        <th>Kembali</th>
        <th style="min-width: 110px;">Status</th> </tr>
</thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $peminjamanTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->anggota->nama ?? '-'); ?></td>
                            <td><?php echo e(Str::limit($item->buku->judul ?? '-', 20)); ?></td>
                            <td><?php echo e($item->tanggal_kembali); ?></td>
                            <td>
                                <?php if($item->status == 'menunggu_konfirmasi'): ?>
                                    <span class="status-badge status-konfirmasi">Menunggu</span>
                                <?php elseif($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString()): ?>
                                    <span class="status-badge status-terlambat">Terlambat</span>
                                <?php elseif($item->status == 'dipinjam'): ?>
                                    <span class="status-badge status-dipinjam">Dipinjam</span>
                                <?php else: ?>
                                    <span class="status-badge status-dikembalikan">Kembali</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const dataPeminjaman = new Array(12).fill(0);

<?php $__currentLoopData = $peminjamanPerBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    dataPeminjaman[<?php echo e($item->bulan - 1); ?>] = <?php echo e($item->total); ?>;
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

new Chart(document.getElementById('chartPeminjaman'), {
    type: 'bar',
    data: {
        labels: bulanLabels,
        datasets: [{
            label: 'Peminjaman',
            data: dataPeminjaman,
            backgroundColor: 'rgba(26,110,53,0.8)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
            x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
        }
    }
});

new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Dipinjam', 'Dikembalikan'],
        datasets: [{
            data: [<?php echo e($sedangDipinjam); ?>, <?php echo e($totalPeminjaman - $sedangDipinjam); ?>],
            backgroundColor: ['#e67e22', '#1a6e35'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 11 }, padding: 12 }
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>