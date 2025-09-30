<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;

#[SnakeCase]
class BaseDTO extends Data {}

class FirstNameDTO extends BaseDTO
{
    public function __construct(
        public string $firstName
    ) {}
}

it('can support nested DTO extending a BaseDTO camelCase', function (): void {
    $dto = FirstNameDTO::from([
        'firstName' => 'Nick',
    ]);

    expect($dto->firstName)
        ->toBe('Nick');

    expect(json_decode($dto->toJson(), true))
        ->toHaveKey('first_name')
        ->first_name->toBe('Nick');
});

it('can support nested DTO extending a BaseDTO SnakeCase', function (): void {
    $dto = FirstNameDTO::from([
        'first_name' => 'Nick',
    ]);

    expect($dto->firstName)
        ->toBe('Nick');

    expect(json_decode($dto->toJson(), true))
        ->toHaveKey('first_name')
        ->first_name->toBe('Nick');

});
