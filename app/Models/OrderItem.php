<?php

namespace App\Models;

use App\Actions\AdminActions;
use App\Enums\OrderStatuses;
use App\Models\Scopes\OrderItem\WithCartItemScope;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cart_item_id',
        'user_id',
        'merchant_id',
        'status',
    ];

    public function cart_item()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function scopeAdminItems(Builder $builder): Builder
    {
        $ids = (new AdminActions)->adminIds();
        return $builder->whereIn('merchant_id', $ids);
    }

    public function scopeWhereStatus(Builder $builder, OrderStatuses $status): Builder
    {
        return $builder->where('status', $status->value);
    }

    public function order_status_histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WithCartItemScope);
    }
}