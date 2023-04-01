<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Api\PostsPage;
use App\Factory\Blog\PostFactory;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\{Request, Response};

class CrawlerController extends AbstractApiController
{
    public function __construct(
        private readonly string $crawlerSecret,
    ) {
    }

    public function receiveAllPageAction(Request $request, SerializerInterface $serializer, PostFactory $postFactory, EntityManagerInterface $em): Response
    {
        $remoteToken = $request->request->get('token');

        if (!$this->crawlerSecret || ($this->crawlerSecret !== $remoteToken)) {
            return $this->createErrorResponse(
                'Token error. Please check it in crawler and API parameters.',
                Response::HTTP_FORBIDDEN,
            );
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
