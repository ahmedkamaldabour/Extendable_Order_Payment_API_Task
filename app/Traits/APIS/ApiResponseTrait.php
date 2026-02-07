<?php

namespace App\Traits\APIS;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public function apiResponse(?int $code = null, ?string $message = null, mixed $errors = null, mixed $data = null): JsonResponse
    {
        return ApiResponse::make($code ?? 200, $message, $errors, $data);
    }
}
