<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

interface HandlesBeforeHydration
{
    // I'm not sure if I love passing this by reference, might have this later
    /** @param array<mixed> $data */
    public function handleBefore(string $propertyName, array &$data): void;
}
