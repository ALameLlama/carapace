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

test('can maps scalar', function (): void {
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

test('can maps nested DTOs', function (): void {
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

test('can uses default value if not provided', function (): void {
    $dto = WithDefaultValue::from([]);

    expect($dto->name)->toBe('Default Nick');
});

test('can assigns null to nullable parameter when not provided', function (): void {
    $dto = Nullable::from([]);

    expect($dto->optional)->toBeNull();
});

test('can throws if required parameter is missing', function (): void {
    expect(fn (): RequiredOnly => RequiredOnly::from([]))
        ->toThrow(InvalidArgumentException::class, 'Missing required parameter: required');
});

// I guess you could pass in some sort of state value and change the DTO from within the constructor?
// I'm not sure if this is a good idea, but it is possible.
test('can construct DTO with constructor param but no declared property', function (): void {
    $dto = NoProperty::from(['foo' => 'bar']);

    expect($dto)->not->toHaveProperty('foo');
});

test('can handle json encoded data', function (): void {
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
