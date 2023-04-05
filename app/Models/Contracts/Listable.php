<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 */
interface Listable
{
    /**
     * @param Builder<TModelClass> $query
     * @param string $column
     * @param string $direction
     * @return Builder<TModelClass>
     */
    public function scopeSort(Builder $query, string $column = 'created_at', string $direction = 'desc'): Builder;

    /**
     * @param Builder<TModelClass> $query
     * @param array<string, mixed> $filters
     * @return Builder<TModelClass>
     */
    public function scopeFilter(Builder $query, array $filters): Builder;

    /**
     * @param Builder<TModelClass> $query
     * @param array<string, mixed> $filters
     * @return Builder<TModelClass>
     */
    public function scopeFilterLike(Builder $query, array $filters): Builder;

    /**
     * @param Builder<TModelClass> $query
     * @param array<string, mixed> $filters
     * @return Builder<TModelClass>
     */
    public function scopeFilterExact(Builder $query, array $filters): Builder;

    /**
     * @return array<int, string>
     */
    public function getSortableColumns(): array;

    /**
     * @return array<int, string>
     */
    public function getFilterLikeColumns(): array;

    /**
     * @return array<int, string>
     */
    public function getFilterExactColumns(): array;
}
