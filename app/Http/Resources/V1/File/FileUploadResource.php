<?php

namespace App\Http\Resources\V1\File;

use App\Http\Resources\V1\BaseResource;
use Illuminate\Http\Request;

class FileUploadResource extends BaseResource
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
            'name' => $this->resource->name,
            'path' => $this->resource->path,
            'size' => $this->resource->size,
            'type' => $this->resource->type,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
        ];
        return parent::toArray($request);
    }
}
