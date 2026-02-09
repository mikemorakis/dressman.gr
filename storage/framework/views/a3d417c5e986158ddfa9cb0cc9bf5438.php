<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'image',
    'size' => 'medium',
    'class' => '',
    'eager' => false,
]));

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

foreach (array_filter(([
    'image',
    'size' => 'medium',
    'class' => '',
    'eager' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    /** @var \App\Models\ProductImage $image */
    $src = asset('storage/' . ($size === 'large' ? $image->path_large : ($size === 'thumb' ? $image->path_thumb : $image->path_medium)));
    $srcLarge = asset('storage/' . $image->path_large);
    $srcMedium = asset('storage/' . $image->path_medium);

    // Compute approximate dimensions for the requested size
    $ratio = $image->width > 0 ? $image->height / $image->width : 1;
    $sizeMap = ['large' => 1200, 'medium' => 600, 'thumb' => 150];
    $targetW = $sizeMap[$size] ?? 600;
    $w = min($image->width ?: $targetW, $targetW);
    $h = (int) round($w * $ratio);
?>

<img
    src="<?php echo e($src); ?>"
    alt="<?php echo e($image->alt_text); ?>"
    width="<?php echo e($w); ?>"
    height="<?php echo e($h); ?>"
    <?php if(!$eager): ?> loading="lazy" <?php endif; ?>
    decoding="async"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([$class]); ?>"
>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\components\product-image.blade.php ENDPATH**/ ?>