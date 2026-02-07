<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function make(
        int $code = 200,
        ?string $message = null,
        mixed $errors = null,
        mixed $data = null
    ): JsonResponse {
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

    public static function success(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return self::make($code, $message, null, $data);
    }

    public static function error(string $message, mixed $errors = null, int $code = 400): JsonResponse
    {
        return self::make($code, $message, $errors);
    }
}
