<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use ReflectionAttribute;
use ReflectionClass;

trait GetParentAttributesTrait
{
    /**
     * Helper to get attributes from the class and all its parents (closest first).
     *
     * @template T of object
     *
     * @param  ReflectionClass<T>  $reflection
     * @return list<ReflectionAttribute<object>>
     */
    private static function getParentAttributes(ReflectionClass $reflection): array
    {
        /** @var list<ReflectionAttribute<object>> $attributes */
        $attributes = [];

        // Traverse the class hierarchy from the given class up to the root.
        $current = $reflection;
        while ($current !== false) {
            foreach ($current->getAttributes() as $attr) {
                $attributes[] = $attr;
            }
            $current = $current->getParentClass();
        }

        return $attributes;
    }
}
