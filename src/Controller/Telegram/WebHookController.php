<?php
declare(strict_types=1);

namespace App\Controller\Telegram;

use Psr\Log\LoggerInterface;
use src\PointToolsBundle\Service\Telegram\IncomingUpdateDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use unreal4u\Telegram\Types\Update;

class WebHookController extends AbstractController
{
    public function __construct(
        private readonly string $telegramToken,
        private readonly bool $debugEnabled,
    ) {
    }

    public function receiveUpdateAction(Request $request, string $token, IncomingUpdateDispatcher $updateDispatcher, LoggerInterface $logger): Response
    {
        if ($token !== $savedToken = $this->telegramToken) {
            throw $this->createNotFoundException();
        }

        $content = \json_decode($request->getContent(), flags: JSON_THROW_ON_ERROR);

        $update = new Update(
            $content,
        );

        try {
            $updateDispatcher->process($update);
        } catch (\Exception $e) {
            if ($this->debugEnabled) {
                throw $e;
            }

            $logger->error('Telegram bot error', [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return new JsonResponse('received');
    }
}
