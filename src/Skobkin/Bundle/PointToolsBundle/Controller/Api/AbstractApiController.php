<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractApiController extends Controller
{
    protected function createSuccessResponse($data, int $code = 200): Response
    {
        return $this->json([
            'status' => 'success',
            'data' => $data,
        ], $code);
    }

    /**
     *
     */
    protected function createErrorResponse(string $message, int $code = 400): Response
    {
        return $this->json([
            'status' => 'fail',
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }
}