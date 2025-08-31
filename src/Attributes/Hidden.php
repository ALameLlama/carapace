<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\ClassTransformationInterface;
use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
/**
 * Prevents a property from being included in serialized output.
 *
 * When applied to a property or class, this attribute will exclude the property or properties
 * when converting the DTO to an array or JSON.
 */
class Hidden implements ClassTransformationInterface, PropertyTransformationInterface
{
    public const SIGNAL = '__hidden__';

    /**
     * Handles the exclusion of a property from serialized output.
     *
     * @return array{string, mixed} A special signal to indicate this property should be excluded.
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        return [self::SIGNAL, null];
    }

    /**
     * Handles the exclusion of all properties from serialized output.
     *
     * @return array{string, mixed} A special signal to indicate this property should be excluded.
     */
    public function classTransform(ReflectionProperty $property, mixed $value): array
    {
        return [self::SIGNAL, null];
    }
}
