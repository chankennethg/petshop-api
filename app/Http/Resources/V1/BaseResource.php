<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * @var int
     */
    protected int $success = 1;

    /**
     * @var string|null
     */
    protected string|null $error = '';

    /**
     * @var array<mixed>
     */
    protected array $errors = [];

    /**
     * @var array<mixed>|null
     */
    protected array|null $extra = null;

    /**
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $extraKeyName = $this->success === 0 ? 'trace' : 'extra';

        return [
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'errors' => $this->errors,
            $extraKeyName => $this->extra,
        ];
    }
}
