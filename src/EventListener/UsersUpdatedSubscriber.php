<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Event\UsersRenamedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\{User, UserRenameEvent, UserRenameEvent as RenameEventEntity};
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

// For new doctrine: https://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html#creating-the-subscriber-class
//use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
//use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;

class UsersUpdatedSubscriber implements EventSubscriber
{
    /** @var UserRenameEvent[] */
    private array $renameEntities = [];

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preUpdate',
            'postFlush',
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        /** @var User $entity */
        $entity = $event->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if ($event->hasChangedField('login')) {
            $this->renameEntities[] = new RenameEventEntity($entity, $event->getOldValue('login'));
        }
    }

    /** TODO: try to avoid double flush or no? */
    public function postFlush(PostFlushEventArgs $event): void
    {
        if (0 !== count($this->renameEntities)) {
            // Creating event for dispatch
            $usersRenamedEvent = new UsersRenamedEvent($this->renameEntities);

            $om = $event->getObjectManager();

            foreach ($this->renameEntities as $item) {
                $om->persist($item);
            }

            $this->renameEntities = [];

            $om->flush();

            $this->eventDispatcher->dispatch($usersRenamedEvent, UsersRenamedEvent::NAME);
        }
    }
}
