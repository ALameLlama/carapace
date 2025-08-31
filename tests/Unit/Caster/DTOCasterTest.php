<?php

declare(strict_types=1);

namespace Tests\Unit\Caster;

use Alamellama\Carapace\Casters\DTOCaster;
use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

it('exposes target DTO class via targetClass()', function (): void {
    $caster = new DTOCaster(User::class);

    expect($caster->targetClass())->toBe(User::class);
});

it('can cast a list of arrays into DTO instances and pass through existing DTOs', function (): void {
    $caster = new DTOCaster(User::class);

    $nick = new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: 'x', city: 'y', postcode: 'z'));

    $out = $caster->cast([
        [
            'name' => 'Mike',
            'email' => 'mike@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
        $nick,
    ]);

    expect($out)
        ->toHaveCount(2)
        ->and($out[0])->toBeInstanceOf(User::class)
        ->and($out[1])->toBe($nick);
});

it('can cast a single associative array into a DTO instance', function (): void {
    $caster = new DTOCaster(User::class);

    $out = $caster->cast([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($out)->toBeInstanceOf(User::class);
});

it('throws for invalid list item types', function (): void {
    $caster = new DTOCaster(User::class);

    $fn = fn (): mixed => $caster->cast([123]);

    $fn();
})->throws(InvalidArgumentException::class, 'Cannot cast list item to DTO');

it('throws for unsupported single value types', function (): void {
    $caster = new DTOCaster(User::class);

    $fn = fn (): mixed => $caster->cast(123);

    $fn();
})->throws(InvalidArgumentException::class, 'Cannot cast value to DTO');
