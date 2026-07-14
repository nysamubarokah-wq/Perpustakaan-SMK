<?php $__env->startSection('header_title', 'Kelola Background'); ?>

<?php $__env->startSection('content'); ?>
<div class="card-admin">
    <?php if (isset($component)) { $__componentOriginal30f75447732d1254415eecac77636d07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30f75447732d1254415eecac77636d07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-card-header','data' => ['title' => 'Daftar Background','icon' => 'bi bi-palette']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-card-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daftar Background','icon' => 'bi bi-palette']); ?>
         <?php $__env->slot('action', null, []); ?> 
            <a href="<?php echo e(route('background.create')); ?>" style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-plus-circle"></i> Tambah Background
            </a>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30f75447732d1254415eecac77636d07)): ?>
<?php $attributes = $__attributesOriginal30f75447732d1254415eecac77636d07; ?>
<?php unset($__attributesOriginal30f75447732d1254415eecac77636d07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30f75447732d1254415eecac77636d07)): ?>
<?php $component = $__componentOriginal30f75447732d1254415eecac77636d07; ?>
<?php unset($__componentOriginal30f75447732d1254415eecac77636d07); ?>
<?php endif; ?>
    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $backgrounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td>
                        <div style="width:70px;height:45px;border-radius:6px;overflow:hidden;position:relative;<?php echo e($item->type === 'color' ? $item->value : 'background:#000'); ?>">
                            <?php if($item->type === 'image'): ?>
                                <img src="<?php echo e(asset($item->value)); ?>" style="width:100%;height:100%;object-fit:cover">
                            <?php elseif($item->type === 'video'): ?>
                                <video muted style="width:100%;height:100%;object-fit:cover">
                                    <source src="<?php echo e(asset($item->value)); ?>">
                                </video>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo e($item->nama); ?></td>
                    <td>
                        <span style="padding:3px 10px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:11px;font-weight:600;text-transform:capitalize"><?php echo e($item->type); ?></span>
                    </td>
                    <td>🪙 <?php echo e($item->harga); ?></td>
                    <td>
                        <a href="<?php echo e(route('background.edit', $item->id)); ?>"
                            style="padding:5px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if($item->slug !== 'default'): ?>
                        <form action="<?php echo e(route('background.destroy', $item->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" onclick="return confirm('Yakin hapus background ini?')"
                                style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data background</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/background/index.blade.php ENDPATH**/ ?>