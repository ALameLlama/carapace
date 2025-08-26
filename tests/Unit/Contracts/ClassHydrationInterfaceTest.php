<?php

declare(strict_types=1);

namespace Tests\Unit\Contracts;

use Alamellama\Carapace\Contracts\ClassHydrationInterface;
use Alamellama\Carapace\Data;
use Alamellama\Carapace\Support\Data as DataWrapper;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_CLASS)]
class EnsureNonEmpty implements ClassHydrationInterface
{
    public function classHydrate(ReflectionProperty $property, DataWrapper $data): void
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
readonly class Server2DTO extends Data
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
