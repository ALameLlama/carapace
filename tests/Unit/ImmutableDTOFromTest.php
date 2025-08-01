<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\Car;
use Tests\Fixtures\DTO\NoProperty;
use Tests\Fixtures\DTO\Nullable;
use Tests\Fixtures\DTO\RequiredOnly;
use Tests\Fixtures\DTO\User;
use Tests\Fixtures\DTO\WithDefaultValue;

it('can map scalar values into a DTO', function (): void {
    $dto = Car::from([
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'color' => 'Blue',
    ]);

    expect($dto)
        ->toBeInstanceOf(Car::class)
        ->make->toBe('Toyota')
        ->model->toBe('Corolla')
        ->year->toBe(2020)
        ->color->toBe('Blue');
});

it('can map nested DTOs from an array', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com')
        ->address->toBeInstanceOf(Address::class)
        ->address->street->toBe('123 Main St');
});

it('can use default value if not provided', function (): void {
    $dto = WithDefaultValue::from([]);

    expect($dto->name)->toBe('Default Nick');
});

it('can assign null to nullable parameters when not provided', function (): void {
    $dto = Nullable::from([]);

    expect($dto->optional)->toBeNull();
});

it('throws an exception if required parameter is missing', function (): void {
    expect(fn (): RequiredOnly => RequiredOnly::from([]))
        ->toThrow(InvalidArgumentException::class, 'Missing required parameter: required');
});

// I guess you could pass in some sort of state value and change the DTO from within the constructor?
// I'm not sure if this is a good idea, but it is possible.
it('can construct a DTO using a constructor parameter but without a declared property', function (): void {
    $dto = NoProperty::from(['foo' => 'bar']);

    expect($dto)->not->toHaveProperty('foo');
});

it('can handle JSON encoded data', function (): void {
    $json = json_encode([
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2020,
        'color' => 'Blue',
    ]);

    $dto = Car::from($json);

    expect($dto)
        ->toBeInstanceOf(Car::class)
        ->make->toBe('Toyota')
        ->model->toBe('Corolla')
        ->year->toBe(2020)
        ->color->toBe('Blue');
});
