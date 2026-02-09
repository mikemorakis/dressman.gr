<!DOCTYPE html>
<html lang="el" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo e($seo ?? ''); ?>


    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-serif-display:400,500,600,700&family=roboto:400,500,700&display=swap" rel="stylesheet" />

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-sans antialiased bg-white text-gray-900 flex flex-col min-h-screen">
    
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-black focus:text-white focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
        Skip to main content
    </a>

    
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200"
            x-data="{ mobileMenu: false, megaMenu: false }"
            x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileMenu)">

        
        <div class="border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 lg:h-20">
                    
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

                    
                    <a href="<?php echo e(url('/search')); ?>"
                       class="hidden lg:block p-2 text-gray-700 hover:text-black focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                       aria-label="Search products">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </a>

                    
                    <a href="<?php echo e(url('/')); ?>" class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0 lg:flex-1 lg:text-center" aria-label="Dressman home">
                        <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/dressman-logo-big.png','alt' => 'Dressman','class' => 'h-8 sm:h-10 lg:h-12 w-auto mx-auto','loading' => 'eager','fetchpriority' => 'high']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/dressman-logo-big.png','alt' => 'Dressman','class' => 'h-8 sm:h-10 lg:h-12 w-auto mx-auto','loading' => 'eager','fetchpriority' => 'high']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $attributes = $__attributesOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $component = $__componentOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__componentOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
                    </a>

                    
                    <div class="flex items-center gap-x-2 sm:gap-x-3">
                        
                        <a href="<?php echo e(url('/search')); ?>"
                           class="lg:hidden p-2 text-gray-700 hover:text-black focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2"
                           aria-label="Search products">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </a>

                        
                        <?php if (isset($component)) { $__componentOriginalff467597409d3d5104c229cfe35ec26e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalff467597409d3d5104c229cfe35ec26e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cart-icon','data' => ['count' => app(App\Services\CartService::class)->count()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cart-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app(App\Services\CartService::class)->count())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalff467597409d3d5104c229cfe35ec26e)): ?>
<?php $attributes = $__attributesOriginalff467597409d3d5104c229cfe35ec26e; ?>
<?php unset($__attributesOriginalff467597409d3d5104c229cfe35ec26e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalff467597409d3d5104c229cfe35ec26e)): ?>
<?php $component = $__componentOriginalff467597409d3d5104c229cfe35ec26e; ?>
<?php unset($__componentOriginalff467597409d3d5104c229cfe35ec26e); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <nav class="hidden lg:flex justify-center gap-x-8 relative" aria-label="Main navigation">
                    <a href="<?php echo e(url('/product-category/kostoumia')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]" @mouseenter="megaMenu = true" :aria-expanded="megaMenu.toString()">Ανδρικά Ρούχα <svg class="inline h-3.5 w-3.5 -mt-px transition-transform" :class="megaMenu && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg></a>
                    <a href="<?php echo e(url('/product-category/gabriatika-kostoumia')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Γαμπριάτικα Κοστούμια</a>
                    <a href="<?php echo e(url('/product-category/rent')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Ενοικίαση</a>
                    <a href="<?php echo e(url('/metapoiisi')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Μεταποίηση</a>
                    <a href="<?php echo e(url('/made-to-measure')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Made to Measure</a>
                    <a href="<?php echo e(url('/blog')); ?>" class="text-sm font-medium text-gray-700 hover:text-black transition-colors leading-[3.25rem]">Blog</a>

            
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
                    
                    <div>
                        <a href="<?php echo e(url('/product-category/kostoumia')); ?>" class="text-sm font-bold text-black hover:underline">Κοστούμια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/business-kostoymia')); ?>" class="text-sm text-gray-600 hover:text-black">Business Κοστούμια</a></li>
                            <li><a href="<?php echo e(url('/product-category/kostoymia-megala-megethi')); ?>" class="text-sm text-gray-600 hover:text-black">Μεγάλα Μεγέθη</a></li>
                            <li><a href="<?php echo e(url('/product-category/gabriatika-kostoumia')); ?>" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Κοστούμια</a></li>
                            <li><a href="<?php echo e(url('/product-category/tuxedo')); ?>" class="text-sm text-gray-600 hover:text-black">Tuxedo</a></li>
                        </ul>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/poykamisa')); ?>" class="text-sm font-bold text-black hover:underline">Πουκάμισα</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/gampriatika-poykamisa')); ?>" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Πουκάμισα</a></li>
                            <li><a href="<?php echo e(url('/product-category/business-poykamisa')); ?>" class="text-sm text-gray-600 hover:text-black">Business Πουκάμισα</a></li>
                        </ul>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/gileka')); ?>" class="text-sm font-bold text-black hover:underline">Γιλέκα</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/business-gileka')); ?>" class="text-sm text-gray-600 hover:text-black">Business Γιλέκα</a></li>
                            <li><a href="<?php echo e(url('/product-category/gabriatika-gileka')); ?>" class="text-sm text-gray-600 hover:text-black">Γαμπριάτικα Γιλέκα</a></li>
                        </ul>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/sakakia')); ?>" class="text-sm font-bold text-black hover:underline">Σακάκια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/business-sakakia')); ?>" class="text-sm text-gray-600 hover:text-black">Business Σακάκια</a></li>
                            <li><a href="<?php echo e(url('/product-category/palto')); ?>" class="text-sm text-gray-600 hover:text-black">Παλτό</a></li>
                        </ul>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/mplouzes')); ?>" class="text-sm font-bold text-black hover:underline">Μπλούζες</a>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/pantelonia')); ?>" class="text-sm font-bold text-black hover:underline">Παντελόνια</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/business-panteloni')); ?>" class="text-sm text-gray-600 hover:text-black">Business Παντελόνια</a></li>
                            <li><a href="<?php echo e(url('/product-category/tsinos')); ?>" class="text-sm text-gray-600 hover:text-black">Τσίνος</a></li>
                            <li><a href="<?php echo e(url('/product-category/tzin')); ?>" class="text-sm text-gray-600 hover:text-black">Τζιν</a></li>
                        </ul>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/papoytsia')); ?>" class="text-sm font-bold text-black hover:underline">Ανδρικά Παπούτσια</a>
                    </div>

                    
                    <div>
                        <a href="<?php echo e(url('/product-category/axesouar')); ?>" class="text-sm font-bold text-black hover:underline">Αξεσουάρ</a>
                        <ul class="mt-2 space-y-1.5">
                            <li><a href="<?php echo e(url('/product-category/gravates')); ?>" class="text-sm text-gray-600 hover:text-black">Γραβάτες & Παπιόν</a></li>
                            <li><a href="<?php echo e(url('/product-category/zones')); ?>" class="text-sm text-gray-600 hover:text-black">Ζώνες</a></li>
                            <li><a href="<?php echo e(url('/product-category/kaskol')); ?>" class="text-sm text-gray-600 hover:text-black">Κασκόλ</a></li>
                            <li><a href="<?php echo e(url('/product-category/maniketokouba')); ?>" class="text-sm text-gray-600 hover:text-black">Μανικετόκουμπα</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        
        <div x-show="mobileMenu"
             x-cloak
             class="lg:hidden fixed inset-0 z-40"
             role="dialog"
             aria-modal="true"
             aria-label="Navigation menu"
             id="mobile-menu">
            
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
                
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200">
                    <span class="text-xl font-bold text-black tracking-widest font-serif">DRESSMAN</span>
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

                
                <nav class="px-4 py-4" aria-label="Mobile navigation" x-data="{ openCat: null }">
                    
                    <div class="border-b border-gray-100 pb-3 mb-3">
                        <button type="button" @click="openCat = openCat === 'men' ? null : 'men'" class="flex items-center justify-between w-full px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">
                            Ανδρικά Ρούχα
                            <svg class="h-4 w-4 transition-transform" :class="openCat === 'men' && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                        <div x-show="openCat === 'men'" x-collapse x-cloak class="pl-4 mt-1 space-y-1">
                            <a href="<?php echo e(url('/product-category/kostoumia')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Κοστούμια</a>
                            <a href="<?php echo e(url('/product-category/poykamisa')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Πουκάμισα</a>
                            <a href="<?php echo e(url('/product-category/gileka')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Γιλέκα</a>
                            <a href="<?php echo e(url('/product-category/sakakia')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Σακάκια</a>
                            <a href="<?php echo e(url('/product-category/mplouzes')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Μπλούζες</a>
                            <a href="<?php echo e(url('/product-category/pantelonia')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Παντελόνια</a>
                            <a href="<?php echo e(url('/product-category/papoytsia')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Ανδρικά Παπούτσια</a>
                            <a href="<?php echo e(url('/product-category/axesouar')); ?>" class="block px-3 py-1.5 text-sm font-medium text-gray-900 hover:bg-gray-50">Αξεσουάρ</a>
                        </div>
                    </div>

                    <a href="<?php echo e(url('/product-category/gabriatika-kostoumia')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Γαμπριάτικα Κοστούμια</a>
                    <a href="<?php echo e(url('/product-category/rent')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Ενοικίαση</a>
                    <a href="<?php echo e(url('/metapoiisi')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Μεταποίηση</a>
                    <a href="<?php echo e(url('/made-to-measure')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Made to Measure</a>
                    <a href="<?php echo e(url('/blog')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Blog</a>
                    <a href="<?php echo e(url('/search')); ?>" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">Αναζήτηση</a>
                </nav>
            </div>
        </div>
    </header>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumb)): ?>
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <?php echo e($breadcrumb); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <main id="main-content" class="flex-1">
        <?php echo e($slot); ?>

    </main>

    
    <footer class="bg-black text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <div>
                    <span class="text-xl font-bold text-white tracking-widest font-serif">DRESSMAN</span>
                    <p class="mt-4 text-sm leading-relaxed">
                        Ανδρικά ρούχα υψηλής ποιότητας. Κοστούμια, πουκάμισα, αξεσουάρ.
                    </p>
                </div>

                
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Κατηγορίες</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="<?php echo e(url('/product-category/kostoumia')); ?>" class="text-sm hover:text-white transition-colors">Κοστούμια</a></li>
                        <li><a href="<?php echo e(url('/product-category/poykamisa')); ?>" class="text-sm hover:text-white transition-colors">Πουκάμισα</a></li>
                        <li><a href="<?php echo e(url('/product-category/pantelonia')); ?>" class="text-sm hover:text-white transition-colors">Παντελόνια</a></li>
                        <li><a href="<?php echo e(url('/product-category/axesouar')); ?>" class="text-sm hover:text-white transition-colors">Αξεσουάρ</a></li>
                    </ul>
                </div>

                
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Εξυπηρέτηση</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="<?php echo e(url('/contact')); ?>" class="text-sm hover:text-white transition-colors">Επικοινωνία</a></li>
                        <li><a href="<?php echo e(url('/shipping')); ?>" class="text-sm hover:text-white transition-colors">Αποστολές</a></li>
                        <li><a href="<?php echo e(url('/returns')); ?>" class="text-sm hover:text-white transition-colors">Επιστροφές</a></li>
                    </ul>
                </div>

                
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider">Πληροφορίες</h2>
                    <ul class="mt-4 space-y-2" role="list">
                        <li><a href="<?php echo e(url('/about')); ?>" class="text-sm hover:text-white transition-colors">Σχετικά</a></li>
                        <li><a href="<?php echo e(url('/privacy')); ?>" class="text-sm hover:text-white transition-colors">Πολιτική Απορρήτου</a></li>
                        <li><a href="<?php echo e(url('/terms')); ?>" class="text-sm hover:text-white transition-colors">Όροι Χρήσης</a></li>
                    </ul>
                </div>
            </div>

            
            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-sm">
                <p>&copy; <?php echo e(date('Y')); ?> Dressman. All rights reserved.</p>
            </div>
        </div>
    </footer>

    
    <div
        x-data="cartDrawer"
        @cart-open.window="handleCartOpen($event)"
        x-cloak
    >
        
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
            
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\components\layouts\app.blade.php ENDPATH**/ ?>