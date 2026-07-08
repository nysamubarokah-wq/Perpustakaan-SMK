

<?php $__env->startSection('title', 'Kelola User'); ?>
<?php $__env->startSection('page-title', 'Kelola User'); ?>

<?php $__env->startSection('content'); ?>
<style>
    @media (max-width: 768px) {
        .admin-tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .admin-tbl-wrap table { min-width: 600px; }
        .admin-tbl-wrap td, .admin-tbl-wrap th { padding: 8px 10px !important; font-size: 12px; }
        .admin-tbl-wrap .btn-sm { padding: 4px 6px !important; font-size: 10px !important; }
    }
</style>
<div class="card-admin">
    <div class="card-admin-header">
        <h5><i class="bi bi-people" style="color:#1a6e35"></i> Daftar User</h5>
    </div>
    <div class="card-admin-body">
        <div class="admin-tbl-wrap">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIS</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($user->name)); ?>&background=1a6e35&color=fff&size=50"
                                 style="width:35px;height:35px;border-radius:50%">
                            <?php echo e($user->name); ?>

                            <?php if($user->id === auth()->id()): ?>
                                <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:2px 8px;border-radius:10px">Anda</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo e($user->email); ?></td>
                    <td><?php echo e($user->nis ?? '-'); ?></td>
                    <td>
                        <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                            background:<?php echo e($user->role === 'admin' ? '#d4edda' : '#e8f4fd'); ?>;
                            color:<?php echo e($user->role === 'admin' ? '#1a6e35' : '#2c3e50'); ?>">
                            <?php echo e(ucfirst($user->role)); ?>

                        </span>
                    </td>
                    <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                    <td>
                        <?php if($user->id !== auth()->id()): ?>
                            <form action="<?php echo e(route('admin.users.role', $user->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <button type="submit" 
                                    onclick="return confirm('Ubah role <?php echo e($user->name); ?> menjadi <?php echo e($user->role === 'admin' ? 'siswa' : 'admin'); ?>?')"
                                    style="padding:6px 14px;background:<?php echo e($user->role === 'admin' ? '#fff3cd' : '#d4edda'); ?>;color:<?php echo e($user->role === 'admin' ? '#856404' : '#1a6e35'); ?>;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                    <?php if($user->role === 'admin'): ?>
                                        <i class="bi bi-arrow-down-circle"></i> Jadikan Siswa
                                    <?php else: ?>
                                        <i class="bi bi-arrow-up-circle"></i> Jadikan Admin
                                    <?php endif; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <span style="color:#aaa;font-size:12px">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada user</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\users.blade.php ENDPATH**/ ?>