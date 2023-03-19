<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\UserRenameEvent;
use Symfony\Contracts\EventDispatcher\Event;

/** Dispatched when one or more users were renamed */
class UsersRenamedEvent extends Event
{
    const NAME = 'app.users.renamed';

    /** @param UserRenameEvent[] $renames */
    public function __construct(
        public readonly array $renames
    ) {
    }
}
