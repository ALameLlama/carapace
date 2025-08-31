<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

enum Color
{
    case RED;
    case GREEN;
    case BLUE;

    public function niceName(): string
    {
        return match ($this) {
            self::RED => 'Red',
            self::GREEN => 'Green',
            self::BLUE => 'Blue',
        };
    }
}
