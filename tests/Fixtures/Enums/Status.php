<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Approval',
            self::ACTIVE => 'Active and Running',
            self::INACTIVE => 'Inactive/Disabled',
        };
    }
}
