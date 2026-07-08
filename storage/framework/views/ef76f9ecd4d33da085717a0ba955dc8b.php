

<?php $__env->startSection('header_title', 'Kelola VIP'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div style="background:white;border-radius:16px;padding:25px;box-shadow:0 3px 15px rgba(0,0,0,0.08);margin-bottom:25px">
    <div style="font-weight:700;color:#222;margin-bottom:15px">⭐ Upgrade VIP Manual (by Admin)</div>
    <form action="#" method="POST" id="formUpgradeAdmin" class="d-flex gap-2 flex-wrap">
        <?php echo csrf_field(); ?>
        <select name="user_id" id="selectUser" style="flex:1;min-width:200px;padding:10px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none;background:white">
            <option value="">-- Pilih Siswa --</option>
            <?php $__currentLoopData = \App\Models\User::where('role','!=','admin')->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> (🪙 <?php echo e($u->coin ?? 0); ?> koin) <?php echo e($u->is_vip && $u->vip_expired_at && now()->lt($u->vip_expired_at) ? '⭐ VIP' : ''); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" id="btnUpgrade"
                style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            ⭐ Upgrade 7 Hari
        </button>
    </form>
</div>


<div style="background:white;border-radius:16px;box-shadow:0 3px 15px rgba(0,0,0,0.08);overflow:hidden">
    <div style="padding:20px 25px;border-bottom:1px solid #f0f0f0;font-weight:700;color:#222">
        Daftar Member VIP Aktif
    </div>

    <?php if($vipUsers->count() > 0): ?>
    <div class="card-admin-body">
        <table class="table table-hover mb-0" style="border-collapse:collapse">
            <thead style="background:#f8f9fa;border-bottom:2px solid #e5e7eb">
                <tr>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">NAMA</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">EXPIRED</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">STATUS</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $vipUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="padding:12px 20px;vertical-align:middle">
                        <div style="font-weight:700;color:#222;font-size:14px"><?php echo e($u->name); ?></div>
                        <div style="font-size:12px;color:#888"><?php echo e($u->email); ?></div>
                    </td>
                    <td style="padding:12px 20px;vertical-align:middle;font-size:14px">
                        <?php echo e($u->vip_expired_at ? $u->vip_expired_at->format('d M Y') : '-'); ?>

                    </td>
                    <td style="padding:12px 20px;vertical-align:middle">
    <?php if($u->vip_expired_at && now()->lt($u->vip_expired_at)): ?>
        
        <span style="background:#d1fae5;color:#065f46;font-size:11px;padding:3px 10px;border-radius:8px;font-weight:600;white-space:nowrap;display:inline-block">
            ✓ Aktif (<?php echo e(ceil(now()->diffInDays($u->vip_expired_at, false))); ?> hari lagi)
        </span>
    <?php else: ?>
        
        <span style="background:#fee2e2;color:#991b1b;font-size:11px;padding:3px 10px;border-radius:8px;font-weight:600;white-space:nowrap;display:inline-block">
            Expired
        </span>
    <?php endif; ?>
</td>
                    <td style="padding:12px 20px;vertical-align:middle">
                        <div style="display:flex;gap:8px">
                            <form action="<?php echo e(route('admin.vip.upgrade', $u->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                        style="padding:6px 14px;background:#d1fae5;color:#065f46;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                    +7 Hari
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.vip.cabut', $u->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                        onclick="return confirm('Cabut VIP <?php echo e($u->name); ?>?')"
                                        style="padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                    Cabut
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div style="padding:15px 20px"><?php echo e($vipUsers->links()); ?></div>

    <?php else: ?>
    <div style="text-align:center;padding:50px;color:#aaa">
        <i class="bi bi-star" style="font-size:40px;display:block;margin-bottom:10px"></i>
        <p>Belum ada member VIP</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('formUpgradeAdmin').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('selectUser').value;
    if (!userId) { alert('Pilih siswa dulu!'); return; }
    if (!confirm('Upgrade VIP 7 hari untuk siswa ini?')) return;
    this.action = `/admin/vip/${userId}/upgrade`;
    this.submit();
});
</script>
<style>
@media (max-width: 768px) {
    #formUpgradeAdmin { flex-direction: column; }
    #formUpgradeAdmin select { min-width: 100%; }
    #formUpgradeAdmin button { width: 100%; }

    /* Tabel VIP di mobile — sembunyikan kolom email, tampilkan inline */
    .table td[style*="padding:12px 20px"] { padding: 8px 12px !important; font-size: 12px !important; }
    .table th[style*="padding:15px 20px"] { padding: 10px 12px !important; font-size: 11px !important; }

    /* Nama + email jadi satu kolom */
    .vip-email { display: none; }
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\vip\index.blade.php ENDPATH**/ ?>