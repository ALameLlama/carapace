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

it('applies non-destructive overrides for ImmutableData without mutating the original or creating a new instance', function (): void {
    $original = SimpleImmutable::from(['name' => 'Item', 'value' => 42]);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 99);

    expect($wrapped->get('value'))->toBe(99)
        ->and($original->value)->toBe(42);

    $wrapped->set('name', 'Updated');

    expect($wrapped->get('name'))
        ->toBe('Updated')
        ->and($wrapped->toArray())
        ->toMatchArray(['name' => 'Updated', 'value' => 99]);
});
