<?php $__env->startSection('title', 'Tambah E-book'); ?>
<?php $__env->startSection('page-title', 'Tambah E-book'); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleNew(field) {
    const input = document.getElementById(field + 'Baru');
    if (input.style.display === 'none') {
        input.style.display = 'block';
        input.focus();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function previewCover(input) {
    const preview = document.getElementById('coverPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleKoin(val) {
    document.getElementById('koinField').style.display = val == 1 ? 'none' : 'block';
}

toggleKoin('<?php echo e(old('is_vip', 0)); ?>');
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal8e6ccc94deb46dfd1314097afabe5570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e6ccc94deb46dfd1314097afabe5570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-page-header','data' => ['title' => 'Tambah E-book','icon' => 'bi bi-plus-circle','backUrl' => route('admin.ebook.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tambah E-book','icon' => 'bi bi-plus-circle','backUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.ebook.index'))]); ?>
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
        <form action="<?php echo e(route('admin.ebook.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul E-book</label>
                    <input type="text" name="judul" class="form-control <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('judul')); ?>">
                    <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control <?php $__errorArgs = ['penulis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('penulis')); ?>">
                    <?php $__errorArgs = ['penulis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Akses</label>
                    <select name="is_vip" id="tipeAkses" class="form-control" onchange="toggleKoin(this.value)">
                        <option value="0" <?php echo e(old('is_vip') == 0 ? 'selected' : ''); ?>>Gratis / Bayar Koin</option>
                        <option value="1" <?php echo e(old('is_vip') == 1 ? 'selected' : ''); ?>>VIP (akses unlimited)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="koinField">
                    <label class="form-label">Harga Koin <small class="text-muted">(isi 0 jika gratis)</small></label>
                    <input type="number" name="harga_koin" class="form-control <?php $__errorArgs = ['harga_koin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('harga_koin', 0)); ?>" min="0">
                    <?php $__errorArgs = ['harga_koin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sinopsis</label>
                    <textarea name="sinopsis" class="form-control <?php $__errorArgs = ['sinopsis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4"><?php echo e(old('sinopsis')); ?></textarea>
                    <?php $__errorArgs = ['sinopsis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File PDF <small class="text-muted">(maks. 20MB)</small></label>
                    <input type="file" name="file_pdf" class="form-control <?php $__errorArgs = ['file_pdf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf">
                    <?php $__errorArgs = ['file_pdf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cover <small class="text-muted">(jpg/png, maks. 2MB)</small></label>
                    <input type="file" name="cover" class="form-control <?php $__errorArgs = ['cover'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*" id="coverInput" onchange="previewCover(this)">
                    <img id="coverPreview" src="" alt="Preview" style="display:none;margin-top:10px;height:100px;border-radius:8px;object-fit:cover">
                    <?php $__errorArgs = ['cover'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-upload"></i> Upload E-book
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\admin\ebook\create.blade.php ENDPATH**/ ?>