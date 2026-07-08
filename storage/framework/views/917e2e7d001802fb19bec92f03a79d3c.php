<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'icon' => 'bi-list']));

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

foreach (array_filter((['title', 'icon' => 'bi-list']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="card-header-wrap mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="<?php echo e($icon); ?> fs-4 text-success"></i>
        <h5 class="fw-bold m-0 admin-dynamic-text" style="font-size:18px"><?php echo e($title); ?></h5>
    </div>
    <?php if(isset($action)): ?>
        <div class="card-header-actions">
            <?php echo e($action); ?>

        </div>
    <?php endif; ?>
</div>

<style>
    .card-header-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .card-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    @media (max-width: 768px) {
        .card-header-wrap {
            flex-direction: column;
            align-items: flex-start;
        }
        .card-header-actions {
            width: 100%;
        }
        .card-header-actions > * {
            flex: 1 1 auto;
            min-width: 0;
            text-align: center;
            justify-content: center;
            display: inline-flex;
            align-items: center;
        }
    }
</style>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\components\admin-card-header.blade.php ENDPATH**/ ?>