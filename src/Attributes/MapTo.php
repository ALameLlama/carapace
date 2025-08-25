<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Maps a property to a specific key in the array when transforming to an array.
 *
 * This is useful for ensuring that the property is always represented with a specific key.
 * For instance, you might have properties using PascalCase while array keys use snake_case.
 */
final class MapTo implements PropertyTransformationInterface
{
    /**
     * @param  string  $destinationKey  The key to use in the output array
     */
    public function __construct(
        public string $destinationKey
    ) {}

    /**
     * Handles the mapping of a property to a specific key in the array.
     *
     * @return array{string, mixed} The key-value pair to be used in the array.
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        return [$this->destinationKey, $value];
    }
}
