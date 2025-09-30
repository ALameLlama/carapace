<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use ReflectionProperty;

trait PropertyHasAttributeTrait
{
    /**
     * Helper to check if a property has a specific attribute attached.
     *
     * @param  class-string  $attribute
     */
    private function propertyHasAttribute(ReflectionProperty $property, string $attribute): bool
    {
        foreach ($property->getAttributes() as $attr) {
            if ($attr->getName() === $attribute) {
                return true;
            }
        }

        return false;
    }
}
