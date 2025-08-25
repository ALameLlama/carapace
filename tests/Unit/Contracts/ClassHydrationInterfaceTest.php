<?php

declare(strict_types=1);

namespace Tests\Unit\Contracts;

use Alamellama\Carapace\Contracts\ClassHydrationInterface;
use Alamellama\Carapace\ImmutableDTO;
use Alamellama\Carapace\Support\Data;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_CLASS)]
final class EnsureNonEmpty implements ClassHydrationInterface
{
    public function classHydrate(ReflectionProperty $property, Data $data): void
    {
        $name = $property->getName();

        if (! $data->has($name)) {
            return;
        }

        $value = $data->get($name);
        if ($value === '') {
            $data->set($name, 'default');
        }
    }
}

#[EnsureNonEmpty]
final class Server2DTO extends ImmutableDTO
{
    public function __construct(
        public string $host,
        public int $port = 80,
    ) {}
}

it('can run class-level hydration handlers to adjust values', function (): void {
    $dto = Server2DTO::from([
        'host' => '',
    ]);

    expect($dto)
        ->host->toBe('default')
        ->port->toBe(80);
});
