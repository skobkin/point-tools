<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CrawlerController extends AbstractApiController
{
    public function receiveAllPageAction(Request $request)
    {
        $remoteToken = $request->request->get('token');
        $localToken = $this->getParameter('crawler_token');

        if (!$localToken || ($localToken !== $remoteToken)) {
            return $this->createErrorResponse('Token error. Please check it in crawler and API parameters.', Response::HTTP_FORBIDDEN);
        }

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
