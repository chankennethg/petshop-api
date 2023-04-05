<?php

namespace App\Http\Controllers\Api\V1;

use Response;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PromotionListingRequest;

class PromotionController extends Controller
{
    /**
     * Promotion Listing
     *
     * @param PromotionListingRequest $request
     * @return JsonResponse
     */
    public function list(PromotionListingRequest $request): JsonResponse
    {
        $filters = $request->safe()->all();
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $direction = $filters['desc'] ?? 'true';
        $isValid = $filters['valid'] ?? null;

        // If is active has a value convert to boolean
        if ($isValid !== null) {
            $isValid = filter_var($isValid, FILTER_VALIDATE_BOOLEAN);
        }

        // If sort by contains metadata, convert to eloquent readable
        if (str_contains($sortBy, 'metadata')) {
            $sortBy = str_replace('.', '->', $sortBy);
        }

        $promotions = Promotion::active($isValid)
            ->sort($sortBy, $direction)
            ->paginate($request->get('limit', 10));

        return Response::json($promotions);
    }
}
