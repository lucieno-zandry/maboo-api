<?php

namespace App\Models;

use App\Enums\OrderStatuses;
use App\Models\Scopes\SoldItem\WithBuyerScope;
use App\Models\Scopes\SoldItem\WithOrderScope;
use App\Models\Scopes\SoldItem\WithProductScope;
use App\Models\Scopes\SoldItem\WithProductVariantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'merchant_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'subtotal',
        'order_id',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeStatusPending(Builder $builder): Builder
    {
        return $builder->whereHas('order', function ($query) {
            $query->where('order_status', OrderStatuses::PROCESSING->value);
        });
    }

    protected static function booted()
    {
        self::addGlobalScopes([
            new WithBuyerScope,
            new WithProductScope,
            new WithProductVariantScope,
            new WithOrderScope,
        ]);
    }
}