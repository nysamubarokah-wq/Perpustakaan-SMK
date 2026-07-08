<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'icon' => 'bi-arrow-left-circle', 'backUrl', 'backText' => 'Kembali']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'icon' => 'bi-arrow-left-circle', 'backUrl', 'backText' => 'Kembali']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2" style="margin-bottom:20px">
    <h5 class="fw-bold m-0 admin-dynamic-text">
        <i class="<?php echo e($icon); ?>" style="color:#1a6e35"></i> <?php echo e($title); ?>

    </h5>
    <a href="<?php echo e($backUrl); ?>"
       class="admin-back-btn"
       style="padding:8px 16px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
        <i class="bi bi-arrow-left"></i> <?php echo e($backText); ?>

    </a>
</div>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\components\admin-page-header.blade.php ENDPATH**/ ?>