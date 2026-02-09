<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'fetchpriority' => null,
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
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'fetchpriority' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $info = pathinfo($src);
    $base = $info['dirname'] . '/' . $info['filename'];
?>

<picture>
    <source srcset="<?php echo e(asset($base . '.avif')); ?>" type="image/avif">
    <source srcset="<?php echo e(asset($base . '.webp')); ?>" type="image/webp">
    <img
        src="<?php echo e(asset($src)); ?>"
        alt="<?php echo e($alt); ?>"
        <?php if($width): ?> width="<?php echo e($width); ?>" <?php endif; ?>
        <?php if($height): ?> height="<?php echo e($height); ?>" <?php endif; ?>
        <?php if($fetchpriority): ?> fetchpriority="<?php echo e($fetchpriority); ?>" <?php endif; ?>
        loading="<?php echo e($loading); ?>"
        decoding="async"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([$class]); ?>"
    >
</picture>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\components\picture.blade.php ENDPATH**/ ?>