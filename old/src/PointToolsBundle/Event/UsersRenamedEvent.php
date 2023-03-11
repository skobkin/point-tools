<?php

namespace src\PointToolsBundle\Event;

use src\PointToolsBundle\Entity\UserRenameEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched when one or more users were renamed
 */
class UsersRenamedEvent extends Event
{
    const NAME = 'app.users.renamed';

    /**
     * @var UserRenameEvent[]
     */
    private $renames;

    /**
     * UsersRenamedEvent constructor.
     *
     * @param UserRenameEvent[] $renames
     */
    public function __construct(array $renames)
    {
        $this->renames = $renames;
    }

    /**
     * @return UserRenameEvent[]
     */
    public function getRenames(): array
    {
        return $this->renames;
    }
}