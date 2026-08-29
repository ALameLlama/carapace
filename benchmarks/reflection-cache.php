<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;

#[SnakeCase]
final class ReflectionCacheBenchmarkData extends Data
{
    public function __construct(
        #[MapFrom('identifier')]
        public int $id,
        #[MapTo('display_name')]
        public string $displayName,
        public string $emailAddress,
        public bool $isActive,
        public ?string $notes = null,
    ) {}
}

$iterations = 5000;
$run = static function () use ($iterations): void {
    for ($iteration = 0; $iteration < $iterations; $iteration++) {
        $dto = ReflectionCacheBenchmarkData::from([
            'identifier' => $iteration,
            'displayName' => 'Benchmark User',
            'email_address' => 'benchmark@example.test',
            'is_active' => true,
        ]);
        $dto->toArray();
        $dto->with(displayName: 'Updated User')->toArray();
    }
};

$run();
$start = hrtime(true);
$run();

printf(
    "PHP %s; iterations=%d; elapsed=%.3fms\n",
    PHP_VERSION, $iterations, (hrtime(true) - $start) / 1_000_000
);
