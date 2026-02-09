<?php
    /** @var \App\Models\Product $product */
    /** @var array $variantData */
    /** @var array $jsonLd */

    $hasVariants = $product->has_variants && count($variantData) > 0;
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => $product->meta_title ?: $product->name . ' — PeShop','description' => $product->meta_description ?: $product->short_description,'jsonLd' => $jsonLd]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->meta_title ?: $product->name . ' — PeShop'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->meta_description ?: $product->short_description),'jsonLd' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jsonLd)]); ?>
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
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('breadcrumb', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>

    <div
        x-data="{
            variants: <?php echo e(Js::from($variantData)); ?>,
            selected: {},
            stickyVisible: false,
            adding: false,
            added: false,

            async addToCart(productId, variantId = null) {
                this.adding = true;
                this.added = false;
                try {
                    const res = await fetch('<?php echo e(route("cart.add")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: 1 }),
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.added = true;
                    window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: { count: data.count } }));
                    window.dispatchEvent(new CustomEvent('cart-open', { detail: { html: data.drawer_html } }));
                    setTimeout(() => { this.added = false; }, 2000);
                } finally {
                    this.adding = false;
                }
            },

            init() {
                const sentinel = this.$refs.addToCart;
                if (sentinel && 'IntersectionObserver' in window) {
                    new IntersectionObserver(([e]) => {
                        this.stickyVisible = !e.isIntersecting;
                    }).observe(sentinel);
                } else if (sentinel) {
                    this.stickyVisible = true;
                }
            },

            get attributeNames() {
                const names = new Set();
                this.variants.forEach(v => Object.keys(v.attributes).forEach(k => names.add(k)));
                return [...names];
            },

            attributeValues(name) {
                const seen = new Set();
                return this.variants.reduce((acc, v) => {
                    const val = v.attributes[name];
                    if (val && !seen.has(val)) { seen.add(val); acc.push(val); }
                    return acc;
                }, []);
            },

            get currentVariant() {
                const names = this.attributeNames;
                if (Object.keys(this.selected).length < names.length) return null;
                return this.variants.find(v => names.every(n => v.attributes[n] === this.selected[n])) || null;
            },

            isAvailable(name, value) {
                const test = { ...this.selected, [name]: value };
                return this.variants.some(v =>
                    Object.entries(test).every(([k, val]) => v.attributes[k] === val)
                );
            }
        }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
                
                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->isNotEmpty()): ?>
                        <div class="space-y-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-gray-100">
                                    <img
                                        src="<?php echo e(asset('storage/' . $image->path_large)); ?>"
                                        alt="<?php echo e($image->alt_text ?: $product->name); ?>"
                                        width="<?php echo e($image->width ?: 1200); ?>"
                                        height="<?php echo e($image->height ?: 1200); ?>"
                                        <?php if($index === 0): ?> fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>
                                        decoding="async"
                                        class="w-full h-auto"
                                    >
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            <svg class="h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                            </svg>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="mt-8 lg:mt-0 lg:sticky lg:top-[150px] lg:self-start">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->labels->isNotEmpty()): ?>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-white"
                                      style="background-color: <?php echo e($label->color); ?>">
                                    <?php echo e($label->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($product->name); ?></h1>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->brand): ?>
                        <p class="mt-1 text-sm text-gray-500"><?php echo e($product->brand->name); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mt-4 flex items-baseline gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                            <span class="text-2xl font-bold text-gray-900"
                                  x-text="currentVariant ? currentVariant.price_formatted : '<?php echo e(format_price($product->price)); ?>'">
                                <?php echo e(format_price($product->price)); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-2xl font-bold text-gray-900"><?php echo e(format_price($product->price)); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->is_on_sale): ?>
                                <span class="text-base text-gray-500 line-through"><?php echo e(format_price($product->compare_price)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->short_description): ?>
                        <p class="mt-4 text-gray-600"><?php echo e($product->short_description); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                        <div class="mt-6 space-y-5">
                            <template x-for="name in attributeNames" :key="name">
                                <fieldset>
                                    <legend class="text-sm font-medium text-gray-900 uppercase" x-text="name"></legend>

                                    
                                    <template x-if="attributeValues(name).length > 4">
                                        <select
                                            x-model="selected[name]"
                                            class="mt-2 block w-full border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5"
                                        >
                                            <option value="" x-text="'Select ' + name.toLowerCase()"></option>
                                            <template x-for="value in attributeValues(name)" :key="value">
                                                <option :value="value" :disabled="!isAvailable(name, value)" x-text="value"></option>
                                            </template>
                                        </select>
                                    </template>

                                    
                                    <template x-if="attributeValues(name).length <= 4">
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <template x-for="value in attributeValues(name)" :key="value">
                                                <button
                                                    @click="selected[name] = value"
                                                    :class="{
                                                        'ring-2 ring-primary-500 bg-primary-50 text-primary-700': selected[name] === value,
                                                        'ring-1 ring-gray-300 text-gray-700 hover:ring-gray-400': selected[name] !== value,
                                                        'opacity-40 cursor-not-allowed': !isAvailable(name, value)
                                                    }"
                                                    :disabled="!isAvailable(name, value)"
                                                    :aria-pressed="(selected[name] === value).toString()"
                                                    class="min-w-[2.75rem] min-h-[2.75rem] px-4 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                                                    type="button"
                                                    x-text="value"
                                                ></button>
                                            </template>
                                        </div>
                                    </template>
                                </fieldset>
                            </template>
                        </div>

                        
                        <div class="mt-4">
                            <template x-if="currentVariant">
                                <p class="text-sm font-medium"
                                   :class="currentVariant.in_stock ? 'text-green-600' : 'text-red-600'"
                                   x-text="currentVariant.in_stock ? 'In stock' : 'Out of stock'">
                                </p>
                            </template>
                        </div>
                    <?php else: ?>
                        
                        <div class="mt-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->is_in_stock): ?>
                                <p class="text-sm font-medium text-green-600">In stock</p>
                            <?php else: ?>
                                <p class="text-sm font-medium text-red-600">Out of stock</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
                        <div class="mt-6 prose prose-sm max-w-none text-gray-700">
                            <?php echo clean_html($product->description); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div x-ref="addToCart" class="mt-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                            <button
                                class="btn-primary w-full py-3 text-base"
                                :disabled="!currentVariant || !currentVariant.in_stock || adding"
                                @click="addToCart(<?php echo e($product->id); ?>, currentVariant?.id)"
                                type="button"
                            >
                                <span x-show="adding" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Adding...
                                </span>
                                <span x-show="!adding && added">Added to cart</span>
                                <span x-show="!adding && !added" x-text="!currentVariant
                                    ? 'Select options'
                                    : (!currentVariant.in_stock ? 'Out of stock' : 'Add to Cart')">
                                    Select options
                                </span>
                            </button>
                        <?php else: ?>
                            <button
                                class="btn-primary w-full py-3 text-base"
                                <?php if(!$product->is_in_stock): echo 'disabled'; endif; ?>
                                :disabled="adding"
                                @click="addToCart(<?php echo e($product->id); ?>)"
                                type="button"
                            >
                                <span x-show="adding" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Adding...
                                </span>
                                <span x-show="!adding && added">Added to cart</span>
                                <span x-show="!adding && !added"><?php echo e($product->is_in_stock ? 'Add to Cart' : 'Out of Stock'); ?></span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <p class="mt-4 text-xs text-gray-500">
                        SKU:
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                            <span x-text="currentVariant ? currentVariant.sku : '<?php echo e(e($product->sku)); ?>'"><?php echo e($product->sku); ?></span>
                        <?php else: ?>
                            <?php echo e($product->sku); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
            </div>

        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentlyViewed->isNotEmpty()): ?>
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-xl font-bold text-gray-900">Recently Viewed</h2>
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentlyViewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $recentProduct]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recentProduct)]); ?>
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
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($similarProducts->isNotEmpty()): ?>
            <section class="bg-[#f5f5f5] <?php echo e($recentlyViewed->isEmpty() ? 'mt-12' : ''); ?>">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h2 class="text-xl font-bold text-gray-900">Similar Products</h2>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $similarProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $similarProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $similarProduct]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($similarProduct)]); ?>
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
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div
            x-show="stickyVisible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            x-cloak
            class="fixed bottom-0 inset-x-0 z-20 lg:hidden bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.08)]"
            style="padding-bottom: env(safe-area-inset-bottom, 0px)"
        >
            <div class="flex items-center justify-between gap-4 px-4 py-3">
                <div class="flex flex-col min-w-0">
                    <span class="text-lg font-bold text-gray-900 truncate"
                          x-text="currentVariant ? currentVariant.price_formatted : '<?php echo e(format_price($product->price)); ?>'">
                        <?php echo e(format_price($product->price)); ?>

                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                        <span class="text-xs"
                              :class="currentVariant?.in_stock ? 'text-green-600' : 'text-gray-500'"
                              x-text="currentVariant ? (currentVariant.in_stock ? 'In stock' : 'Out of stock') : 'Select options'">
                            Select options
                        </span>
                    <?php else: ?>
                        <span class="text-xs <?php echo e($product->is_in_stock ? 'text-green-600' : 'text-red-600'); ?>">
                            <?php echo e($product->is_in_stock ? 'In stock' : 'Out of stock'); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVariants): ?>
                    <button
                        class="btn-primary text-sm px-6 py-2.5 flex-shrink-0"
                        :disabled="!currentVariant || !currentVariant.in_stock || adding"
                        @click="addToCart(<?php echo e($product->id); ?>, currentVariant?.id)"
                        type="button"
                    >
                        <span x-show="adding">Adding...</span>
                        <span x-show="!adding && added">Added</span>
                        <span x-show="!adding && !added">Add to Cart</span>
                    </button>
                <?php else: ?>
                    <button
                        class="btn-primary text-sm px-6 py-2.5 flex-shrink-0"
                        <?php if(!$product->is_in_stock): echo 'disabled'; endif; ?>
                        :disabled="adding"
                        @click="addToCart(<?php echo e($product->id); ?>)"
                        type="button"
                    >
                        <span x-show="adding">Adding...</span>
                        <span x-show="!adding && added">Added</span>
                        <span x-show="!adding && !added"><?php echo e($product->is_in_stock ? 'Add to Cart' : 'Sold Out'); ?></span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
    </div>
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
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views/pages/product/show.blade.php ENDPATH**/ ?>