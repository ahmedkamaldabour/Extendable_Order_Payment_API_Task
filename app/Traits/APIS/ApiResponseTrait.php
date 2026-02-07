<?php

namespace App\Traits\APIS;

use Illuminate\Http\JsonResponse;
use function response;

trait ApiResponseTrait
{
    public function apiResponse(?int $code = null, ?string $message = null, mixed $errors = null, mixed $data = null): JsonResponse
    {
        $code ??= 200;

        $array = [
            'status' => $code,
            'success' => $code >= 200 && $code < 300,
            'message' => $message,
        ];

        if ($data === null && $errors !== null) {
            $array['errors'] = $errors;
        } elseif ($data !== null && $errors === null) {
            $array['data'] = $data;
        } else {
            $array['data'] = $data;
            $array['errors'] = $errors;
        }

        return response()->json($array, $code);
    }
}
