<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use Alamellama\Carapace\Contracts\AttributeInterface;
use ReflectionAttribute;
use ReflectionClass;

trait GetParentAttributesTrait
{
    /**
     * Helper to get attributes from the class and all its parents (closest first).
     *
     * @template TObject of object
     * @template TAttribute of AttributeInterface
     *
     * @param  ReflectionClass<TObject>  $reflection
     * @param  class-string<TAttribute>  $interface
     * @return list<ReflectionAttribute<TAttribute>>
     */
    private static function getParentAttributes(ReflectionClass $reflection, string $interface): array
    {
        /** @var list<ReflectionAttribute<TAttribute>> $attributes */
        $attributes = [];

        // Traverse the class hierarchy from the given class up to the root.
        $current = $reflection;
        while ($current !== false) {
            foreach ($current->getAttributes($interface, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
                $attributes[] = $attr;
            }

            $current = $current->getParentClass();
        }

        return $attributes;
    }
}
