<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Telegram;

use Skobkin\Bundle\PointToolsBundle\Service\Telegram\IncomingUpdateDispatcher;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use unreal4u\TelegramAPI\Telegram\Types\Update;

/**
 * {@inheritdoc}
 */
class WebHookController extends AbstractController
{
    /** @var string */
    private $telegramToken;

    /** @var bool */
    private $debug;

    public function __construct(string $telegramToken, bool $debug)
    {
        $this->telegramToken = $telegramToken;
        $this->debug = $debug;
    }

    public function receiveUpdateAction(Request $request, string $token, IncomingUpdateDispatcher $updateDispatcher, Logger $logger): Response
    {
        if ($token !== $savedToken = $this->telegramToken) {
            throw $this->createNotFoundException();
        }

        $content = json_decode($request->getContent(), true);

        $update = new Update(
            $content,
            $logger
        );

        try {
            $updateDispatcher->process($update);
        } catch (\Exception $e) {
            if ($this->debug) {
                throw $e;
            }

            $logger->addError('Telegram bot error', [
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
