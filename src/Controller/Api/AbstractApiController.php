<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AbstractApiController extends AbstractController
{
    protected function createSuccessResponse($data, int $code = 200): Response
    {
        return $this->json([
            'status' => 'success',
            'data' => $data,
        ], $code);
    }

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
