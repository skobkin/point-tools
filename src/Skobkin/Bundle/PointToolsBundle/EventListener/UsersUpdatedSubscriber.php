<?php

namespace Skobkin\Bundle\PointToolsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
// For new doctrine: https://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html#creating-the-subscriber-class
//use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
//use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Entity\UserRenameEvent;
use Skobkin\Bundle\PointToolsBundle\Event\UsersRenamedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UsersUpdatedSubscriber implements EventSubscriber
{
    /**
     * @var UserRenameEvent[]
     */
    private $renames = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    /**
     * UserRenameSubscriber constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
            'postFlush',
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        /** @var User $entity */
        $entity = $event->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if ($event->hasChangedField('login')) {
            $this->renames[] = new UserRenameEvent($entity, $event->getOldValue('login'));
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (0 !== count($this->renames)) {
            // Creating event for dispatch
            $usersRenamedEvent = new UsersRenamedEvent($this->renames);

            $em = $event->getEntityManager();

            foreach ($this->renames as $item) {
                $em->persist($item);
            }

            $this->renames = [];

            $em->flush();

            $this->eventDispatcher->dispatch(UsersRenamedEvent::NAME, $usersRenamedEvent);
        }
    }
}