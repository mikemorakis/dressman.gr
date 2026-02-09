



<div class="flex items-center justify-between px-4 h-16 border-b border-gray-200 flex-shrink-0">
    <h2 class="text-lg font-semibold text-gray-900">Your Cart</h2>
    <button
        type="button"
        data-close-drawer
        class="p-2 -mr-2 rounded-md text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        aria-label="Close cart"
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totals['count'] > 0): ?>
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex-shrink-0">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totals['free_shipping_remaining'] > 0): ?>
            <p class="text-sm text-gray-600">
                Add <strong><?php echo e(format_price($totals['free_shipping_remaining'])); ?></strong> more for free shipping!
            </p>
        <?php else: ?>
            <p class="text-sm text-green-700 font-medium">
                You qualify for free shipping!
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden" role="progressbar" aria-valuenow="<?php echo e($totals['free_shipping_progress']); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Free shipping progress">
            <div class="h-full rounded-full transition-all duration-300 <?php echo e($totals['free_shipping_progress'] >= 100 ? 'bg-green-500' : 'bg-primary-500'); ?>" style="width: <?php echo e($totals['free_shipping_progress']); ?>%"></div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="flex-1 overflow-y-auto px-4 py-4">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->isEmpty()): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <p class="mt-4 text-sm text-gray-500">Your cart is empty</p>
            <a href="<?php echo e(url('/')); ?>" data-close-drawer class="mt-4 inline-block text-sm font-medium text-primary-600 hover:text-primary-800">
                Continue shopping
            </a>
        </div>
    <?php else: ?>
        <ul role="list" class="divide-y divide-gray-200">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="py-4 flex gap-4">
                    
                    <div class="h-20 w-20 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                        <?php $image = $item->product->images->first(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
                            <img src="<?php echo e(asset('storage/' . $image->path_thumb)); ?>" alt="<?php echo e($image->alt_text); ?>" class="h-full w-full object-cover" loading="lazy">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 truncate">
                            <a href="<?php echo e(route('product.show', $item->product->slug)); ?>" data-close-drawer><?php echo e($item->product->name); ?></a>
                        </h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                            <p class="mt-0.5 text-xs text-gray-500"><?php echo e($item->variant->sku); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="mt-1 text-sm font-medium text-gray-900"><?php echo e(format_price($item->unit_price)); ?></p>

                        
                        <div class="mt-2 flex items-center gap-2">
                            <label for="drawer-qty-<?php echo e($item->id); ?>" class="sr-only">Quantity for <?php echo e($item->product->name); ?></label>
                            <select
                                id="drawer-qty-<?php echo e($item->id); ?>"
                                data-update-qty="<?php echo e($item->id); ?>"
                                class="text-sm border-gray-300 rounded-md py-1 pl-2 pr-7 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= min(10, max($item->quantity, 10)); $i++): ?>
                                    <option value="<?php echo e($i); ?>" <?php if($i === $item->quantity): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>

                            <button
                                data-remove="<?php echo e($item->id); ?>"
                                type="button"
                                class="text-sm text-red-600 hover:text-red-800 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded-sm"
                                aria-label="Remove <?php echo e($item->product->name); ?> from cart"
                            >
                                Remove
                            </button>
                        </div>
                    </div>

                    
                    <div class="flex-shrink-0 text-sm font-medium text-gray-900">
                        <?php echo e(format_price($item->line_total)); ?>

                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->isNotEmpty()): ?>
    <div class="border-t border-gray-200 px-4 py-4 flex-shrink-0 space-y-3">
        <div class="flex justify-between text-sm text-gray-600">
            <span>Subtotal</span>
            <span class="font-medium text-gray-900"><?php echo e(format_price($totals['subtotal'])); ?></span>
        </div>
        <div class="flex justify-between text-sm text-gray-600">
            <span>Shipping</span>
            <span class="font-medium text-gray-900">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totals['shipping'] > 0): ?>
                    <?php echo e(format_price($totals['shipping'])); ?>

                <?php else: ?>
                    Free
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>
        <div class="flex justify-between text-base font-semibold text-gray-900 pt-2 border-t border-gray-200">
            <span>Total</span>
            <span><?php echo e(format_price($totals['total'])); ?></span>
        </div>

        <a href="<?php echo e(route('cart')); ?>" data-close-drawer class="btn-primary w-full text-center block">
            View Cart
        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views/partials/cart-drawer-content.blade.php ENDPATH**/ ?>