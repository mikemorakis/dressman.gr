<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 */
class Order extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->guest_token) {
                $order->guest_token = bin2hex(random_bytes(32));
            }
        });
    }

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'guest_token',
        'email',
        'phone',
        'billing_address',
        'shipping_address',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'shipping_amount',
        'shipping_method_code',
        'shipping_label',
        'shipping_zone_code',
        'total',
        'currency',
        'prices_include_vat',
        'notes',
        'confirmation_sent_at',
        'paid_at',
        'shipped_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'billing_address' => 'array',
            'shipping_address' => 'array',
            'subtotal' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'prices_include_vat' => 'boolean',
            'confirmation_sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<OrderStatusHistory, $this>
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * @return HasMany<StockReservation, $this>
     */
    public function stockReservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }

    /**
     * Generate a unique order number: PREFIX-YYYYMMDD-XXXX.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = config('shop.order_prefix', 'PE');
        $date = now()->format('Ymd');

        do {
            $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $number = "{$prefix}-{$date}-{$random}";
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Mark order as paid (idempotent — skips if already paid).
     */
    public function markAsPaid(string $paymentIntentId): void
    {
        if ($this->payment_status === PaymentStatus::Paid) {
            return;
        }

        $oldStatus = $this->status;

        $this->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => OrderStatus::Paid,
            'stripe_payment_intent_id' => $paymentIntentId,
            'paid_at' => now(),
        ]);

        $this->statusHistory()->create([
            'from_status' => $oldStatus->value,
            'to_status' => OrderStatus::Paid->value,
            'notes' => 'Payment confirmed via Stripe webhook',
        ]);
    }

    /**
     * Transition to a new status with history tracking.
     */
    public function transitionTo(OrderStatus $newStatus, ?string $notes = null): void
    {
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        $this->statusHistory()->create([
            'from_status' => $oldStatus->value,
            'to_status' => $newStatus->value,
            'notes' => $notes,
        ]);
    }
}
