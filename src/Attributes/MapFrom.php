<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
/**
 * Maps a property from one or more keys in the data array when hydrating an object.
 *
 * Useful for transforming data where the source key needs to be renamed
 * or moved to a different property during hydration. Can accept multiple source keys
 * which will be checked in order until a match is found.
 */
class MapFrom implements PropertyPreHydrationInterface
{
    /**
     * @var array<string> The keys in the input data to map from (checked in order)
     */
    public array $sourceKeys;

    /**
     * @param  string  ...$sourceKeys  The keys in the input data to map from (checked in order)
     */
    public function __construct(string ...$sourceKeys)
    {
        $this->sourceKeys = $sourceKeys;
    }

    /**
     * Handles the mapping of a property from another key in the data array.
     */
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        foreach ($this->sourceKeys as $sourceKey) {
            if (! $data->has($sourceKey)) {
                continue;
            }

            $data->set($property->getName(), $data->get($sourceKey));
            $data->unset($sourceKey);

            return;
        }
    }
}
