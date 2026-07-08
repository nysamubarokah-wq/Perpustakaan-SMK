<?php $__env->startSection('title', 'Edit Buku'); ?>
<?php $__env->startSection('page-title', 'Edit Buku'); ?>

<?php
$backParams = request()->only(['search', 'sort', 'direction', 'per_page']);
?>

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
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal8e6ccc94deb46dfd1314097afabe5570 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e6ccc94deb46dfd1314097afabe5570 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-page-header','data' => ['title' => 'Edit Buku','icon' => 'bi bi-pencil-square','backUrl' => route('buku.index', $backParams)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Buku','icon' => 'bi bi-pencil-square','backUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('buku.index', $backParams))]); ?>
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
    <div class="card-admin-body" style="position:relative">
        <form action="<?php echo e(route('buku.update', $buku->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
            <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
            <input type="hidden" name="direction" value="<?php echo e(request('direction')); ?>">
            <input type="hidden" name="per_page" value="<?php echo e(request('per_page')); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('judul', $buku->judul)); ?>">
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
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" class="form-control <?php $__errorArgs = ['pengarang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('pengarang', $buku->pengarang)); ?>">
                    <?php $__errorArgs = ['pengarang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <div class="input-group">
                        <select name="penerbit_id" class="form-control <?php $__errorArgs = ['penerbit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih --</option>
                            <?php $__currentLoopData = $penerbitList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php echo e(old('penerbit_id', $buku->penerbit_id) == $p->id ? 'selected' : ''); ?>><?php echo e($p->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('penerbit')">+ Baru</button>
                    </div>
                    <input type="text" name="penerbit_baru" id="penerbitBaru" class="form-control mt-2" placeholder="Ketik nama penerbit baru..." style="display:none">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control <?php $__errorArgs = ['tahun_terbit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tahun_terbit', $buku->tahun_terbit)); ?>">
                    <?php $__errorArgs = ['tahun_terbit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Eksemplar</label>
                    <div style="padding:8px 12px;background:#f8f9fa;border-radius:8px;font-size:14px">
                        <strong><?php echo e($buku->eksemplar()->count()); ?></strong> total &middot;
                        <span style="color:#1a6e35"><?php echo e($buku->eksemplarTersedia()->count()); ?> tersedia</span>
                    </div>
                    <a href="<?php echo e(route('buku.show', $buku->id)); ?>" style="font-size:11px;color:#2563eb">Kelola Eksemplar &rarr;</a>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" name="kode_buku" class="form-control <?php $__errorArgs = ['kode_buku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('kode_buku', $buku->kode_buku)); ?>">
                    <?php $__errorArgs = ['kode_buku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control <?php $__errorArgs = ['isbn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('isbn', $buku->isbn)); ?>">
                    <?php $__errorArgs = ['isbn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <div class="input-group">
                        <select name="genre_id" class="form-control <?php $__errorArgs = ['genre_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih --</option>
                            <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e(old('genre_id', $buku->genre_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('genre')">+ Baru</button>
                    </div>
                    <input type="text" name="genre_baru" id="genreBaru" class="form-control mt-2" placeholder="Ketik genre baru..." style="display:none">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi Buku</label>
                    <textarea name="deskripsi" class="form-control <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4"><?php echo e(old('deskripsi', $buku->deskripsi)); ?></textarea>
                    <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sampul Buku</label>
                    <?php if($buku->sampul): ?>
                        <div class="mb-2">
                            <img src="<?php echo e(asset($buku->sampul)); ?>" style="height:100px;border-radius:8px;object-fit:cover">
                            <p style="font-size:12px;color:#888;margin-top:5px">Sampul saat ini</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="sampul" class="form-control <?php $__errorArgs = ['sampul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah sampul</small>
                    <?php $__errorArgs = ['sampul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Background Rekomendasi <small class="text-muted">(opsional)</small></label>
                    <?php if($buku->rekom_bg): ?>
                        <div class="mb-2">
                            <img src="<?php echo e(asset($buku->rekom_bg)); ?>" style="height:80px;border-radius:8px;object-fit:cover">
                            <p style="font-size:12px;color:#888;margin-top:5px">Background saat ini</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="rekom_bg" class="form-control <?php $__errorArgs = ['rekom_bg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah background rekomendasi</small>
                    <?php $__errorArgs = ['rekom_bg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Rak</label>
                    <select name="lokasi" class="form-control <?php $__errorArgs = ['lokasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php $__currentLoopData = ['A1','A2','A3','B1','B2','B3','C1','C2','C3','D1','D2','D3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lok); ?>" <?php echo e(old('lokasi', $buku->lokasi) == $lok ? 'selected' : ''); ?>>Rak <?php echo e($lok); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['lokasi'];
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
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\buku\edit.blade.php ENDPATH**/ ?>