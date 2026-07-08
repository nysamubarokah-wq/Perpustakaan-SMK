

<?php $__env->startSection('header_title', 'Edit Background'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal8e6ccc94deb46dfd1314097afabe5570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e6ccc94deb46dfd1314097afabe5570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-page-header','data' => ['title' => 'Edit Background','icon' => 'bi bi-pencil','backUrl' => route('background.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Background','icon' => 'bi bi-pencil','backUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('background.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e6ccc94deb46dfd1314097afabe5570)): ?>
<?php $attributes = $__attributesOriginal8e6ccc94deb46dfd1314097afabe5570; ?>
<?php unset($__attributesOriginal8e6ccc94deb46dfd1314097afabe5570); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e6ccc94deb46dfd1314097afabe5570)): ?>
<?php $component = $__componentOriginal8e6ccc94deb46dfd1314097afabe5570; ?>
<?php unset($__componentOriginal8e6ccc94deb46dfd1314097afabe5570); ?>
<?php endif; ?>

<div class="card-admin">
    <div class="card-admin-body">
        <form action="<?php echo e(route('background.update', $background->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Background</label>
                    <input type="text" name="nama" class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nama', $background->nama)); ?>">
                    <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga (coin)</label>
                    <input type="number" name="harga" class="form-control <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('harga', $background->harga)); ?>">
                    <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" id="type" class="form-control <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" onchange="toggleTipe()">
                        <option value="color" <?php echo e(old('type', $background->type) == 'color' ? 'selected' : ''); ?>>Warna</option>
                        <option value="image" <?php echo e(old('type', $background->type) == 'image' ? 'selected' : ''); ?>>Foto</option>
                        <option value="video" <?php echo e(old('type', $background->type) == 'video' ? 'selected' : ''); ?>>Video</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-12" id="fieldWarna">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Warna 1</label>
                            <input type="color" name="warna1" class="form-control" value="<?php echo e(old('warna1', '#1a6e35')); ?>" style="height:45px">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Warna 2</label>
                            <input type="color" name="warna2" class="form-control" value="<?php echo e(old('warna2', '#27ae60')); ?>" style="height:45px">
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3" id="fieldFile" style="display:none">
                    <label class="form-label">Upload File Baru (opsional, kosongkan kalau ga ganti)</label>
                    <input type="file" name="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <?php if($background->type !== 'color'): ?>
                        <div class="mt-2" style="width:120px;height:70px;border-radius:8px;overflow:hidden">
                            <?php if($background->type === 'image'): ?>
                                <img src="<?php echo e(asset($background->value)); ?>" style="width:100%;height:100%;object-fit:cover">
                            <?php else: ?>
                                <video muted controls style="width:100%;height:100%;object-fit:cover">
                                    <source src="<?php echo e(asset($background->value)); ?>">
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 30px;font-weight:600">
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>

<script>
function toggleTipe() {
    const type = document.getElementById('type').value;
    document.getElementById('fieldWarna').style.display = type === 'color' ? 'block' : 'none';
    document.getElementById('fieldFile').style.display = type === 'color' ? 'none' : 'block';
}
toggleTipe();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\background\edit.blade.php ENDPATH**/ ?>