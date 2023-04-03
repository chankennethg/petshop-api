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
     * @param array<mixed,mixed> $trace
     * @return \Illuminate\Http\JsonResponse
     */
    protected function toResponse($code = 400, $isSuccess = 0, $data = [], $error = null, $errors = [], $trace = [])
    {
        return response()->json([
            'success' => $isSuccess,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            'trace' => $trace,
        ], $code);
    }
}
