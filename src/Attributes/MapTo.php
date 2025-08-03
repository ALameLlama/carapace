<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\TransformationHandler;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Maps a property to a specific key in the array when transforming to an array.
 *
 * This is useful for ensuring that the property is always represented with a specific key.
 * For instance, you might have properties using PascalCase while array keys use snake_case.
 */
final class MapTo implements TransformationHandler
{
    public function __construct(
        public string $destinationKey
    ) {}

    /**
     * Handles the mapping of a property to a specific key in the array.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  mixed  $value  The value of the property.
     * @return array{string, mixed} The key-value pair to be used in the array.
     */
    public function handle(string $propertyName, mixed $value): array
    {
        return [$this->destinationKey, $value];
    }
}
