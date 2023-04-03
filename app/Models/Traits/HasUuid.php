<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasUuid
{
    /**
     * Boot
     *
     * @return void
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::orderedUuid();
            }
        });
    }

    /**
     * Find or fail
     *
     * @param string $uuid
     * @return User
     */
    public static function findByUuidOrFail(string $uuid): User
    {
        return self::whereUuid($uuid)->firstOrFail();
    }

    /**
     * Eloquent scope to look for a given UUID
     *
     * @param  Builder $query
     * @param  String  $uuid  The UUID to search for
     * @return Builder
     */
    public function scopeUuid(Builder $query, string $uuid): Builder
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
