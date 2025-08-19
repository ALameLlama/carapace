<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

enum StatusCode: int
{
    case PENDING = 100;
    case ACTIVE = 200;
    case INACTIVE = 300;
}
