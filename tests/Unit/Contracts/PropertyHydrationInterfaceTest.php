<?php

declare(strict_types=1);

namespace Tests\Unit;

use const FILTER_FLAG_IPV4;
use const FILTER_VALIDATE_IP;

use Alamellama\Carapace\Contracts\PropertyHydrationInterface;
use Alamellama\Carapace\Data;
use Alamellama\Carapace\Support\Data as DataWrapper;
use Attribute;
use InvalidArgumentException;
use ReflectionProperty;

use function is_string;

/**
 * An attribute that validates IPv4 addresses during hydration.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidateIPv4 implements PropertyHydrationInterface
{
    public function propertyHydrate(ReflectionProperty $property, DataWrapper $data): void
    {
        $propertyName = $property->getName();

        if (! $data->has($propertyName)) {
            return;
        }

        $value = $data->get($propertyName);

        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException("Invalid IPv4 address for property '{$propertyName}': {$value}");
        }
    }
}

class ServerDTO extends Data
{
    public function __construct(
        #[ValidateIPv4]
        public string $ipAddress,
        public string $name,
    ) {}
}

it('validates valid IPv4 addresses', function (): void {
    $dto = ServerDTO::from([
        'ipAddress' => '192.168.1.1',
        'name' => 'Test Server',
    ]);

    expect($dto)
        ->toBeInstanceOf(ServerDTO::class)
        ->ipAddress->toBe('192.168.1.1')
        ->name->toBe('Test Server');
});

it('throws exception for invalid IPv4 addresses', function (): void {
    ServerDTO::from([
        'ipAddress' => 'invalid-ip',
        'name' => 'Test Server',
    ]);
})->throws(InvalidArgumentException::class, "Invalid IPv4 address for property 'ipAddress': invalid-ip");
