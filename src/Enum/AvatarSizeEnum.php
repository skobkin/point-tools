<?php
declare(strict_types=1);

namespace App\Enum;

enum AvatarSizeEnum: int
{
    case Small = 24;
    case Medium = 40;
    case Large = 80;
}
