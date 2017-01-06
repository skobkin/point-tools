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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class UserRenameSubscriber implements EventSubscriber
{
    /**
     * @var UserRenameEvent[]
     */
    private $items = [];

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
            $this->items[] = new UserRenameEvent($entity, $event->getOldValue('login'));
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (0 !== count($this->items)) {
            // Creating event for dispatch
            $usersRenamedEvent = new GenericEvent(null, $this->items);

            $em = $event->getEntityManager();

            foreach ($this->items as $item) {
                $em->persist($item);
            }

            $this->items = [];

            $em->flush();

            $this->eventDispatcher->dispatch('app.users.renamed', $usersRenamedEvent);
        }
    }
}