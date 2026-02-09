@props([
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'fetchpriority' => null,
])

@php
    $info = pathinfo($src);
    $base = $info['dirname'] . '/' . $info['filename'];
@endphp

<picture>
    <source srcset="{{ asset($base . '.avif') }}" type="image/avif">
    <source srcset="{{ asset($base . '.webp') }}" type="image/webp">
    <img
        src="{{ asset($src) }}"
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
        loading="{{ $loading }}"
        decoding="async"
        @class([$class])
    >
</picture>
