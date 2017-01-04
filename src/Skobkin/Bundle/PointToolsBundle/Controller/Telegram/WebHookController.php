<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Telegram;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use unreal4u\TelegramAPI\Telegram\Types\Update;

/**
 * {@inheritdoc}
 */
class WebHookController extends Controller
{
    public function receiveUpdateAction(Request $request, $token)
    {
        if ($token !== $savedToken = $this->getParameter('telegram_token')) {
            throw $this->createNotFoundException();
        }

        $content = json_decode($request->getContent(), true);

        $update = new Update(
            $content,
            $this->get('logger')
        );

        $this->get('point_tools.telegram.update_processor')->process($update);

        return new JsonResponse('received');
    }
}
