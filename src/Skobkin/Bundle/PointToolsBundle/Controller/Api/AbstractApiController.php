<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractApiController extends Controller
{
    protected function createSuccessResponse($data, $code = 200)
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ], $code);
    }

    /**
     *
     */
    protected function createErrorResponse($message, $code = 400)
    {
        return new JsonResponse([
            'status' => 'fail',
            'error' => [
                'code' => (int) $code,
                'message' => $message
            ]
        ], $code);
    }
}