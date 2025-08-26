<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\ImmutableData;
use Alamellama\Carapace\Support\Data;

readonly class SimpleImmutable extends ImmutableData
{
    public function __construct(
        public string $name,
        public int $value,
    ) {}
}

it('updates wrapped ImmutableData using with() and keeps original unchanged', function (): void {
    $original = SimpleImmutable::from(['name' => 'Item', 'value' => 42]);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 99);

    $rawAfter = $wrapped->raw();

    expect($rawAfter)
        ->toBeInstanceOf(SimpleImmutable::class)
        ->and($rawAfter)->not()->toBe($original) // a new immutable instance returned by with()
        ->and($rawAfter->name)->toBe('Item')
        ->and($rawAfter->value)->toBe(99)
        // Original instance remains unchanged
        ->and($original->value)->toBe(42);

    // Setting another property should again produce a new instance with an updated value
    $prev = $rawAfter;
    $wrapped->set('name', 'Updated');
    $rawAfter2 = $wrapped->raw();

    expect($rawAfter2)
        ->toBeInstanceOf(SimpleImmutable::class)
        ->and($rawAfter2)->not()->toBe($prev)
        ->and($rawAfter2->name)->toBe('Updated')
        ->and($rawAfter2->value)->toBe(99);
});
