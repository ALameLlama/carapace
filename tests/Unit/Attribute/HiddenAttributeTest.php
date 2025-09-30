<?php

declare(strict_types=1);

namespace Tests\Unit\Attribute;

use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\Data;

class HiddenPropertyDTO extends Data
{
    public function __construct(
        public string $visible,
        #[Hidden]
        public string $hidden,
    ) {}
}

it('excludes properties marked with Hidden attribute when serializing to array', function (): void {
    $dto = HiddenPropertyDTO::from([
        'visible' => 'This should be visible',
        'hidden' => 'This should be hidden',
    ]);

    $array = $dto->toArray();

    expect($array)
        ->toHaveKey('visible')
        ->not->toHaveKey('hidden')
        ->and($array['visible'])
        ->toBe('This should be visible');
});

it('excludes properties marked with Hidden attribute when serializing to JSON', function (): void {
    $dto = HiddenPropertyDTO::from([
        'visible' => 'This should be visible',
        'hidden' => 'This should be hidden',
    ]);

    $json = $dto->toJson();

    expect($json)
        ->toContain('visible')
        ->toContain('This should be visible')
        ->not->toContain('hidden')
        ->not->toContain('This should be hidden');
});

#[Hidden]
class HiddenByClassDTO extends Data
{
    public function __construct(
        public string $field,
    ) {}
}

it('class-level Hidden can hide all properties using a class attribute', function (): void {
    $dto = HiddenByClassDTO::from([
        'field' => 'value',
    ]);

    expect($dto->toArray())
        ->toBeArray()
        ->toBeEmpty();
});
