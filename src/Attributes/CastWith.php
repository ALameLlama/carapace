<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\CasterInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\ImmutableDTO;
use Alamellama\Carapace\Support\Data;
use Attribute;
use InvalidArgumentException;
use ReflectionProperty;

use function is_array;
use function is_null;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts a property using either a class-string of ImmutableDTO/CasterInterface class or a CasterInterface implementation.
 *
 * This ensures the property is properly cast to the desired type during hydration.
 */
class CastWith implements PropertyPreHydrationInterface
{
    /**
     * @var class-string<ImmutableDTO>|class-string<CasterInterface>|CasterInterface
     */
    public string|CasterInterface $caster;

    /**
     * @param  class-string<ImmutableDTO>|class-string<CasterInterface>|CasterInterface  $caster  Either a class-string of ImmutableDTO/CasterInterface class or a CasterInterface implementation.
     */
    public function __construct(
        string|CasterInterface $caster
    ) {
        if ($caster instanceof CasterInterface) {
            $this->caster = $caster;

            return;
        }

        if (is_subclass_of($caster, CasterInterface::class)) {
            $this->caster = new $caster;

            return;
        }

        if (class_exists($caster) && is_subclass_of($caster, ImmutableDTO::class)) {
            $this->caster = $caster;

            return;
        }

        throw new InvalidArgumentException("Invalid caster type: {$caster}");
    }

    /**
     * Handles the casting of a property using either a class-string of ImmutableDTO/CasterInterface class or a CasterInterface implementation.
     *
     * @throws InvalidArgumentException If the value cannot be cast properly.
     */
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $propertyName = $property->getName();

        if (! $data->has($propertyName)) {
            return;
        }

        $value = $data->get($propertyName);

        $type = $property->getType();

        // Only return early if we allow null otherwise the caster might handle this
        if (is_null($type) || $type->allowsNull() && is_null($value)) {
            return;
        }

        if ($value instanceof $this->caster) {
            return;
        }

        if ($this->caster instanceof CasterInterface) {
            $value = $this->caster->cast($value);
            $data->set($propertyName, $value);

            return;
        }

        // Collection of DTOs
        if (is_array($value) && array_is_list($value)) {
            $data->set(
                $propertyName,
                array_map(
                    fn ($item) => $item instanceof $this->caster ? $item : $this->caster::from($item),
                    $value
                )
            );

            return;
        }

        // Single DTO array
        if (is_array($value)) {
            $data->set($propertyName, $this->caster::from($value));

            return;
        }

        throw new InvalidArgumentException("Unable to cast property '{$propertyName}' to " . $this->caster);
    }
}
