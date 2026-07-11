<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

        .header {
            text-align: center;
            padding: 20px 0 15px;
            border-bottom: 3px solid #1a6e35;
            margin-bottom: 20px;
        }

        .header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .header h2 {
            font-size: 16px;
            color: #1a6e35;
            margin: 8px 0 3px;
        }

        .header p { font-size: 11px; color: #666; }

        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-box {
            flex: 1;
            background: #f0faf4;
            border: 1px solid #1a6e35;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .stat-box h3 { font-size: 20px; color: #1a6e35; margin-bottom: 3px; }
        .stat-box p { font-size: 10px; color: #666; }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1a6e35;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr {
            background: #1a6e35;
            color: white;
        }

        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }

        tbody tr:nth-child(even) { background: #f9f9f9; }
        tbody tr:hover { background: #f0faf4; }

        tbody td {
            padding: 7px 10px;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-dipinjam { background: #fff3cd; color: #856404; }
        .badge-dikembalikan { background: #d4edda; color: #1a6e35; }
        .badge-terlambat { background: #f8d7da; color: #721c24; }

        .footer {
            text-align: right;
            font-size: 10px;
            color: #888;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h2>PERPUSTAKAAN SMK MAARIF WALISONGO KAJORAN</h2>
    <p>Laporan Data Peminjaman Buku</p>
    <p>Dicetak pada: <?php echo e(now()->format('d F Y, H:i')); ?> WIB</p>
</div>

<!-- STATISTIK -->
<table style="margin-bottom:20px">
    <tr>
        <td style="width:20%;background:#f0faf4;border:1px solid #1a6e35;border-radius:5px;padding:10px;text-align:center">
            <div style="font-size:20px;font-weight:bold;color:#1a6e35"><?php echo e($totalBuku); ?></div>
            <div style="font-size:10px;color:#666">Total Buku</div>
        </td>
        <td style="width:5%"></td>
        <td style="width:20%;background:#f0faf4;border:1px solid #1a6e35;border-radius:5px;padding:10px;text-align:center">
            <div style="font-size:20px;font-weight:bold;color:#1a6e35"><?php echo e($totalAnggota); ?></div>
            <div style="font-size:10px;color:#666">Total Anggota</div>
        </td>
        <td style="width:5%"></td>
        <td style="width:20%;background:#f0faf4;border:1px solid #1a6e35;border-radius:5px;padding:10px;text-align:center">
            <div style="font-size:20px;font-weight:bold;color:#1a6e35"><?php echo e($totalPeminjaman); ?></div>
            <div style="font-size:10px;color:#666">Total Peminjaman</div>
        </td>
        <td style="width:5%"></td>
        <td style="width:20%;background:#fff3cd;border:1px solid #856404;border-radius:5px;padding:10px;text-align:center">
            <div style="font-size:20px;font-weight:bold;color:#856404"><?php echo e($sedangDipinjam); ?></div>
            <div style="font-size:10px;color:#666">Sedang Dipinjam</div>
        </td>
        <td style="width:5%"></td>
        <td style="width:20%;background:#f8d7da;border:1px solid #721c24;border-radius:5px;padding:10px;text-align:center">
            <div style="font-size:20px;font-weight:bold;color:#721c24"><?php echo e($terlambat); ?></div>
            <div style="font-size:10px;color:#666">Terlambat</div>
        </td>
    </tr>
</table>

<!-- TABEL PEMINJAMAN -->
<div class="section-title">Data Peminjaman</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Anggota</th>
            <th>Judul Buku</th>
            <th>Pengarang</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $peminjaman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($index + 1); ?></td>
            <td><?php echo e($item->anggota->nama ?? '-'); ?></td>
            <td><?php echo e($item->buku->judul ?? '-'); ?></td>
            <td><?php echo e($item->buku->pengarang ?? '-'); ?></td>
            <td><?php echo e($item->tanggal_pinjam); ?></td>
            <td><?php echo e($item->tanggal_kembali); ?></td>
            <td>
                <?php if($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString()): ?>
                    <span class="badge badge-terlambat">Terlambat</span>
                <?php elseif($item->status == 'dipinjam'): ?>
                    <span class="badge badge-dipinjam">Dipinjam</span>
                <?php elseif($item->status == 'menunggu_konfirmasi'): ?>
                    <span class="badge badge-dipinjam">Menunggu</span>
                <?php else: ?>
                    <span class="badge badge-dikembalikan">Dikembalikan</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="7" style="text-align:center;color:#aaa;padding:20px">Belum ada data peminjaman</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- FOOTER -->
<div class="footer">
    Perpustakaan SMK Maarif Walisongo Kajoran &nbsp;|&nbsp; <?php echo e(now()->format('d F Y')); ?>

</div>

</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/admin/laporan-pdf.blade.php ENDPATH**/ ?>