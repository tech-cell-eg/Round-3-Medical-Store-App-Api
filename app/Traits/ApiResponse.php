<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'true',
            'message' => $message,
            'data' => $data,
            
        ], $code);
    }

    protected function errorResponse($message = null, $code)
    {
        return response()->json([
            'status' => 'false',
            'message' => $message,
        ], $code);
    }

}
