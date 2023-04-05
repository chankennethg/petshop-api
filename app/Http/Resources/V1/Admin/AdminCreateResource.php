<?php

namespace App\Http\Resources\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\V1\BaseResource;

class AdminCreateResource extends BaseResource
{
    /**
     * @var string
     */
    private string $token;

    /**
     * Class constructor
     * @param mixed $resource
     * @param string $token
     */
    public function __construct(mixed $resource, string $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

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
            'address' => $this->resource->address,
            'phone_number' => $this->resource->phone_number,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
            'token' => $this->token,
        ];
        return parent::toArray($request);
    }
}
