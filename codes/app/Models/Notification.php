<?php

namespace App\Models;

use App\Models\Scopes\Notification\WhereIsAuthScope;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    public function scopeUnread(Builder $builder)
    {
        return $builder->where('read_at', null);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new WhereIsAuthScope);
    }
}
