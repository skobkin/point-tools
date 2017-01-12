<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Telegram;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use unreal4u\TelegramAPI\Telegram\Types\Update;

/**
 * {@inheritdoc}
 */
class WebHookController extends Controller
{
    public function receiveUpdateAction(Request $request, string $token): Response
    {
        if ($token !== $savedToken = $this->getParameter('telegram_token')) {
            throw $this->createNotFoundException();
        }

        $logger = $this->get('logger');

        $content = json_decode($request->getContent(), true);

        $update = new Update(
            $content,
            $logger
        );

        try {
            $this->get('app.telegram.update_dispatcher')->process($update);
        } catch (\Exception $e) {
            if ($this->getParameter('kernel.debug')) {
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
