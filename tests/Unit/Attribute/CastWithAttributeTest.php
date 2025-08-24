<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\ImmutableDTO;
use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

it('can cast class-string of DTO instances', function (): void {
    $attribute = new CastWith(User::class);

    expect($attribute->caster)
        ->toBeString(User::class);
});

it('can cast class-string of caster interface', function (): void {
    $attribute = new CastWith(DateTimeCaster::class);

    expect($attribute->caster)
        ->toBeInstanceOf(DateTimeCaster::class);
});

it('can cast caster interface', function (): void {
    $attribute = new CastWith(new DateTimeCaster);

    expect($attribute->caster)
        ->toBeInstanceOf(DateTimeCaster::class);
});

final class CastWithUsersDTO extends ImmutableDTO
{
    public function __construct(
        #[CastWith(User::class)]
        /** @var User[] */
        public array $users = [],
    ) {}
}

final class CastWithUserDTO extends ImmutableDTO
{
    public function __construct(
        #[CastWith(User::class)]
        public ?User $user = null,
    ) {}
}

it('can cast nested arrays into DTO instances', function (): void {
    $dto = CastWithUsersDTO::from([
        'users' => [
            [
                'name' => 'Nick',
                'email' => 'nick@example.com',
                'address' => [
                    'street' => '123 Main St',
                    'city' => 'Melbourne',
                    'postcode' => '3000',
                ],
            ],
            [
                'name' => 'Mike',
                'email' => 'mike@example.com',
                'address' => [
                    'street' => '123 Main St',
                    'city' => 'Melbourne',
                    'postcode' => '3000',
                ],
            ],
        ],
    ]);

    expect($dto->users)
        ->toHaveCount(2)
        ->and($dto->users[0])->toBeInstanceOf(User::class)
        ->and($dto->users[1])->toBeInstanceOf(User::class);
});

it('ignores missing properties during casting', function (): void {
    $dto = CastWithUsersDTO::from(['not_users' => []]);

    expect($dto->users)
        ->toBeArray()
        ->toHaveCount(0);
});

it('skips re-casting for array of DTO instances', function (): void {
    $dto = CastWithUsersDTO::from([
        'users' => [
            new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
            new User(name: 'Mike', email: 'mike@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
        ],
    ]);

    expect($dto->users)
        ->toHaveCount(2)
        ->and($dto->users[0])->toBeInstanceOf(User::class)
        ->and($dto->users[1])->toBeInstanceOf(User::class);
});

it('handles non-array value that is already a DTO instance', function (): void {
    $dto = CastWithUserDTO::from([
        'user' => new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
    ]);

    expect($dto->user)->toBeInstanceOf(User::class);
});

it('can cast a non-array value into a DTO instance', function (): void {
    $dto = CastWithUserDTO::from([
        'user' => [
            'name' => 'Nick',
            'email' => 'nick@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
    ]);

    expect($dto->user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');
});

it('throws if value is not an array or DTO instance', function (): void {
    CastWithUserDTO::from([
        'user' => 'not a valid user object or array',
    ]);
})->throws(InvalidArgumentException::class, "Unable to cast property 'user' to " . User::class);

it('handles empty array without throwing exception', function (): void {
    $dto = CastWithUsersDTO::from([
        'users' => [],
    ]);

    expect($dto->users)
        ->toBeArray()
        ->toHaveCount(0);
});

it('throws if invalid caster provided', function (): void {
    new CastWith('not-real');
})->throws(InvalidArgumentException::class, 'Invalid caster type: not-real');
