<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use App\Http\Resources\V1\BaseResource;

class PostSingleResource extends BaseResource
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
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'content' => $this->resource->content,
            'metadata' => $this->resource->metadata,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
        return parent::toArray($request);
    }
}
