<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class Response
{
    /**
     * Send a JSON response.
     *
     * @param  bool  $status
     * @param  int  $code
     * @param  string|null  $message
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function send(bool $status, int $code, string $message = null, $data = []): JsonResponse
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, $code);
    }
}
