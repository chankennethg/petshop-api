<?php

namespace App\Http\Controllers\Api\V1;

use Response;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use App\Exceptions\V1\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PostSingleResource;
use App\Http\Requests\V1\Post\PostListingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    /**
     * Blog/Post Listing
     *
     * @param PostListingRequest $request
     * @return JsonResponse
     */
    public function list(PostListingRequest $request): JsonResponse
    {
        $filters = $request->safe()->all();
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $direction = $filters['desc'] ?? 'true';

        // If sort by contains metadata, convert to eloquent readable
        if (str_contains($sortBy, 'metadata')) {
            $sortBy = str_replace('.', '->', $sortBy);
        }

        $posts = Post::sort($sortBy, $direction)
            ->paginate($request->get('limit', 10));

        return Response::json($posts);
    }

    /**
     * Get single post
     *
     * @param string $uuid
     * @return ApiException|PostSingleResource
     */
    public function get(string $uuid): ApiException|PostSingleResource
    {
        try {
            $post = Post::where('uuid', $uuid)->firstOrFail();
            return new PostSingleResource($post);
        } catch(ModelNotFoundException $e) {
            throw new ApiException(404, 'Post not found');
        }
    }
}
