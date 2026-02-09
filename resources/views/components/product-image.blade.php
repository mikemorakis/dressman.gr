@props([
    'image',
    'size' => 'medium',
    'class' => '',
    'eager' => false,
])

@php
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
@endphp

<img
    src="{{ $src }}"
    alt="{{ $image->alt_text }}"
    width="{{ $w }}"
    height="{{ $h }}"
    @if(!$eager) loading="lazy" @endif
    decoding="async"
    @class([$class])
>
