<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    /**
     * Always include computed fields in API response
     */
    protected $appends = [
        'final_price',
        'stock_quantity'
    ];

    protected $fillable = [
        'category_id',
        'brand_id',
        'product_name',
        'slug',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'price',
        'discount_percent',
        'quantity',
        'stock_quantity',
        'min_stock_alert',
        'weight',
        'image',
        'gallery',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'gallery' => 'array',
        'status' => 'integer',
        'is_featured' => 'boolean',

        'price' => 'float',
        'cost_price' => 'float',
        'discount_percent' => 'integer',

        'quantity' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'weight' => 'float',
    ];

    /**
     * AUTO SLUG GENERATION
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->product_name) . '-' . uniqid();
            }
        });
    }

    /**
     * FINAL PRICE CALCULATION (SAFE)
     */
    public function getFinalPriceAttribute()
    {
        $discount = (int)($this->discount_percent ?? 0);
        $price = (float)($this->price ?? 0);

        if ($discount > 0) {
            return round($price - ($price * $discount / 100), 2);
        }

        return $price;
    }

    public function getStockQuantityAttribute()
    {
        return $this->quantity ?? 0;
    }

    /**
     * RELATIONS
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
