<?php
    $faq = [
        [
            'q' => 'Πώς επιλέγω το σωστό γαμπριάτικο κοστούμι;',
            'a' => 'Η επιλογή του γαμπριάτικου κοστουμιού εξαρτάται από το στυλ του γάμου, την εποχή και τις προσωπικές σας προτιμήσεις. Στη Dressman, οι σύμβουλοί μας σας καθοδηγούν στην επιλογή υφάσματος, χρώματος και εφαρμογής που ταιριάζει στο σωματότυπό σας. Κλείστε ένα ραντεβού για δοκιμή στο κατάστημά μας.',
        ],
        [
            'q' => 'Τι περιλαμβάνει ένα ολοκληρωμένο γαμπριάτικο κοστούμι;',
            'a' => 'Ένα ολοκληρωμένο γαμπριάτικο κοστούμι περιλαμβάνει σακάκι, παντελόνι, πουκάμισο, γραβάτα ή παπιγιόν, γιλέκο (προαιρετικά) και αξεσουάρ όπως μανικετόκουμπα και μαντήλι τσέπης. Στη Dressman προσφέρουμε πλήρεις συνδυασμούς σε ανταγωνιστικές τιμές.',
        ],
        [
            'q' => 'Πόσο πριν τον γάμο πρέπει να επιλέξω κοστούμι γαμπρού;',
            'a' => 'Συνιστούμε να ξεκινήσετε την αναζήτηση 2-3 μήνες πριν τον γάμο. Αυτό δίνει αρκετό χρόνο για δοκιμές, πιθανές μεταποιήσεις και τελικές ρυθμίσεις. Για Made to Measure κοστούμια, χρειάζεστε τουλάχιστον 4-6 εβδομάδες.',
        ],
        [
            'q' => 'Μπορώ να νοικιάσω γαμπριάτικο κοστούμι αντί να αγοράσω;',
            'a' => 'Ναι, η Dressman προσφέρει υπηρεσία ενοικίασης σμόκιν και γαμπριάτικων κοστουμιών. Είναι ιδανική επιλογή αν θέλετε premium κοστούμι σε προσιτή τιμή. Η ενοικίαση περιλαμβάνει πλήρη προσαρμογή στα μέτρα σας.',
        ],
        [
            'q' => 'Κάνετε μεταποιήσεις στα γαμπριάτικα κοστούμια;',
            'a' => 'Φυσικά. Η μόδιστρά μας με πάνω από 40 χρόνια εμπειρίας αναλαμβάνει κάθε μεταποίηση — από στένεμα μέσης και κοντύνω μανικιών μέχρι πλήρη αναπροσαρμογή. Η τέλεια εφαρμογή είναι το κλειδί για ένα εντυπωσιακό γαμπριάτικο κοστούμι.',
        ],
        [
            'q' => 'Τι χρώματα υπάρχουν σε γαμπριάτικα κοστούμια;',
            'a' => 'Η συλλογή μας περιλαμβάνει κλασικά χρώματα (μαύρο, μπλε navy, ανθρακί), σύγχρονα (μπλε royal, μπορντό, πράσινο) και καλοκαιρινά (μπεζ, σιέλ, λευκό). Το χρώμα εξαρτάται από τη θεματολογία του γάμου και τη σεζόν.',
        ],
    ];

    $faqJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($item) => [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['a'],
            ],
        ], $faq),
    ];
?>

<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('seo', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal84f9df3f620371229981225e7ba608d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84f9df3f620371229981225e7ba608d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => 'Γαμπριάτικα Κοστούμια 2026 — Κοστούμι Γαμπρού | Dressman','description' => 'Γαμπριάτικα κοστούμια σε κλασικές και σύγχρονες γραμμές. Κοστούμι γαμπρού για κάθε στυλ γάμου. Made to Measure, ενοικίαση σμόκιν & δωρεάν μεταποιήσεις. Dressman — Αθήνα.','jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'Dressman',
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/search') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Γαμπριάτικα Κοστούμια 2026 — Κοστούμι Γαμπρού | Dressman','description' => 'Γαμπριάτικα κοστούμια σε κλασικές και σύγχρονες γραμμές. Κοστούμι γαμπρού για κάθε στυλ γάμου. Made to Measure, ενοικίαση σμόκιν & δωρεάν μεταποιήσεις. Dressman — Αθήνα.','jsonLd' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'Dressman',
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/search') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84f9df3f620371229981225e7ba608d7)): ?>
<?php $attributes = $__attributesOriginal84f9df3f620371229981225e7ba608d7; ?>
<?php unset($__attributesOriginal84f9df3f620371229981225e7ba608d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84f9df3f620371229981225e7ba608d7)): ?>
<?php $component = $__componentOriginal84f9df3f620371229981225e7ba608d7; ?>
<?php unset($__componentOriginal84f9df3f620371229981225e7ba608d7); ?>
<?php endif; ?>
        <script type="application/ld+json"><?php echo json_encode($faqJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
     <?php $__env->endSlot(); ?>

    
    <section
        x-data="{
            current: 0,
            slides: [
                { img: '<?php echo e(asset('images/home-bg.jpg')); ?>', title: 'Γαμπριάτικα Κοστούμια 2026', subtitle: 'Κοστούμι γαμπρού για κάθε στυλ γάμου. Κλασικές και σύγχρονες γραμμές με υφάσματα που αναδεικνύουν κάθε σωματότυπο.', link: '<?php echo e(url('/product-category/gabriatika-kostoumia')); ?>', cta: 'Δείτε τη Συλλογή' },
                { img: '<?php echo e(asset('images/mtm-hero.jpeg')); ?>', title: 'Made to Measure', subtitle: 'Δεν είναι απλά ένα ρούχο, είναι τρόπος ζωής. Κοστούμι φτιαγμένο στα μέτρα σας.', link: '<?php echo e(url('/made-to-measure')); ?>', cta: 'Μάθετε Περισσότερα' },
                { img: '<?php echo e(asset('images/metapoiisi-hero.jpeg')); ?>', title: 'Ενοικίαση Κοστουμιών', subtitle: 'Premium γαμπριάτικα κοστούμια και σμόκιν σε προσιτές τιμές ενοικίασης.', link: '<?php echo e(url('/product-category/rent')); ?>', cta: 'Δείτε τις Επιλογές' }
            ],
            timer: null,
            start() { this.timer = setInterval(() => this.next(), 5000) },
            stop() { clearInterval(this.timer) },
            next() { this.current = (this.current + 1) % this.slides.length },
            prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length },
            goTo(i) { this.current = i; this.stop(); this.start(); }
        }"
        x-init="start()"
        @mouseenter="stop()"
        @mouseleave="start()"
        class="relative bg-black overflow-hidden"
        aria-label="Hero slider"
    >
        
        <template x-for="(slide, index) in slides" :key="index">
            <div
                x-show="current === index"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0"
            >
                <img :src="slide.img" :alt="slide.title" class="w-full h-full object-cover" decoding="async">
            </div>
        </template>

        
        <div class="absolute inset-0" style="background:rgba(0,0,0,0.55)"></div>

        
        <div class="relative flex items-center justify-center h-[80vh]">
            <div class="max-w-3xl mx-auto px-6 text-center text-white">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight" x-text="slides[current].title"></h1>
                <p class="mt-6 text-base sm:text-lg leading-relaxed text-white/90" x-text="slides[current].subtitle"></p>
                <a :href="slides[current].link" class="mt-8 inline-block bg-white text-gray-900 px-8 py-3 text-sm font-semibold hover:bg-gray-100 transition-colors" x-text="slides[current].cta"></a>
            </div>
        </div>

        
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex" style="gap:8px">
            <template x-for="(slide, index) in slides" :key="'dot-'+index">
                <span
                    @click="goTo(index)"
                    :style="'display:block;height:4px;min-height:4px;min-width:0;cursor:pointer;transition:all 0.3s ease;width:' + (current === index ? '40px' : '24px') + ';background:' + (current === index ? '#fff' : 'rgba(255,255,255,0.4)')"
                    :aria-label="'Go to slide ' + (index + 1)"
                    :aria-current="current === index ? 'true' : 'false'"
                ></span>
            </template>
        </div>
    </section>

    
    <section class="bg-black text-white py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center text-sm">
                <div class="flex flex-col items-center gap-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    <span>Δωρεάν Αποστολή</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.696.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" /></svg>
                    <span>Δωρεάν Μεταποιήσεις</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    <span>Εγγύηση Ποιότητας</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    <span>Ραντεβού στο Κατάστημα</span>
                </div>
            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bestSellers->isNotEmpty()): ?>
        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
            x-data="{ offset: 12, loading: false, hasMore: <?php echo e($bestSellers->count() >= 12 ? 'true' : 'false'); ?> }"
        >
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Γαμπριάτικα Κοστούμια 2026</h2>
                <p class="mt-3 text-gray-600">Ανακαλύψτε τη συλλογή μας σε γαμπριάτικα κοστούμια. Κλασικά, σύγχρονα και slim fit κοστούμια γαμπρού σε premium υφάσματα.</p>
            </div>
            <div id="best-sellers-grid" class="mt-8 grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bestSellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div x-show="hasMore" class="mt-8 text-center">
                <button
                    @click="
                        loading = true;
                        fetch('<?php echo e(route('products.loadMore')); ?>?offset=' + offset)
                            .then(r => { if (r.status === 204) { hasMore = false; loading = false; return ''; } return r.text(); })
                            .then(html => {
                                if (html) {
                                    document.getElementById('best-sellers-grid').insertAdjacentHTML('beforeend', html);
                                    offset += 12;
                                }
                                loading = false;
                            })
                    "
                    :disabled="loading"
                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-3 text-sm font-semibold hover:bg-gray-800 transition-colors disabled:opacity-50"
                >
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="loading ? 'Φόρτωση...' : 'Δείτε Περισσότερα'"></span>
                </button>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="relative bg-black overflow-hidden">
        <div class="absolute inset-0">
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/home-bg.jpg','alt' => 'Ενοικίαση γαμπριάτικων κοστουμιών','class' => 'w-full h-full object-cover']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/home-bg.jpg','alt' => 'Ενοικίαση γαμπριάτικων κοστουμιών','class' => 'w-full h-full object-cover']); ?>
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
        </div>
        <div class="absolute inset-0" style="background:rgba(0,0,0,0.65)"></div>
        <div class="relative max-w-4xl mx-auto px-6 py-20 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Ενοικίαση Γαμπριάτικων Κοστουμιών & Σμόκιν</h2>
            <p class="mt-4 text-base sm:text-lg text-white/90 leading-relaxed max-w-2xl mx-auto">
                Premium κοστούμια γαμπρού και σμόκιν σε προσιτές τιμές ενοικίασης. Πλήρης προσαρμογή στα μέτρα σας, δωρεάν μεταποιήσεις και επαγγελματική καθοδήγηση.
            </p>
            <a href="<?php echo e(url('/product-category/rent')); ?>" class="mt-8 inline-block bg-white text-gray-900 px-8 py-3 text-sm font-semibold hover:bg-gray-100 transition-colors">
                Ενοικίαση Κοστουμιών
            </a>
        </div>
    </section>

    
    <section class="bg-[#f5f5f5]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-shirt.jpg','alt' => 'Made to Measure κοστούμι γαμπρού','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-shirt.jpg','alt' => 'Made to Measure κοστούμι γαμπρού','class' => 'w-full h-auto']); ?>
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
                </div>
                <div class="mt-8 lg:mt-0">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Made to Measure</h2>
                    <p class="mt-2 text-lg text-gray-500 italic">The Luxury is Style</p>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Σχεδιάστε το γαμπριάτικο κοστούμι σας από την αρχή. Επιλέξτε ύφασμα, χρώμα, πέτα, κουμπιά και κάθε λεπτομέρεια. Κάθε κοστούμι γαμπρού Made to Measure δημιουργείται αποκλειστικά για εσάς, βάσει των μέτρων σας.
                    </p>
                    <ul class="mt-6 space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-900 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Χειροποίητο φινίρισμα σε κάθε ραφή
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-900 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Δωρεάν δείγματα υφασμάτων στο σπίτι σας
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-900 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Παράδοση σε 4-6 εβδομάδες
                        </li>
                    </ul>
                    <a href="<?php echo e(url('/made-to-measure')); ?>" class="mt-8 inline-block bg-gray-900 text-white px-8 py-3 text-sm font-semibold hover:bg-gray-800 transition-colors">
                        Μάθετε Περισσότερα
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isNotEmpty()): ?>
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Ψωνίστε ανά Κατηγορία</h2>
                <p class="mt-3 text-gray-600">Κοστούμια, σακάκια, πουκάμισα και αξεσουάρ για τον γαμπρό και τον σύγχρονο άνδρα.</p>
            </div>
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('category.show', $category->slug)); ?>"
                       class="group relative flex items-end overflow-hidden bg-gray-200 aspect-[4/3]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->image_path): ?>
                            <img
                                src="<?php echo e(asset('storage/' . $category->image_path)); ?>"
                                alt="<?php echo e($category->name); ?>"
                                loading="lazy"
                                decoding="async"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        <?php else: ?>
                            <?php
                                $firstProduct = $category->products()->active()->with('images')->first();
                                $catImage = $firstProduct?->images->first();
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($catImage): ?>
                                <img
                                    src="<?php echo e(asset('storage/' . $catImage->path_large)); ?>"
                                    alt="<?php echo e($category->name); ?>"
                                    loading="lazy"
                                    decoding="async"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="relative w-full p-4" style="background:linear-gradient(to top,rgba(0,0,0,0.7),transparent)">
                            <span class="text-sm sm:text-base font-semibold text-white"><?php echo e($category->name); ?></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Συχνές Ερωτήσεις για Γαμπριάτικα Κοστούμια</h2>
        </div>
        <div class="mt-8 divide-y divide-gray-200" x-data="{ open: null }">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-4">
                    <button
                        @click="open = open === <?php echo e($index); ?> ? null : <?php echo e($index); ?>"
                        class="flex w-full items-center justify-between text-left"
                        :aria-expanded="open === <?php echo e($index); ?>"
                    >
                        <span class="text-base font-medium text-gray-900 pr-4"><?php echo e($item['q']); ?></span>
                        <svg
                            class="w-5 h-5 flex-shrink-0 text-gray-500 transition-transform duration-200"
                            :class="open === <?php echo e($index); ?> && 'rotate-180'"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div
                        x-show="open === <?php echo e($index); ?>"
                        x-collapse
                        class="mt-3 text-sm text-gray-600 leading-relaxed"
                    >
                        <?php echo e($item['a']); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views/pages/home.blade.php ENDPATH**/ ?>