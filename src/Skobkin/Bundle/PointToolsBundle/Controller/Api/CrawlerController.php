<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\PostsPage;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;
use Symfony\Component\HttpFoundation\{Request, Response};

class CrawlerController extends AbstractApiController
{
    /** @var string */
    private $crawlerToken;

    public function __construct(string $crawlerToken)
    {
        $this->crawlerToken = $crawlerToken;
    }

    public function receiveAllPageAction(Request $request, SerializerInterface $serializer, PostFactory $postFactory, EntityManagerInterface $em): Response
    {
        $remoteToken = $request->request->get('token');

        if (!$this->crawlerToken || ($this->crawlerToken !== $remoteToken)) {
            return $this->createErrorResponse('Token error. Please check it in crawler and API parameters.', Response::HTTP_FORBIDDEN);
        }

        $json = $request->request->get('json');

        $page = $serializer->deserialize($json, PostsPage::class, 'json');
        
        $continue = $postFactory->createFromPageDTO($page);

        $em->flush();

        return $this->createSuccessResponse([
            'continue' => $continue,
        ]);
    }
}
