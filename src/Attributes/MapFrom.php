<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
/**
 * Maps a property from one or more keys in the data array when hydrating an object.
 *
 * Useful for transforming data where the source key needs to be renamed
 * or moved to a different property during hydration. Can accept multiple source keys
 * which will be checked in order until a match is found.
 */
final class MapFrom implements PreHydrationInterface
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
     *
     * @param  string  $propertyName  The name of the property being handled.
     */
    public function handle(string $propertyName, Data $data): void
    {
        foreach ($this->sourceKeys as $sourceKey) {
            if (! $data->has($sourceKey)) {
                continue;
            }

            $data->set($propertyName, $data->get($sourceKey));
            $data->unset($sourceKey);

            return;
        }
    }
}
