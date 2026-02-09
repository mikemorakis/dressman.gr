<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost',
        'stock',
        'reserved_stock',
        'low_stock_threshold',
        'has_variants',
        'is_active',
        'is_featured',
        'weight',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'has_variants' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ──── Relationships ────

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsToMany<Label, $this>
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProductVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // ──── Scopes ────

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Product>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true)->whereNotNull('published_at');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Product>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Eager-load variant stock sums to avoid N+1 on listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Product>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function scopeWithAvailableStock(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query
            ->withSum('variants as variants_stock_sum', 'stock')
            ->withSum('variants as variants_reserved_sum', 'reserved_stock');
    }

    // ──── Accessors ────

    /**
     * Available stock. For variant products, aggregate from variants.
     * Uses preloaded withSum attributes when available (via scopeWithAvailableStock)
     * to avoid N+1 on listings.
     */
    public function getAvailableStockAttribute(): int
    {
        if ($this->has_variants) {
            // Use preloaded sums from scopeWithAvailableStock if available
            if (array_key_exists('variants_stock_sum', $this->attributes)) {
                return max(0, (int) ($this->attributes['variants_stock_sum'] ?? 0)
                    - (int) ($this->attributes['variants_reserved_sum'] ?? 0));
            }

            return $this->variants->sum(fn (ProductVariant $v) => $v->available_stock);
        }

        return max(0, $this->stock - $this->reserved_stock);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price !== null && $this->compare_price > $this->price;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->available_stock > 0 && $this->available_stock <= $this->low_stock_threshold;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->available_stock > 0;
    }
}
