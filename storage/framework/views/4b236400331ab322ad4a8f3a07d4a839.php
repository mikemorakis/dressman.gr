<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #374151; background-color: #f3f4f6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <div style="background-color: #ffffff; border-radius: 8px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h1 style="margin: 0 0 8px; font-size: 24px; color: #111827;">Thank you for your order!</h1>

            <p style="margin: 0 0 24px; color: #6b7280;">
                Hi <?php echo e($order->shipping_address['first_name']); ?>, your order has been confirmed.
            </p>

            <div style="padding: 16px; background-color: #f9fafb; border-radius: 6px; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 14px; color: #6b7280;">Order Number</p>
                <p style="margin: 4px 0 0; font-size: 18px; font-weight: 600; color: #111827;"><?php echo e($order->order_number); ?></p>
            </div>

            <h2 style="margin: 0 0 12px; font-size: 16px; color: #111827;">Items</h2>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th style="text-align: left; padding: 8px 0; font-size: 13px; color: #6b7280; font-weight: 500;">Product</th>
                        <th style="text-align: center; padding: 8px 0; font-size: 13px; color: #6b7280; font-weight: 500;">Qty</th>
                        <th style="text-align: right; padding: 8px 0; font-size: 13px; color: #6b7280; font-weight: 500;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 10px 0; font-size: 14px;">
                                <?php echo e($item->product_name); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_label): ?>
                                    <br><span style="color: #9ca3af; font-size: 12px;"><?php echo e($item->variant_label); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="text-align: center; padding: 10px 0; font-size: 14px;"><?php echo e($item->quantity); ?></td>
                            <td style="text-align: right; padding: 10px 0; font-size: 14px;"><?php echo e(format_price($item->line_total)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #e5e7eb; padding-top: 12px;">
                <tr>
                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Subtotal</td>
                    <td style="padding: 6px 0; font-size: 14px; text-align: right;"><?php echo e(format_price($order->subtotal)); ?></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->prices_include_vat): ?>
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; color: #9ca3af;">Includes VAT (<?php echo e(number_format((float) $order->vat_rate, 0)); ?>%)</td>
                        <td style="padding: 6px 0; font-size: 13px; color: #9ca3af; text-align: right;"><?php echo e(format_price($order->vat_amount)); ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">VAT (<?php echo e(number_format((float) $order->vat_rate, 0)); ?>%)</td>
                        <td style="padding: 6px 0; font-size: 14px; text-align: right;"><?php echo e(format_price($order->vat_amount)); ?></td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <tr>
                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Shipping</td>
                    <td style="padding: 6px 0; font-size: 14px; text-align: right;">
                        <?php echo e((float) $order->shipping_amount > 0 ? format_price($order->shipping_amount) : 'Free'); ?>

                    </td>
                </tr>
                <tr style="border-top: 2px solid #e5e7eb;">
                    <td style="padding: 12px 0 0; font-size: 18px; font-weight: 600; color: #111827;">Total</td>
                    <td style="padding: 12px 0 0; font-size: 18px; font-weight: 600; color: #111827; text-align: right;"><?php echo e(format_price($order->total)); ?></td>
                </tr>
            </table>

            <h2 style="margin: 24px 0 8px; font-size: 16px; color: #111827;">Shipping Address</h2>
            <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.8;">
                <?php echo e($order->shipping_address['first_name']); ?> <?php echo e($order->shipping_address['last_name']); ?><br>
                <?php echo e($order->shipping_address['address']); ?><br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($order->shipping_address['address2'])): ?>
                    <?php echo e($order->shipping_address['address2']); ?><br>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($order->shipping_address['postal_code']); ?> <?php echo e($order->shipping_address['city']); ?><br>
                <?php echo e($order->shipping_address['country']); ?>

            </p>

            <div style="margin-top: 24px; text-align: center;">
                <a href="<?php echo e(route('order.guest.show', ['orderNumber' => $order->order_number, 'token' => $order->guest_token])); ?>"
                   style="display: inline-block; padding: 12px 24px; background-color: #111827; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                    Track Your Order
                </a>
            </div>

            <div style="margin-top: 24px; padding: 16px; background-color: #f0fdf4; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #166534;">
                    We'll send you another email when your order ships. You can also track your order using the button above. If you have any questions, please reply to this email.
                </p>
            </div>
        </div>

        <p style="margin: 24px 0 0; text-align: center; font-size: 12px; color: #9ca3af;">
            Thank you for shopping with PeShop!
        </p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\emails\order-confirmation.blade.php ENDPATH**/ ?>