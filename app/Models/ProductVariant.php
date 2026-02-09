<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'reserved_stock',
        'signature',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ──── Relationships ────

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsToMany<AttributeValue, $this>
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class);
    }

    // ──── Accessors ────

    /**
     * Effective price: variant price if set, otherwise parent product price.
     */
    public function getEffectivePriceAttribute(): string
    {
        /** @var string */
        return $this->price ?? $this->product->price;
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    // ──── Signature ────

    /**
     * Generate a deterministic signature from attribute value IDs.
     * Used to enforce uniqueness of variants per product.
     *
     * @param  array<int>  $attributeValueIds
     */
    public static function generateSignature(array $attributeValueIds): string
    {
        $sorted = $attributeValueIds;
        sort($sorted, SORT_NUMERIC);

        return sha1(implode('-', $sorted));
    }
}
