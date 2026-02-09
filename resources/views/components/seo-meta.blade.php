@props([
    'title' => config('app.name', 'PeShop'),
    'description' => '',
    'canonical' => '',
    'ogImage' => '',
    'ogType' => 'website',
    'noindex' => false,
    'jsonLd' => null,
])

<title>{{ $title }}</title>

@if($description)
    <meta name="description" content="{{ $description }}">
@endif

@if($noindex)
    <meta name="robots" content="noindex, follow">
@endif

@if($canonical)
    <link rel="canonical" href="{{ $canonical }}">
@endif

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonical ?: request()->url() }}">
@if($description)
    <meta property="og:description" content="{{ $description }}">
@endif
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif
<meta property="og:site_name" content="{{ config('app.name', 'PeShop') }}">

{{-- JSON-LD structured data --}}
@if($jsonLd)
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
