<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function scopeSearchByTitle(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    public function scopeFilterByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }
}
