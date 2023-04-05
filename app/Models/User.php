<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\Listable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contracts\Listable as ListableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @implements ListableContract<User>
 */
class User extends Authenticatable implements ListableContract
{
    /** @use Listable<User> */
    use HasUuid, HasApiTokens, HasFactory, Notifiable, Listable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'phone_number',
        'avatar',
        'is_marketing',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'is_admin',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
            'first_name',
            'last_name',
            'email',
            'address',
            'phone_number',
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
        return [
            'first_name',
            'email',
            'address',
            'phone_number',
        ];
    }

    /**
     * List of filter like columns
     *
     * @return array<int,string>
     */
    public function getFilterExactColumns(): array
    {
        return [
            'created_at',
            'is_marketing',
        ];
    }

    /**
     * Undocumented function
     *
     * @param Builder<User> $query
     * @return void
     */
    public function scopeNonAdmin(Builder $query): void
    {
        $query->where('is_admin', false);
    }

    /**
     * Get User UUID
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
