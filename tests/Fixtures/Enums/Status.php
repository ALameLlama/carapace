<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
