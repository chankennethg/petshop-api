<?php

namespace App\Models\Traits;

use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait Sortable
{
    /**
     * Sorting trait
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function scopeSort(Builder $query, Request $request): Builder
    {
        // Get all sortable columns
        $sortable = data_get($this, 'sortable', []);

        // Get the column to sort
        $column = $request->get('sortBy', 'id');

        // Get the direction of which to sort
        $direction = ($request->get('desc', 'true') === 'true') ? 'desc' : 'asc';

        if (in_array($column, $sortable)) {
            return $query->orderBy($column, $direction);
        }

        // If there are no sorting, return query
        return $query;
    }
}
