<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Casters\DTOCaster;
use Alamellama\Carapace\Contracts\CasterInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Data;
use Alamellama\Carapace\Support\Data as DataWrapper;
use Attribute;
use InvalidArgumentException;
use JsonException;
use ReflectionProperty;

use function is_null;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts a property using either a class-string of Data/CasterInterface class or a CasterInterface implementation.
 *
 * This ensures the property is properly cast to the desired type during hydration.
 */
class CastWith implements PropertyPreHydrationInterface
{
    public CasterInterface $caster;

    /**
     * @param  class-string<Data>|class-string<CasterInterface>|CasterInterface  $caster  Either a class-string of Data/CasterInterface class or a CasterInterface implementation.
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

        if (is_subclass_of($caster, Data::class)) {
            $this->caster = new DTOCaster($caster);

            return;
        }

        throw new InvalidArgumentException("Invalid caster type: {$caster}");
    }

    /**
     * Handles the casting of a property using either a class-string of Data/CasterInterface class or a CasterInterface implementation.
     *
     * @throws InvalidArgumentException If the value cannot be cast properly.
     */
    public function propertyPreHydrate(ReflectionProperty $property, DataWrapper $data): void
    {
        $propertyName = $property->getName();

        if (! $data->has($propertyName)) {
            return;
        }

        $value = $data->get($propertyName);

        $type = $property->getType();

        // Only return early if we allow null, otherwise the caster might handle this
        if (is_null($type) || ($type->allowsNull() && is_null($value))) {
            return;
        }

        // Always delegate to the caster, but standardize error context
        try {
            $casted = $this->caster->cast($value);
        } catch (JsonException $e) {
            if (method_exists($this->caster, 'targetClass')) {
                /** @var DTOCaster $caster */
                $caster = $this->caster;
                throw new InvalidArgumentException("Unable to cast property '{$propertyName}' to " . $caster->targetClass(), $e->getCode(), previous: $e);
            }

            throw new InvalidArgumentException("Unable to cast property '{$propertyName}' to " . $this->caster::class, $e->getCode(), previous: $e);
        }

        $data->set($propertyName, $casted);
    }
}
