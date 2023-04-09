<?php
declare(strict_types=1);

namespace App\Enum\Telegram;

enum ChatTypeEnum: string
{
    case Private = 'private';
    case Group = 'group';
    case SuperGroup = 'supergroup';
    case Channel = 'channel';
}
