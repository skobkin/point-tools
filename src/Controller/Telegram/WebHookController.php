<?php
declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Service\Telegram\IncomingUpdateDispatcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use TelegramBot\Api\Types\Update;

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

        try {
            $content = \json_decode($request->getContent(), flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new JsonResponse('bad json', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $update = Update::fromResponse($content);

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
