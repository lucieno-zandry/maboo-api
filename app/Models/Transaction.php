<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const TYPE_ORDER = 'ORDER';
    const TYPE_SUBSCRIPTION = 'SUBSCRIPTION';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_PENDING = 'PENDING';
    const METHOD_ORANGEMONEY = 'ORANGEMONEY';
    const METHOD_AIRTELMONEY = 'AIRTELMONEY';
    const METHOD_VANILLA_PAY = 'VANILLA_PAY';
    const METHOD_VANILLA_PAY_ORANGEMONEY = 'VANILLA_PAY_ORANGEMONEY';
    const METHOD_VANILLA_PAY_AIRTEL_MONEY = 'VANILLA_PAY_AIRTEL_MONEY';
    const METHOD_VANILLA_PAY_MVOLA = 'VANILLA_PAY_MVOLA';

    protected $fillable = [
        'description',
        'transactionnable_id',
        'status',
        'method',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
