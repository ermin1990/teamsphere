<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ApiResponses
{
    protected function ok($data = null, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ]);
    }

    protected function created($data = null, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], 201);
    }

    protected function fail(string $message, int $status = 403): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    /**
     * Wrap a paginator in the standard envelope plus a `meta` block, mapping
     * each page's items through the given Resource class.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource> $resourceClass
     */
    protected function paginated($paginator, string $resourceClass, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'message' => $message,
        ]);
    }

    /**
     * Read & clamp the `per_page` query param (default 20, max 100) so
     * clients can't request unbounded page sizes.
     */
    protected function perPage(Request $request, int $default = 20, int $max = 100): int
    {
        return max(1, min($max, (int) $request->input('per_page', $default) ?: $default));
    }
}
