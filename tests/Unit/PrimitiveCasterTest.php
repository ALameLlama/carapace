<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\PrimitiveCaster;
use Alamellama\Carapace\ImmutableDTO;
use InvalidArgumentException;

final class WithPrimitiveCasting extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new PrimitiveCaster('int'))]
        public int $age,
        #[CastWith(new PrimitiveCaster('float'))]
        public float $height,
        #[CastWith(new PrimitiveCaster('string'))]
        public string $name,
        #[CastWith(new PrimitiveCaster('bool'))]
        public bool $active
    ) {}
}

it('can cast string to int', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => '25',
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
    ]);

    expect($dto->age)
        ->toBeInt()
        ->toBe(25);
});

it('can cast string to float', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => '175.5',
        'name' => 'John',
        'active' => true,
    ]);

    expect($dto->height)
        ->toBeFloat()
        ->toBe(175.5);
});

it('can cast int to string', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 42,
        'active' => true,
    ]);

    expect($dto->name)
        ->toBeString()
        ->toBe('42');
});

it('can cast int to bool', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => 1,
    ]);

    expect($dto->active)
        ->toBeBool()
        ->toBeTrue();
});

it('can cast string to bool', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => 'yes',
    ]);

    expect($dto->active)
        ->toBeBool()
        ->toBeTrue();
});

it('can cast falsy values to bool', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => 0,
    ]);

    expect($dto->active)
        ->toBeBool()
        ->toBeFalse();
});

it('throws exception for unsupported primitive type', function (): void {
    $caster = new PrimitiveCaster('array');
    $caster->cast('test');
})->throws(InvalidArgumentException::class, 'Unsupported primitive type: array');
