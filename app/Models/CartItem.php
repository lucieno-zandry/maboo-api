<?php

namespace App\Models;

use App\Models\Scopes\CartItem\WithProductColorScope;
use App\Models\Scopes\CartItem\WithProductScope;
use App\Models\Scopes\CartItem\WithProductVariantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'product_id',
        'quantity',
        'product_variant_id',
        'product_color_id',
        'user_id',
        'subtotal',
        'ordered_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_color()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WithProductScope);
        static::addGlobalScope(new WithProductColorScope);
        static::addGlobalScope(new WithProductVariantScope);
    }
}
