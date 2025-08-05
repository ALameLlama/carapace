<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\ImmutableDTO;

final class Account extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[CastWith(User::class)]
        /** @var User[] */
        public array $users,
    ) {}
}
