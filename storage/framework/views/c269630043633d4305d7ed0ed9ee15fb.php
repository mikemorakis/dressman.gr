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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => 'Shopping Cart — PeShop','description' => 'Review your shopping cart and proceed to checkout.','noindex' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Shopping Cart — PeShop','description' => 'Review your shopping cart and proceed to checkout.','noindex' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Cart'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Cart'],
        ])]); ?>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->isEmpty()): ?>
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <h2 class="mt-4 text-lg font-semibold text-gray-900">Your cart is empty</h2>
                <p class="mt-2 text-sm text-gray-500">Looks like you haven't added any products yet.</p>
                <a href="<?php echo e(url('/')); ?>" class="mt-6 inline-block btn-primary">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totals['free_shipping_remaining'] > 0): ?>
                    <p class="text-sm text-gray-600">
                        Add <strong><?php echo e(format_price($totals['free_shipping_remaining'])); ?></strong> more for free shipping!
                    </p>
                <?php else: ?>
                    <p class="text-sm text-green-700 font-medium">
                        You qualify for free shipping!
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="mt-2 h-2.5 bg-gray-200 rounded-full overflow-hidden" role="progressbar" aria-valuenow="<?php echo e($totals['free_shipping_progress']); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Free shipping progress">
                    <div class="h-full rounded-full transition-all duration-300 <?php echo e($totals['free_shipping_progress'] >= 100 ? 'bg-green-500' : 'bg-primary-500'); ?>" style="width: <?php echo e($totals['free_shipping_progress']); ?>%"></div>
                </div>
            </div>

            <div class="mt-8 lg:grid lg:grid-cols-12 lg:gap-x-8">
                
                <section class="lg:col-span-8" aria-label="Cart items">
                    <ul role="list" class="divide-y divide-gray-200 border-t border-b border-gray-200">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="py-6 flex gap-4 sm:gap-6">
                                
                                <div class="h-24 w-24 sm:h-32 sm:w-32 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                    <?php $image = $item->product->images->first(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
                                        <img src="<?php echo e(asset('storage/' . $image->path_medium)); ?>" alt="<?php echo e($image->alt_text); ?>" class="h-full w-full object-cover" loading="lazy">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="flex-1 min-w-0 flex flex-col">
                                    <div class="flex justify-between">
                                        <div>
                                            <h3 class="text-base font-medium text-gray-900">
                                                <a href="<?php echo e(route('product.show', $item->product->slug)); ?>" class="hover:text-primary-600"><?php echo e($item->product->name); ?></a>
                                            </h3>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                                                <p class="mt-0.5 text-sm text-gray-500">SKU: <?php echo e($item->variant->sku); ?></p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <p class="mt-1 text-sm text-gray-600"><?php echo e(format_price($item->unit_price)); ?> each</p>
                                        </div>
                                        <p class="text-base font-medium text-gray-900 flex-shrink-0 ml-4">
                                            <?php echo e(format_price($item->line_total)); ?>

                                        </p>
                                    </div>

                                    
                                    <div class="mt-auto pt-4 flex items-center gap-4">
                                        <form action="<?php echo e(route('cart.update', $item)); ?>" method="POST" class="flex items-center gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <label for="cart-qty-<?php echo e($item->id); ?>" class="text-sm text-gray-600">Qty:</label>
                                            <select
                                                id="cart-qty-<?php echo e($item->id); ?>"
                                                name="quantity"
                                                onchange="this.form.submit()"
                                                class="text-sm border-gray-300 rounded-md py-1.5 pl-3 pr-8 focus:border-primary-500 focus:ring-primary-500"
                                            >
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= min(10, max($item->quantity, 10)); $i++): ?>
                                                    <option value="<?php echo e($i); ?>" <?php if($i === $item->quantity): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </form>

                                        <form action="<?php echo e(route('cart.remove', $item)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button
                                                type="submit"
                                                class="text-sm text-red-600 hover:text-red-800 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded-sm"
                                                aria-label="Remove <?php echo e($item->product->name); ?> from cart"
                                            >
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>

                    <div class="mt-4 flex justify-between items-center">
                        <a href="<?php echo e(url('/')); ?>" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                            Continue Shopping
                        </a>
                        <form action="<?php echo e(route('cart.clear')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button
                                type="submit"
                                onclick="return confirm('Are you sure you want to empty your cart?')"
                                class="text-sm text-gray-500 hover:text-gray-700 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 rounded-sm"
                            >
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </section>

                
                <section class="mt-8 lg:mt-0 lg:col-span-4" aria-label="Order summary">
                    <div class="bg-gray-50 rounded-lg p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>

                        <dl class="mt-6 space-y-4">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Subtotal (<?php echo e($totals['count']); ?> <?php echo e(Str::plural('item', $totals['count'])); ?>)</dt>
                                <dd class="font-medium text-gray-900"><?php echo e(format_price($totals['subtotal'])); ?></dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Shipping</dt>
                                <dd class="font-medium text-gray-900">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totals['shipping'] > 0): ?>
                                        <?php echo e(format_price($totals['shipping'])); ?>

                                    <?php else: ?>
                                        <span class="text-green-600">Free</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </dd>
                            </div>
                            <div class="flex justify-between text-base font-semibold text-gray-900 pt-4 border-t border-gray-200">
                                <dt>Total</dt>
                                <dd><?php echo e(format_price($totals['total'])); ?></dd>
                            </div>
                        </dl>

                        <p class="mt-1 text-xs text-gray-500">Including VAT</p>

                        <a href="<?php echo e(route('checkout')); ?>" class="mt-6 btn-primary w-full text-center block">
                            Proceed to Checkout
                        </a>
                    </div>
                </section>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\pages\cart.blade.php ENDPATH**/ ?>