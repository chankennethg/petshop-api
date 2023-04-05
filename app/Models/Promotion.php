<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\Listable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contracts\Listable as ListableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @implements ListableContract<Promotion>
 */
class Promotion extends Model implements ListableContract
{
    /** @use Listable<Promotion> */
    use HasFactory, HasUuid, Listable;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * List of sortable columns
     *
     * @return array<int,string>
     */
    public function getSortableColumns(): array
    {
        return [
            'id',
            'uuid',
            'title',
            'content',
            'metadata->image',
            'metadata->valid_from',
            'metadata->valid_to',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * List of filter like columns
     *
     * @return array<int,string>
     */
    public function getFilterLikeColumns(): array
    {
        return [];
    }

    /**
     * List of filter like columns
     *
     * @return array<int,string>
     */
    public function getFilterExactColumns(): array
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @param Builder<Promotion> $query
     * @param bool|null $active
     * @return Builder<Promotion>
     */
    public function scopeActive(Builder $query, bool|null $active = null): Builder
    {
        // Return early if not specified
        if ($active === null) {
            return $query;
        }

        // Return all active
        if ($active) {
            return $query->where('metadata->valid_from', '<=', now())
                ->where('metadata->valid_to', '>=', now());
        }

        // return all inactive
        return $query->where('metadata->valid_from', '>', now())
            ->orWhere('metadata->valid_to', '<', now());
    }
}
