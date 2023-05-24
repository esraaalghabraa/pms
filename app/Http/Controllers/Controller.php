<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * response success
     * @param mixed|null $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success
    (mixed $data = null, string $message = "ok", int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => $message
        ], $statusCode);

    }

    /**
     * response failure
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error
    (string $message = "error", int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'data' => null,
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

}
