<?php

namespace App\Models;

use App\Models\Scopes\Order\WithOrderItemsScope;
use App\Models\Scopes\Order\WithTransactionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'total_price',
        'transaction_id',
        'user_id',
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WithOrderItemsScope);
        static::addGlobalScope(new WithTransactionScope);
    }
}