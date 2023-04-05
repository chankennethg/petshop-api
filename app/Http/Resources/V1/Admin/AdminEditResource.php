<?php

namespace App\Http\Resources\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\V1\BaseResource;

class AdminEditResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->data = [
            'uuid' => $this->resource->uuid,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'email_verified_at' => $this->resource->email_verified_at,
            'avatar' => $this->resource->avatar,
            'address' => $this->resource->address,
            'phone_number' => $this->resource->phone_number,
            'is_marketing' => $this->resource->is_marketing,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
            'last_login_at' => $this->resource->last_login_at,
        ];
        return parent::toArray($request);
    }
}
