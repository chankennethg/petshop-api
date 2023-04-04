<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\V1\BaseResource;

class AdminLoginResource extends BaseResource
{
    /**
     * Class constructor
     * @param mixed $resource
     * @param string $token
     */
    public function __construct(mixed $resource, string $token)
    {
        parent::__construct($resource);
        $this->data = [
            'token' => $token,
        ];
    }
}
