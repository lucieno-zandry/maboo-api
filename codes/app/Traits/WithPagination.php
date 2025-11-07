<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait WithPagination
{
    public function scopeWithPagination(Builder $query)
    {
        if (request()->has('limit')) {
            $query->limit(request('limit'));
        }

        if (request()->has('offset')) {
            $query->offset(request('offset'));
        }

        return $query;
    }
}
