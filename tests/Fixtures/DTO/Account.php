<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Data;

class Account extends Data
{
    public function __construct(
        public string $name,
        #[CastWith(User::class)]
        /** @var User[] */
        public array $users,
    ) {}
}
