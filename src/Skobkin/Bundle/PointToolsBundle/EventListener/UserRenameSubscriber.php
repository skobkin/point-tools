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

class UserRenameSubscriber implements EventSubscriber
{
    /**
     * @var UserRenameEvent[]
     */
    private $items = [];

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
            $old = $event->getOldValue('login');
            $new = $event->getNewValue('login');

            $this->items[] = new UserRenameEvent($entity, $old, $new);
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (0 !== count($this->items)) {
            $em = $event->getEntityManager();

            foreach ($this->items as $item) {
                $em->persist($item);
            }

            $this->items = [];

            $em->flush();
        }
    }
}