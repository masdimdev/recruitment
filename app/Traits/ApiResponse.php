<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function successResponse($data = [], $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function successResponseWithPagination($data = [], LengthAwarePaginator $paginator = null, $message = null, $code = 200, $additionalAttributes = []): JsonResponse
    {
        $attributes = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'links' => [
                'first' => $paginator->url(1) ?? null,
                'last' => $paginator->url($paginator->lastPage()) ?? null,
                'prev' => $paginator->previousPageUrl() ?? null,
                'next' => $paginator->nextPageUrl() ?? null,
            ]
        ];

        return response()->json(array_merge($attributes, $additionalAttributes), $code);
    }

    protected function errorResponse($message = null, $errors = [], $code = 500): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'data' => null
        ], $code);
    }

    protected function notFoundResponse($message = null): JsonResponse
    {
        if (empty($message)) {
            $message = __('validation.not_found');
        }

        return $this->errorResponse($message, [], 404);
    }
}