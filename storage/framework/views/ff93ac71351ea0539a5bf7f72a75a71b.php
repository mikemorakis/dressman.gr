<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
        
        <div class="max-w-2xl mx-auto">
            <label for="search-input" class="sr-only">Search products</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="query"
                    id="search-input"
                    type="search"
                    autocomplete="off"
                    placeholder="Search products..."
                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500"
                    autofocus
                >
                <div wire:loading wire:target="query" class="absolute inset-y-0 right-0 pr-3 flex items-center" aria-hidden="true">
                    <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" x-data="{ showFilters: false }">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <p class="text-sm text-gray-500" aria-live="polite">
                <span wire:loading.remove wire:target="query,categories,brands,minPrice,maxPrice,sort">
                    <?php echo e($products->total()); ?> <?php echo e(Str::plural('result', $products->total())); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($query): ?>
                        for "<strong><?php echo e(e($query)); ?></strong>"
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span wire:loading wire:target="query,categories,brands,minPrice,maxPrice,sort" class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Searching...
                </span>
            </p>

            <div class="flex items-center gap-3">
                
                <button
                    x-ref="filterToggle"
                    @click="showFilters = !showFilters"
                    class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    :aria-expanded="showFilters.toString()"
                    aria-controls="filter-panel"
                    type="button"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    Filters
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActiveFilters): ?>
                        <span class="inline-flex items-center justify-center h-4 min-w-[1rem] px-1 text-[10px] font-bold leading-none text-white bg-primary-600 rounded-full" aria-hidden="true">&bull;</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>

                
                <label for="sort-select" class="sr-only">Sort by</label>
                <select
                    wire:model.live="sort"
                    id="sort-select"
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="relevance">Relevance</option>
                    <option value="newest">Newest</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name">Name: A–Z</option>
                </select>
            </div>
        </div>

        <div class="lg:grid lg:grid-cols-4 lg:gap-x-8">
            
            <aside
                id="filter-panel"
                x-trap.noscroll="showFilters && window.innerWidth < 1024"
                @keydown.escape.window="if (showFilters && window.innerWidth < 1024) { showFilters = false; $refs.filterToggle.focus(); }"
                :class="showFilters ? '' : 'hidden lg:block'"
                class="mb-8 lg:mb-0 space-y-6"
                role="region"
                aria-label="Product filters"
            >
                
                <div class="flex items-center justify-between lg:hidden">
                    <span class="text-sm font-semibold text-gray-900">Filters</span>
                    <button
                        @click="showFilters = false; $refs.filterToggle.focus()"
                        class="p-1 -m-1 text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded"
                        type="button"
                        aria-label="Close filters"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allCategories->isNotEmpty()): ?>
                    <fieldset>
                        <legend class="text-sm font-semibold text-gray-900">Categories</legend>
                        <div class="mt-2 space-y-1.5 max-h-48 overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input
                                        wire:model.live="categories"
                                        type="checkbox"
                                        value="<?php echo e($cat->id); ?>"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >
                                    <?php echo e($cat->name); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </fieldset>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allBrands->isNotEmpty()): ?>
                    <fieldset>
                        <legend class="text-sm font-semibold text-gray-900">Brands</legend>
                        <div class="mt-2 space-y-1.5 max-h-48 overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input
                                        wire:model.live="brands"
                                        type="checkbox"
                                        value="<?php echo e($brand->id); ?>"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >
                                    <?php echo e($brand->name); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </fieldset>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900">Price range</legend>
                    <div class="mt-2 flex items-center gap-2">
                        <label for="min-price" class="sr-only">Minimum price</label>
                        <input
                            wire:model.live.debounce.500ms="minPrice"
                            id="min-price"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Min"
                            class="w-full text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500"
                        >
                        <span class="text-gray-400" aria-hidden="true">–</span>
                        <label for="max-price" class="sr-only">Maximum price</label>
                        <input
                            wire:model.live.debounce.500ms="maxPrice"
                            id="max-price"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Max"
                            class="w-full text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500"
                        >
                    </div>
                </fieldset>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActiveFilters): ?>
                    <button
                        wire:click="clearFilters"
                        class="text-sm text-primary-600 hover:text-primary-800 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm"
                        type="button"
                    >
                        Clear all filters
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </aside>

            
            <div class="lg:col-span-3">
                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="query,categories,brands,minPrice,maxPrice,sort,gotoPage,previousPage,nextPage" class="transition-opacity">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isNotEmpty()): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

                        <div class="mt-8">
                            <?php echo e($products->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <h3 class="mt-4 text-sm font-semibold text-gray-900">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($query): ?>
                                    Try a different search term or adjust your filters.
                                <?php else: ?>
                                    Adjust your filters or browse our categories.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\livewire\product-search.blade.php ENDPATH**/ ?>