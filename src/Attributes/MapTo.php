<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Maps a property to a specific key in the array when transforming to an array.
 * This is useful for ensuring that the property is always represented with a specific key,
 * or want to have properties use PascalCase but the array keys use snake_case when converting to an array.
 */
final class MapTo implements HandlesPropertyTransformation
{
    public function __construct(
        public string $destinationKey
    ) {}

    /**
     * Handles the mapping of a property to a specific key in the array.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  mixed  $value  The value of the property.
     * @return array{key: string, value: mixed} The key-value pair to be used in the array.
     */
    public function handlePropertyTransformation(string $propertyName, mixed $value): array
    {
        return ['key' => $this->destinationKey, 'value' => $value];
    }
}
