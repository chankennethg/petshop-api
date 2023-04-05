<?php

namespace App\Models;

use App\Models\Traits\Listable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contracts\Listable as ListableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @implements ListableContract<Post>
 */
class Post extends Model implements ListableContract
{
    /** @use Listable<Post> */
    use HasFactory, Listable;

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
            'uuid',
            'title',
            'slug',
            'content',
            'created_at',
            'updated_at',
            'metadata->image',
            'metadata->author',
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
}
