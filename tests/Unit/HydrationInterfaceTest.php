<?php

declare(strict_types=1);

namespace Tests\Unit;

use const FILTER_FLAG_IPV4;
use const FILTER_VALIDATE_IP;

use Alamellama\Carapace\Contracts\HydrationInterface;
use Alamellama\Carapace\ImmutableDTO;
use Attribute;
use InvalidArgumentException;

use function is_string;

/**
 * An attribute that validates IPv4 addresses during hydration.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ValidateIPv4 implements HydrationInterface
{
    public function handle(string $propertyName, array &$data): void
    {
        if (! isset($data[$propertyName])) {
            return;
        }

        $value = $data[$propertyName];

        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException("Invalid IPv4 address for property '{$propertyName}': {$value}");
        }
    }
}

final class ServerDTO extends ImmutableDTO
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
