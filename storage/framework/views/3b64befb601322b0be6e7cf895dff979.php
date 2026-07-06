<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Eksemplar<?php echo e($buku ? ' - '.$buku->judul : ''); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #222; }
        .header p { font-size: 12px; color: #888; }
        .grid { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }
        .card { background: white; border: 2px solid #1a6e35; border-radius: 12px; padding: 15px; text-align: center; width: 200px; break-inside: avoid; }
        .card h3 { font-size: 11px; color: #888; margin-bottom: 2px; }
        .card .kode { font-size: 14px; color: #1a6e35; font-weight: 700; font-family: monospace; margin-bottom: 5px; }
        .card .isbn { font-size: 9px; color: #aaa; margin-bottom: 8px; }
        .qr-wrap svg { width: 140px; height: 140px; }
        @media print {
            body { background: white; padding: 10px; }
            .card { border: 1.5px solid #000; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;margin-bottom:20px">
        <button onclick="window.print()" style="padding:10px 24px;background:#1a6e35;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer">
            Cetak Semua
        </button>
        <a href="<?php echo e($buku ? route('buku.show', $buku->id) : route('buku.index')); ?>" style="display:inline-block;margin-left:10px;padding:10px 24px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none">
            Kembali
        </a>
    </div>
    <div class="header">
        <h1><?php echo e($buku ? 'QR Code Eksemplar - '.$buku->judul : 'Semua QR Code Eksemplar'); ?></h1>
        <p>Total: <?php echo e(count($items)); ?> eksemplar &mdash; Dicetak: <?php echo e(date('d M Y H:i')); ?></p>
    </div>
    <div class="grid">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card">
            <h3><?php echo e($item['eksemplar']->buku->judul ?? ($buku->judul ?? '-')); ?></h3>
            <div class="kode"><?php echo e($item['eksemplar']->kode_buku); ?></div>
            <div class="qr-wrap"><?php echo $item['svg']; ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/buku/eksemplar-qr-print-all.blade.php ENDPATH**/ ?>