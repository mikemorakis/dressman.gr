@props([
    'items' => [],
])

@if(count($items) > 0)
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-x-1.5 text-sm text-gray-500" role="list">
            {{-- Home is always first --}}
            <li>
                <a href="{{ url('/') }}" class="hover:text-primary-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                    <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="sr-only">Home</span>
                </a>
            </li>

            @foreach($items as $item)
                <li class="flex items-center gap-x-1.5">
                    {{-- Separator --}}
                    <svg class="h-4 w-4 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>

                    @if(!empty($item['url']) && !$loop->last)
                        <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-gray-900 font-medium" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- BreadcrumbList JSON-LD --}}
    @php
        $jsonLdItems = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')]];
        foreach ($items as $i => $item) {
            $entry = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $item['label']];
            if (!empty($item['url'])) {
                $entry['item'] = $item['url'];
            }
            $jsonLdItems[] = $entry;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $jsonLdItems,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
