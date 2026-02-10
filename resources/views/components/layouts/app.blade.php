<!DOCTYPE html>
<html lang="el" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{ $seo ?? '' }}

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-serif-display:400,500,600,700&family=roboto:400,500,700&display=swap" rel="stylesheet" />

    {{-- Styles & Scripts — Alpine loaded via app.js (deferred, type=module) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900 flex flex-col min-h-screen">
    {{-- Skip to content --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-black focus:text-white focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
        Skip to main content
    </a>

    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 flex flex-col h-[10vh] lg:h-[15vh]"
            x-data="{ mobileMenu: false, megaMenu: false }"
            x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileMenu)">

        {{-- Top bar: logo centered, search + cart --}}
        <div class="border-b border-gray-100 flex-1 min-h-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex items-center justify-between h-full">
                    {{-- Mobile menu button --}}
                    <button
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center p-2 -ml-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                        @click="mobileMenu = true"
                        :aria-expanded="mobileMenu.toString()"
                        aria-controls="mobile-menu"
                        aria-label="Open navigation menu"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Search + Phone (left on desktop) --}}
                    <div class="hidden lg:flex items-center gap-x-3">
                        <a href="{{ url('/search') }}"
                           class="p-2 text-gray-700 hover:text-black focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                           aria-label="Search products">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </a>
                        <a href="tel:2155004038" class="text-sm text-gray-600 hover:text-black transition-colors">2155004038</a>
                    </div>

                    {{-- Logo (centered) --}}
                    <a href="{{ url('/') }}" class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0 lg:flex-1 lg:text-center flex flex-col items-center" aria-label="Dressman home">
                        <x-picture src="images/dressman-logo-big.png" alt="Dressman" class="h-8 sm:h-10 lg:h-12 w-auto mx-auto" loading="eager" fetchpriority="high" />
                        <span class="text-[10px] sm:text-xs tracking-[0.2em] text-gray-500 font-light mt-1">— est 1974 —</span>
                    </a>

                    {{-- Header actions --}}
                    <div class="flex items-center gap-x-2 sm:gap-x-3">
                        {{-- Wishlist --}}
                        <x-wishlist-icon :count="app(App\Services\WishlistService::class)->count()" />
                        {{-- Cart --}}
                        <x-cart-icon :count="app(App\Services\CartService::class)->count()" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop navigation bar --}}
        <nav class="hidden lg:flex justify-center gap-x-8 relative" aria-label="Main navigation">
                    <a href="{{ url('/product-category/gabriatika-kostoumia') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Γαμπριάτικα Κοστούμια</a>
                    <a href="{{ url('/product-category/rent') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Ενοικίαση Σμόκιν</a>
                    <a href="{{ url('/metapoiisi') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Μεταποίηση</a>
                    <a href="{{ url('/made-to-measure') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Made to Measure</a>
                    <a href="{{ url('/product-category/kostoumia') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]" @mouseenter="megaMenu = true" :aria-expanded="megaMenu.toString()">Ανδρικά Ρούχα <svg class="inline h-3.5 w-3.5 -mt-px transition-transform" :class="megaMenu && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg></a>
                    <a href="{{ url('/blog') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Blog</a>
                    <a href="{{ url('/contact') }}" class="text-sm font-light text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Επικοινωνία</a>

            {{-- Mega-menu dropdown --}}
            <div
                x-show="megaMenu"
                @mouseenter="megaMenu = true"
                @mouseleave="megaMenu = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                x-cloak
                class="absolute left-1/2 -translate-x-1/2 top-full w-[700px] bg-white border border-gray-200 shadow-lg z-50 p-6"
            >
                <div class="grid grid-cols-4 gap-6">
                    {{-- Κοστούμια --}}
                    <div>
                        <a href="{{ url('/product-category/kostoumia') }}" class="text-sm font-bold text-black hover:underline">Κοστούμια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/business-kostoymia') }}" class="text-sm text-gray-600 hover:text-black">Business Κοστούμια</a></li>
                            <li><a href="{{ url('/product-category/kostoymia-megala-megethi') }}" class="text-sm text-gray-600 hover:text-black">Μεγάλα Μεγέθη</a></li>
                            <li><a href="{{ url('/product-category/gabriatika-kostoumia') }}" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Κοστούμια</a></li>
                            <li><a href="{{ url('/product-category/tuxedo') }}" class="text-sm text-gray-600 hover:text-black">Tuxedo</a></li>
                        </ul>
                    </div>

                    {{-- Πουκάμισα --}}
                    <div>
                        <a href="{{ url('/product-category/poykamisa') }}" class="text-sm font-bold text-black hover:underline">Πουκάμισα</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/gampriatika-poykamisa') }}" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Πουκάμισα</a></li>
                            <li><a href="{{ url('/product-category/business-poykamisa') }}" class="text-sm text-gray-600 hover:text-black">Business Πουκάμισα</a></li>
                        </ul>
                    </div>

                    {{-- Γιλέκα --}}
                    <div>
                        <a href="{{ url('/product-category/gileka') }}" class="text-sm font-bold text-black hover:underline">Γιλέκα</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/business-gileka') }}" class="text-sm text-gray-600 hover:text-black">Business Γιλέκα</a></li>
                            <li><a href="{{ url('/product-category/gabriatika-gileka') }}" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Γιλέκα</a></li>
                        </ul>
                    </div>

                    {{-- Σακάκια --}}
                    <div>
                        <a href="{{ url('/product-category/sakakia') }}" class="text-sm font-bold text-black hover:underline">Σακάκια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/business-sakakia') }}" class="text-sm text-gray-600 hover:text-black">Business Σακάκια</a></li>
                            <li><a href="{{ url('/product-category/palto') }}" class="text-sm text-gray-600 hover:text-black">Παλτό</a></li>
                        </ul>
                    </div>

                    {{-- Μπλούζες --}}
                    <div>
                        <a href="{{ url('/product-category/mplouzes') }}" class="text-sm font-bold text-black hover:underline">Μπλούζες</a>
                    </div>

                    {{-- Παντελόνια --}}
                    <div>
                        <a href="{{ url('/product-category/pantelonia') }}" class="text-sm font-bold text-black hover:underline">Παντελόνια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/business-panteloni') }}" class="text-sm text-gray-600 hover:text-black">Business Παντελόνια</a></li>
                            <li><a href="{{ url('/product-category/tsinos') }}" class="text-sm text-gray-600 hover:text-black">Τσίνος</a></li>
                            <li><a href="{{ url('/product-category/tzin') }}" class="text-sm text-gray-600 hover:text-black">Τζιν</a></li>
                        </ul>
                    </div>

                    {{-- Παπούτσια --}}
                    <div>
                        <a href="{{ url('/product-category/papoytsia') }}" class="text-sm font-bold text-black hover:underline">Ανδρικά Παπούτσια</a>
                    </div>

                    {{-- Αξεσουάρ --}}
                    <div>
                        <a href="{{ url('/product-category/axesouar') }}" class="text-sm font-bold text-black hover:underline">Αξεσουάρ</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="{{ url('/product-category/gravates') }}" class="text-sm text-gray-600 hover:text-black">Γραβάτες & Παπιόν</a></li>
                            <li><a href="{{ url('/product-category/zones') }}" class="text-sm text-gray-600 hover:text-black">Ζώνες</a></li>
                            <li><a href="{{ url('/product-category/kaskol') }}" class="text-sm text-gray-600 hover:text-black">Κασκόλ</a></li>
                            <li><a href="{{ url('/product-category/maniketokouba') }}" class="text-sm text-gray-600 hover:text-black">Μανικετόκουμπα</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Mobile menu overlay --}}
        <div x-show="mobileMenu"
             x-cloak
             class="lg:hidden fixed inset-0 z-40"
             role="dialog"
             aria-modal="true"
             aria-label="Navigation menu"
             id="mobile-menu">
            {{-- Backdrop --}}
            <div x-show="mobileMenu"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenu = false"
                 class="fixed inset-0 bg-black/25"
                 aria-hidden="true">
            </div>

            {{-- Slide-in panel --}}
            <div x-show="mobileMenu"
                 x-trap.inert.noscroll="mobileMenu"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 @keydown.escape.window="mobileMenu = false"
                 class="fixed inset-y-0 left-0 w-full max-w-xs bg-white shadow-xl transform overflow-y-auto">
                {{-- Close button --}}
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200">
                    <img src="{{ asset('images/dressman-logo-big.png') }}" alt="Dressman" class="h-6 w-auto">
                    <button
                        type="button"
                        @click="mobileMenu = false"
                        class="p-2 -mr-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                        aria-label="Close navigation menu"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Search bar --}}
                <div class="px-4 py-3 border-b border-gray-200">
                    <form action="{{ url('/search') }}" method="GET" class="relative">
                        <div class="relative">
                            <input
                                type="search"
                                name="q"
                                placeholder="Αναζήτηση..."
                                class="w-full pl-9 pr-4 py-2 text-sm bg-gray-100 border-0 rounded-none focus:outline-none focus:ring-1 focus:ring-black"
                            >
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </form>
                </div>

                {{-- Mobile nav with tabs --}}
                <div class="flex flex-col h-[calc(100%-4rem-3.5rem)]" x-data="{ tab: 'fashion' }">
                    {{-- Tab buttons --}}
                    <div class="flex border-b border-gray-200">
                        <button
                            type="button"
                            @click="tab = 'fashion'"
                            :class="tab === 'fashion' ? 'border-black text-black' : 'border-transparent text-gray-500'"
                            class="flex-1 py-3 text-xs font-semibold uppercase tracking-wider border-b-2 transition-colors"
                        >Fashion</button>
                        <button
                            type="button"
                            @click="tab = 'links'"
                            :class="tab === 'links' ? 'border-black text-black' : 'border-transparent text-gray-500'"
                            class="flex-1 py-3 text-xs font-semibold uppercase tracking-wider border-b-2 transition-colors"
                        >Useful Links</button>
                    </div>

                    {{-- Tab content --}}
                    <nav class="flex-1 overflow-y-auto" aria-label="Mobile navigation">
                        {{-- Tab 1: Fashion Categories --}}
                        <div x-show="tab === 'fashion'" class="px-4 py-4 space-y-1">
                            <a href="{{ url('/product-category/gabriatika-kostoumia') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Γαμπριάτικα Κοστούμια</a>
                            <a href="{{ url('/product-category/rent') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Ενοικίαση</a>
                            <a href="{{ url('/metapoiisi') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Μεταποίηση</a>
                            <a href="{{ url('/made-to-measure') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Made to Measure</a>
                            {{-- Ανδρικά Ρούχα with subcategories --}}
                            <div class="pt-2 mt-2 border-t border-gray-100">
                                <span class="block px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Ανδρικά Ρούχα</span>
                                <a href="{{ url('/product-category/kostoumia') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Κοστούμια</a>
                                <a href="{{ url('/product-category/poykamisa') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Πουκάμισα</a>
                                <a href="{{ url('/product-category/gileka') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Γιλέκα</a>
                                <a href="{{ url('/product-category/sakakia') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Σακάκια</a>
                                <a href="{{ url('/product-category/mplouzes') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Μπλούζες</a>
                                <a href="{{ url('/product-category/pantelonia') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Παντελόνια</a>
                                <a href="{{ url('/product-category/papoytsia') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Παπούτσια</a>
                                <a href="{{ url('/product-category/axesouar') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 pl-6">Αξεσουάρ</a>
                            </div>
                        </div>

                        {{-- Tab 2: Useful Links --}}
                        <div x-show="tab === 'links'" x-cloak class="px-4 py-4 space-y-1">
                            <a href="{{ url('/blog') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Blog</a>
                            <a href="{{ url('/faq') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">FAQ</a>
                            <a href="{{ url('/payment-methods') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Τρόποι Πληρωμής</a>
                            <a href="{{ url('/returns') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Επιστροφές Αγορών</a>
                            <a href="{{ url('/privacy') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Πολιτική Απορρήτου</a>
                            <a href="{{ url('/terms') }}" class="block px-3 py-2.5 text-base font-medium text-gray-900 hover:bg-gray-50">Όροι & Προϋποθέσεις</a>
                        </div>
                    </nav>

                    {{-- Bottom: Contact + Social --}}
                    <div class="border-t border-gray-200 px-4 py-4 mt-auto">
                        {{-- Social icons --}}
                        <div class="flex items-center justify-center gap-4 mb-4">
                            <a href="https://facebook.com/dressman.gr" target="_blank" rel="noopener" aria-label="Facebook" class="text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                            </a>
                            <a href="https://instagram.com/dressman.gr" target="_blank" rel="noopener" aria-label="Instagram" class="text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                            </a>
                            <a href="https://pinterest.com/dressman_gr" target="_blank" rel="noopener" aria-label="Pinterest" class="text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.237 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.181-.78 1.172-4.97 1.172-4.97s-.299-.598-.299-1.482c0-1.388.806-2.425 1.808-2.425.853 0 1.265.64 1.265 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.177-4.068-2.845 0-4.515 2.135-4.515 4.34 0 .859.331 1.781.745 2.281a.3.3 0 01.069.288l-.278 1.133c-.044.183-.145.222-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.527-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                            </a>
                            <a href="https://twitter.com/dressman_gr" target="_blank" rel="noopener" aria-label="Twitter" class="text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a href="https://youtube.com/@dressman_gr" target="_blank" rel="noopener" aria-label="YouTube" class="text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        </div>

                        {{-- Store addresses --}}
                        <div class="space-y-3 text-xs text-gray-600">
                            <div>
                                <p class="font-semibold text-gray-900 uppercase tracking-wider text-[10px] mb-1">Boutique</p>
                                <a href="https://maps.google.com/?q=Σκουφά+10,+10673+Κολωνάκι+Αθήνα" target="_blank" rel="noopener" class="flex items-start gap-1.5 hover:text-black">
                                    <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                    <span>Σκουφά 10, 10673 Κολωνάκι, Αθήνα</span>
                                </a>
                                <a href="tel:+302155004038" class="flex items-center gap-1.5 mt-1 hover:text-black">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                    <span>+30 215 500 4038</span>
                                </a>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 uppercase tracking-wider text-[10px] mb-1">Boutique & Stockhouse</p>
                                <a href="https://maps.google.com/?q=Λεωφόρος+Φυλής+116,+13341+Άνω+Λιόσια+Αθήνα" target="_blank" rel="noopener" class="flex items-start gap-1.5 hover:text-black">
                                    <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                    <span>Λεωφόρος Φυλής 116, 13341 Α. Λιόσια, Αθήνα</span>
                                </a>
                                <a href="tel:+302102483370" class="flex items-center gap-1.5 mt-1 hover:text-black">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                    <span>+30 210 2483 370</span>
                                </a>
                            </div>
                        </div>

                        {{-- Quick action icons --}}
                        <div class="flex items-center justify-center gap-5 mt-4 pt-3 border-t border-gray-100">
                            <a href="tel:+302155004038" aria-label="Καλέστε μας" class="flex flex-col items-center gap-1 text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                <span class="text-[9px] uppercase tracking-wider">Call</span>
                            </a>
                            <a href="mailto:info@dressman.gr" aria-label="Email" class="flex flex-col items-center gap-1 text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <span class="text-[9px] uppercase tracking-wider">Email</span>
                            </a>
                            <a href="https://maps.google.com/?q=Σκουφά+10,+10673+Κολωνάκι+Αθήνα" target="_blank" rel="noopener" aria-label="Οδηγίες" class="flex flex-col items-center gap-1 text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span class="text-[9px] uppercase tracking-wider">Map</span>
                            </a>
                            <a href="https://wa.me/302155004038" target="_blank" rel="noopener" aria-label="WhatsApp" class="flex flex-col items-center gap-1 text-gray-500 hover:text-black">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                                <span class="text-[9px] uppercase tracking-wider">WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Breadcrumb --}}
    @isset($breadcrumb)
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                {{ $breadcrumb }}
            </div>
        </div>
    @endisset

    {{-- Main content --}}
    <main id="main-content" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Newsletter --}}
    <section class="text-white" style="background:linear-gradient(135deg,#A0523D 0%,#B5654A 40%,#8B4232 100%)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:flex lg:items-center lg:justify-between lg:gap-8">
                <div class="lg:flex-1">
                    <h2 class="text-lg font-bold tracking-wide">Εγγραφείτε στο Newsletter</h2>
                    <p class="mt-1 text-sm" style="color:rgba(255,255,255,0.75)">Μάθετε πρώτοι για νέες αφίξεις, προσφορές και στυλιστικές συμβουλές.</p>
                </div>
                <form class="mt-4 lg:mt-0 flex gap-3 w-full lg:w-auto lg:min-w-[400px]" action="#" method="POST">
                    @csrf
                    <label for="newsletter-email" class="sr-only">Email</label>
                    <input
                        type="email"
                        id="newsletter-email"
                        name="email"
                        required
                        placeholder="Το email σας"
                        class="flex-1 min-w-0 px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent"
                        style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3)"
                    >
                    <button type="submit" class="px-6 py-3 bg-white text-sm font-semibold hover:bg-gray-100 transition-colors whitespace-nowrap" style="color:#B5654A">
                        Εγγραφή
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-black text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div>
                    <img src="{{ asset('images/dressman-logo-white.png') }}" alt="Dressman" class="h-8 w-auto">
                    <p class="mt-4 text-sm leading-relaxed">
                        Ανδρικά ρούχα υψηλής ποιότητας. Κοστούμια, πουκάμισα, αξεσουάρ.
                    </p>
                </div>

                {{-- Quick links --}}
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Κατηγορίες</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="{{ url('/product-category/gabriatika-kostoumia') }}" class="text-sm hover:text-white transition-colors">Γαμπριάτικα Κοστούμια</a></li>
                        <li><a href="{{ url('/product-category/rent') }}" class="text-sm hover:text-white transition-colors">Ενοικίαση Κοστουμιών</a></li>
                        <li><a href="{{ url('/metapoiisi') }}" class="text-sm hover:text-white transition-colors">Μεταποίηση Κοστουμιών</a></li>
                        <li><a href="{{ url('/made-to-measure') }}" class="text-sm hover:text-white transition-colors">Made to Measure</a></li>
                        <li><a href="{{ url('/product-category/kostoumia') }}" class="text-sm hover:text-white transition-colors">Ανδρικά Ρούχα</a></li>
                    </ul>
                </div>

                {{-- Customer service --}}
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Εξυπηρέτηση</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="{{ url('/contact') }}" class="text-sm hover:text-white transition-colors">Επικοινωνία</a></li>
                        <li><a href="{{ url('/shipping') }}" class="text-sm hover:text-white transition-colors">Αποστολές</a></li>
                        <li><a href="{{ url('/returns') }}" class="text-sm hover:text-white transition-colors">Επιστροφές</a></li>
                        <li><a href="{{ url('/faq') }}" class="text-sm hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Πληροφορίες</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="{{ url('/about') }}" class="text-sm hover:text-white transition-colors">Σχετικά</a></li>
                        <li><a href="{{ url('/privacy') }}" class="text-sm hover:text-white transition-colors">Πολιτική Απορρήτου</a></li>
                        <li><a href="{{ url('/terms') }}" class="text-sm hover:text-white transition-colors">Όροι Χρήσης</a></li>
                        <li><a href="{{ url('/cookie-policy') }}" class="text-sm hover:text-white transition-colors">Πολιτική Cookies</a></li>
                        <li><button type="button" onclick="openCookieSettings()" class="text-sm text-gray-300 hover:text-white transition-colors">Ρυθμίσεις Cookies</button></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm">
                <p>&copy; {{ date('Y') }} Dressman. All rights reserved.</p>
                <p>Performance Eshop by <a href="https://symbols.gr/sxediasmos-istoselidas" target="_blank" rel="noopener" class="text-white hover:underline">Symbols</a></p>
            </div>
        </div>
    </footer>

    {{-- Cart drawer (site-wide, Alpine island) --}}
    <div
        x-data="cartDrawer"
        @cart-open.window="handleCartOpen($event)"
        x-cloak
    >
        {{-- Backdrop --}}
        <div
            x-show="show"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 z-50 bg-black/25"
            aria-hidden="true"
        ></div>

        {{-- Slide-in panel (right) --}}
        <div
            x-show="show"
            x-trap.inert.noscroll="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @keydown.escape.window="close()"
            @click="handleClick($event)"
            @change="handleChange($event)"
            class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-xl flex flex-col"
            role="dialog"
            aria-modal="true"
            aria-label="Shopping cart"
            x-ref="content"
        >
            {{-- Content loaded via fetch --}}
        </div>
    </div>

    {{-- Wishlist drawer (site-wide, Alpine island) --}}
    <div
        x-data="wishlistDrawer"
        @wishlist-open.window="handleWishlistOpen($event)"
        x-cloak
    >
        {{-- Backdrop --}}
        <div
            x-show="show"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 z-50 bg-black/25"
            aria-hidden="true"
        ></div>

        {{-- Slide-in panel (right) --}}
        <div
            x-show="show"
            x-trap.inert.noscroll="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @keydown.escape.window="close()"
            @click="handleClick($event)"
            class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-xl flex flex-col"
            role="dialog"
            aria-modal="true"
            aria-label="Wishlist"
            x-ref="content"
        >
            {{-- Content loaded via fetch --}}
        </div>
    </div>

    {{-- Cookie Consent Banner --}}
    <div
        x-data="cookieConsent()"
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        x-cloak
        class="fixed bottom-0 inset-x-0 z-50 bg-white border-t border-gray-200 shadow-2xl"
        role="dialog"
        aria-label="Cookie preferences"
    >
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-5">
            {{-- Compact view --}}
            <div x-show="!expanded">
                <div class="sm:flex sm:items-start sm:justify-between sm:gap-6">
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Χρησιμοποιούμε cookies για τη σωστή λειτουργία της ιστοσελίδας, ανάλυση επισκεψιμότητας και εξατομικευμένο marketing.
                            <a href="{{ url('/cookie-policy') }}" class="underline hover:text-black">Πολιτική Cookies</a>
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center gap-3 shrink-0">
                        <button @click="expanded = true" type="button" class="text-sm font-medium text-gray-600 hover:text-black underline">Ρυθμίσεις</button>
                        <button @click="rejectAll()" type="button" class="px-4 py-2 text-sm font-medium border border-gray-300 hover:bg-gray-50 transition-colors">Απόρριψη</button>
                        <button @click="acceptAll()" type="button" class="px-4 py-2 text-sm font-medium bg-black text-white hover:bg-gray-800 transition-colors">Αποδοχή όλων</button>
                    </div>
                </div>
            </div>

            {{-- Expanded view with options --}}
            <div x-show="expanded" x-cloak>
                <h2 class="text-base font-bold text-gray-900">Ρυθμίσεις Cookies</h2>
                <p class="mt-1 text-sm text-gray-600">Επιλέξτε ποιες κατηγορίες cookies επιθυμείτε να ενεργοποιήσετε.</p>

                <div class="mt-4 space-y-3">
                    {{-- Necessary (always on) --}}
                    <label class="flex items-start gap-3">
                        <input type="checkbox" checked disabled class="mt-0.5 rounded border-gray-300 text-black">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Απαραίτητα</span>
                            <p class="text-xs text-gray-500">Απαραίτητα για τη λειτουργία της ιστοσελίδας (session, CSRF, καλάθι). Δεν μπορούν να απενεργοποιηθούν.</p>
                        </div>
                    </label>

                    {{-- Analytics --}}
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" x-model="prefs.analytics" class="mt-0.5 rounded border-gray-300 text-black focus:ring-black">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Ανάλυση / Στατιστικά</span>
                            <p class="text-xs text-gray-500">Google Analytics — ανώνυμη παρακολούθηση επισκεψιμότητας για τη βελτίωση της ιστοσελίδας.</p>
                        </div>
                    </label>

                    {{-- Marketing --}}
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" x-model="prefs.marketing" class="mt-0.5 rounded border-gray-300 text-black focus:ring-black">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Marketing / Διαφήμιση</span>
                            <p class="text-xs text-gray-500">Facebook Pixel, Google Ads — εξατομικευμένες διαφημίσεις βάσει των ενδιαφερόντων σας.</p>
                        </div>
                    </label>
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <a href="{{ url('/cookie-policy') }}" class="text-sm text-gray-500 underline hover:text-black">Πολιτική Cookies</a>
                    <div class="flex items-center gap-3">
                        <button @click="expanded = false" type="button" class="text-sm text-gray-600 hover:text-black">Πίσω</button>
                        <button @click="savePrefs()" type="button" class="px-5 py-2 text-sm font-medium bg-black text-white hover:bg-gray-800 transition-colors">Αποθήκευση</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function cookieConsent() {
        return {
            visible: false,
            expanded: false,
            prefs: { analytics: false, marketing: false },
            init() {
                const stored = this.getCookie('cookie_consent');
                if (!stored) {
                    this.visible = true;
                } else {
                    try {
                        const parsed = JSON.parse(stored);
                        this.prefs = { analytics: !!parsed.analytics, marketing: !!parsed.marketing };
                        this.applyConsent();
                    } catch(e) { this.visible = true; }
                }
            },
            acceptAll() {
                this.prefs = { analytics: true, marketing: true };
                this.save();
            },
            rejectAll() {
                this.prefs = { analytics: false, marketing: false };
                this.save();
            },
            savePrefs() {
                this.save();
            },
            save() {
                const val = JSON.stringify({ necessary: true, analytics: this.prefs.analytics, marketing: this.prefs.marketing, timestamp: new Date().toISOString() });
                document.cookie = 'cookie_consent=' + encodeURIComponent(val) + ';path=/;max-age=31536000;SameSite=Lax';
                this.visible = false;
                this.applyConsent();
            },
            applyConsent() {
                if (this.prefs.analytics) {
                    // Load Google Analytics (replace GA_ID with actual ID)
                    // if (!window.gtag) { ... }
                }
                if (this.prefs.marketing) {
                    // Load Facebook Pixel, Google Ads etc.
                }
            },
            getCookie(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? decodeURIComponent(match[2]) : null;
            },
            openSettings() {
                this.expanded = true;
                this.visible = true;
            }
        }
    }

    // Global function so footer button can trigger it
    window.openCookieSettings = function() {
        const el = document.querySelector('[x-data*="cookieConsent"]');
        if (el && el.__x) { el.__x.$data.openSettings(); }
        else if (el) { el.dispatchEvent(new CustomEvent('open-cookie-settings')); }
    }
    </script>

    @stack('scripts')
</body>
</html>
