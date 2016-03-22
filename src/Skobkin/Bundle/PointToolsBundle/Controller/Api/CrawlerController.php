<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;

class CrawlerController extends AbstractApiController
{
    public function receiveAllPageAction(Request $request)
    {
        $token = $request->request->get('token');
        $json = $request->request->get('json');

        $serializer = $this->get('serializer');

        $data = $serializer->deserialize($json, 'Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\PostsPage', 'json', ['groups' => ['import_post_page']]);

        return $this->createSuccessResponse([
            'continue' => true,
        ]);
    }
}
