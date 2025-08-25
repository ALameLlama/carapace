<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;
use ReflectionProperty;

/**
 * Groups multiple flat input keys into a nested structure for a property.
 *
 * Example:
 * #[GroupFrom('street', 'city', 'postcode')]
 * public Address $address;
 *
 * If the incoming data has keys `street`, `city`, and `postcode`, this attribute
 * will collect them into an array and assign it to the `address` property, allowing
 * ImmutableDTO to hydrate the nested Address DTO automatically.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class GroupFrom implements PropertyPreHydrationInterface
{
    /**
     * @var array<string>
     */
    public array $sourceKeys;

    public function __construct(string ...$sourceKeys)
    {
        $this->sourceKeys = $sourceKeys;
    }

    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $propertyName = $property->getName();

        // Do not override if the property value is already present.
        if ($data->has($propertyName)) {
            return;
        }

        $group = [];

        foreach ($this->sourceKeys as $key) {
            if (! $data->has($key)) {
                continue;
            }

            $group[$key] = $data->get($key);
            $data->unset($key);
        }

        if ($group !== []) {
            $data->set($propertyName, $group);
        }
    }
}
