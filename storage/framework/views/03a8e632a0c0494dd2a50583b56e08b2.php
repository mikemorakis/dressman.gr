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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => 'Order '.e($order->order_number).' — PeShop','description' => 'Track your order status.','noindex' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order '.e($order->order_number).' — PeShop','description' => 'Track your order status.','noindex' => true]); ?>
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

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order <?php echo e($order->order_number); ?></h1>
                <p class="mt-1 text-sm text-gray-500">Placed on <?php echo e($order->created_at->format('M j, Y')); ?></p>
            </div>

            <?php
                $badgeColors = match($order->status) {
                    \App\Enums\OrderStatus::Pending => 'bg-yellow-100 text-yellow-800',
                    \App\Enums\OrderStatus::Paid => 'bg-blue-100 text-blue-800',
                    \App\Enums\OrderStatus::Processing => 'bg-indigo-100 text-indigo-800',
                    \App\Enums\OrderStatus::Shipped => 'bg-purple-100 text-purple-800',
                    \App\Enums\OrderStatus::Delivered => 'bg-green-100 text-green-800',
                    \App\Enums\OrderStatus::Cancelled => 'bg-red-100 text-red-800',
                };
            ?>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($badgeColors); ?>">
                <?php echo e($order->status->label()); ?>

            </span>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->statusHistory->isNotEmpty()): ?>
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Timeline</h2>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <div class="relative pb-8">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                                        <span class="absolute left-3 top-3 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="relative flex items-start gap-x-3">
                                        <div class="relative">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 ring-4 ring-white">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?php echo e(ucfirst($entry->to_status)); ?>

                                            </p>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->notes): ?>
                                                <p class="mt-0.5 text-sm text-gray-500"><?php echo e($entry->notes); ?></p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <p class="mt-0.5 text-xs text-gray-400">
                                                <?php echo e($entry->created_at->format('M j, Y \a\t H:i')); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Items</h2>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?php echo e($item->product_name); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_label): ?>
                                        <span class="block text-xs text-gray-500"><?php echo e($item->variant_label); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center"><?php echo e($item->quantity); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right"><?php echo e(format_price($item->line_total)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="mb-8 bg-gray-50 rounded-lg p-6">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Subtotal</dt>
                    <dd class="font-medium text-gray-900"><?php echo e(format_price($order->subtotal)); ?></dd>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prices_include_vat): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Includes VAT (<?php echo e(number_format((float) $order->vat_rate, 0)); ?>%)</dt>
                        <dd class="text-gray-400"><?php echo e(format_price($order->vat_amount)); ?></dd>
                    </div>
                <?php else: ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">VAT (<?php echo e(number_format((float) $order->vat_rate, 0)); ?>%)</dt>
                        <dd class="font-medium text-gray-900"><?php echo e(format_price($order->vat_amount)); ?></dd>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex justify-between">
                    <dt class="text-gray-600">Shipping</dt>
                    <dd class="font-medium text-gray-900">
                        <?php echo e((float) $order->shipping_amount > 0 ? format_price($order->shipping_amount) : 'Free'); ?>

                    </dd>
                </div>

                <div class="flex justify-between border-t border-gray-200 pt-2">
                    <dt class="text-base font-semibold text-gray-900">Total</dt>
                    <dd class="text-base font-semibold text-gray-900"><?php echo e(format_price($order->total)); ?></dd>
                </div>
            </dl>
        </div>

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Shipping Address</h2>
            <address class="not-italic text-sm text-gray-600 leading-relaxed">
                <?php echo e($order->shipping_address['first_name']); ?> <?php echo e($order->shipping_address['last_name']); ?><br>
                <?php echo e($order->shipping_address['address']); ?><br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($order->shipping_address['address2'])): ?>
                    <?php echo e($order->shipping_address['address2']); ?><br>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($order->shipping_address['postal_code']); ?> <?php echo e($order->shipping_address['city']); ?><br>
                <?php echo e($order->shipping_address['country']); ?>

            </address>
        </div>

        
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
            <?php
                $email = $order->email;
                $parts = explode('@', $email);
                $local = $parts[0];
                $masked = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 2)) . '@' . $parts[1];
            ?>
            <p class="text-sm text-gray-600"><?php echo e($masked); ?></p>
        </div>

        
        <div class="text-center">
            <a href="<?php echo e(route('home')); ?>" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                Continue Shopping
            </a>
        </div>
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
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\pages\order\status.blade.php ENDPATH**/ ?>