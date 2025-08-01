<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Maps a property from another key in the data array when hydrating an object.
 * This is useful for transforming data where the source key needs to be renamed
 * or moved to a different property during hydration.
 */
final class MapFrom implements HandlesBeforeHydration
{
    public function __construct(public string $sourceKey) {}

    /**
     * Handles the mapping of a property from another key in the data array.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  array<mixed>  $data  The data being hydrated.
     */
    public function handle(string $propertyName, array &$data): void
    {
        if (! array_key_exists($this->sourceKey, $data)) {
            return;
        }

        $data[$propertyName] = $data[$this->sourceKey];
        unset($data[$this->sourceKey]);
    }
}
