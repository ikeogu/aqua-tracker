<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait RespondsWithHttpStatus
{
    /**
     * @param array<mixed> $data
     */
    protected function success(?string $message = null, mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            ...($message ? ['message' => $message] : []),
            ...($data ? ['data' => $data] : []),
        ], $code);
    }

    protected function error(?string $message = null, mixed $error = null, int $code = 400): JsonResponse
    {

        return response()->json([
            'status' => false,
            ...($message ? ['message' => $message] : []),
            ...($error ? ['error' => $error] : []),
        ], $code);
    }
}
