<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Skobkin\Bundle\PointToolsBundle\DTO\Api\PostsPage;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;
use Symfony\Component\HttpFoundation\{Request, Response};

class CrawlerController extends AbstractApiController
{
    public function receiveAllPageAction(Request $request): Response
    {
        $remoteToken = $request->request->get('token');
        $localToken = $this->getParameter('crawler_token');

        if (!$localToken || ($localToken !== $remoteToken)) {
            return $this->createErrorResponse('Token error. Please check it in crawler and API parameters.', Response::HTTP_FORBIDDEN);
        }

        $json = $request->request->get('json');

        $serializer = $this->get('jms_serializer');

        $page = $serializer->deserialize($json, PostsPage::class, 'json');

        /** @var PostFactory $factory */
        $factory = $this->get('app.point.post_factory');
        
        $continue = $factory->createFromPageDTO($page);

        $this->getDoctrine()->getManager()->flush();

        return $this->createSuccessResponse([
            'continue' => $continue,
        ]);
    }
}
