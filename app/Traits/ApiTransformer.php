<?php

namespace App\Traits;

/**
 * Transform Response into general format
 */
trait ApiTransformer
{
    /**
     * Undocumented function
     * @param int $code
     * @param int $isSuccess
     * @param array<mixed,mixed> $data
     * @param string|null $error
     * @param array<mixed,mixed> $errors
     * @param array<mixed,mixed> $extra
     * @return \Illuminate\Http\JsonResponse
     */
    protected function toResponse(int $code = 400, int $isSuccess = 0, array $data = [], string|null $error = null, array $errors = [], array $extra = [])
    {
        $extraKeyName = $isSuccess === 0 ? 'trace' : 'extra';

        return response()->json([
            'success' => $isSuccess,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            $extraKeyName => $extra,
        ], $code);
    }
}
