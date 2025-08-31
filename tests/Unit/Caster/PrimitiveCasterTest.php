<?php

declare(strict_types=1);

namespace Tests\Unit\Caster;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\PrimitiveCaster;
use Alamellama\Carapace\Data;
use InvalidArgumentException;

class WithPrimitiveCasting extends Data
{
    public function __construct(
        #[CastWith(new PrimitiveCaster('int'))]
        public int $age,
        #[CastWith(new PrimitiveCaster('float'))]
        public float $height,
        #[CastWith(new PrimitiveCaster('string'))]
        public string $name,
        #[CastWith(new PrimitiveCaster('bool'))]
        public bool $active,
        #[CastWith(new PrimitiveCaster('array'))]
        public array $data
    ) {}
}

it('can cast string to int', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => '25',
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
        'data' => [],
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
        'data' => [],
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
        'data' => [],
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
        'data' => [],
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
        'data' => [],
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
        'data' => [],
    ]);

    expect($dto->active)
        ->toBeBool()
        ->toBeFalse();
});

it('can keep an array as is when casting to array', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
        'data' => ['one', 'two', 'three'],
    ]);

    expect($dto->data)
        ->toBeArray()
        ->toEqual(['one', 'two', 'three']);
});

it('can cast a scalar value to an array', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
        'data' => 'single value',
    ]);

    expect($dto->data)
        ->toBeArray()
        ->toEqual(['single value']);
});

it('can cast a JSON string to an array', function (): void {
    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
        'data' => '["json", "array", "values"]',
    ]);

    expect($dto->data)
        ->toBeArray()
        ->toEqual(['json', 'array', 'values']);
});

it('can cast an object to an array', function (): void {
    $object = new class
    {
        public $prop1 = 'value1';

        public $prop2 = 'value2';
    };

    $dto = WithPrimitiveCasting::from([
        'age' => 25,
        'height' => 175.5,
        'name' => 'John',
        'active' => true,
        'data' => $object,
    ]);

    expect($dto->data)
        ->toBeArray()
        ->toHaveKey('prop1')
        ->toHaveKey('prop2');
});

it('throws exception for unsupported primitive type', function (): void {
    $caster = new PrimitiveCaster('unknown');
    $caster->cast('test');
})->throws(InvalidArgumentException::class, 'Unsupported primitive type: unknown');
