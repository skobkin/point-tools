<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;
use Symfony\Component\HttpFoundation\Request;

class CrawlerController extends AbstractApiController
{
    public function receiveAllPageAction(Request $request)
    {
        $token = $request->request->get('token');
        $json = $request->request->get('json');

        $serializer = $this->get('jms_serializer');

        $page = $serializer->deserialize($json, 'Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\PostsPage', 'json');

        /** @var PostFactory $factory */
        $factory = $this->get('skobkin__point_tools.service_factory.post_factory');
        
        $continue = $factory->createFromPageDTO($page);

        return $this->createSuccessResponse([
            'continue' => $continue,
        ]);
    }
}
