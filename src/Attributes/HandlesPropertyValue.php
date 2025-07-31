<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

interface HandlesPropertyValue
{
    /** @param array<mixed>|null $data */
    public function handle(mixed $value, ?array $data = null): mixed;
}
