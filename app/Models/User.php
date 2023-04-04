<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\HasUuid;
use App\Models\Traits\Sortable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasUuid, HasApiTokens, HasFactory, Notifiable, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected array $fillable = [
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
     * The attributes that are sortable.
     *
     * @var array<int, string>
     */
    public array $sortable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'address',
        'phone_number',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected array $hidden = [
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
    protected array $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return void
     */
    public function scopeNonAdmin(Builder $query): void
    {
        $query->where('is_admin', false);
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @param array<string, int|string|bool> $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if ($filters['first_name'] ?? null) {
            $query->where('first_name', 'like', '%' . $filters['first_name'] . '%');
        }

        return $query;
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
