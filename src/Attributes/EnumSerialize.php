<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Attribute;
use BackedEnum;
use ReflectionProperty;
use UnitEnum;

/**
 * Controls how enum properties are serialized when converting DTOs.
 *
 * Usage examples:
 *  - #[EnumSerialize(EnumSerialize::VALUE)] // default for backed enums (value)
 *  - #[EnumSerialize(EnumSerialize::NAME)]  // serialize enum name
 *  - #[EnumSerialize(method: 'niceName')]   // call custom instance method on the enum
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumSerialize implements PropertyTransformationInterface
{
    public const VALUE = 'value';

    public const NAME = 'name';

    public function __construct(
        public string $strategy = self::VALUE,
        public ?string $method = null,
    ) {}

    /**
     * @return array{string, mixed}
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        // Internally backed enums just implement an interface on UnitEnum
        if (! $value instanceof UnitEnum) {
            return [$property->getName(), $value];
        }

        if ($this->method !== null && method_exists($value, $this->method)) {
            return [$property->getName(), $value->{$this->method}()];
        }

        $value = match (true) {
            $this->strategy === self::NAME => $value->name,
            $value instanceof BackedEnum => $value->value,
            default => $value->name,
        };

        return [$property->getName(), $value];
    }
}
