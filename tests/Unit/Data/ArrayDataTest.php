<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use Alamellama\Carapace\Support\Data;

use function array_key_exists;

it('can detect wrap array', function (): void {
    $data = Data::wrap(['a' => 1]);

    expect($data->isArray())->toBeTrue()
        ->and($data->isObject())->toBeFalse();
});

it('can read values from an array using has and get', function (): void {
    $data = Data::wrap(['a' => 1]);

    expect($data->has('a'))->toBeTrue()
        ->and($data->get('a'))->toBe(1)
        ->and($data->has('missing'))->toBeFalse()
        ->and($data->get('missing'))->toBeNull();
});

it('can set values on an array without mutating the original array', function (): void {
    $input = ['a' => 1];
    $data = Data::wrap($input);

    $data->set('b', 2);

    expect($data->get('b'))->toBe(2)
        ->and(array_key_exists('b', $input))->toBeFalse();
});

it('can return array raw snapshot by value', function (): void {
    $data = Data::wrap(['a' => 1]);

    $raw = $data->raw();
    $raw['c'] = 3;

    expect($data->has('c'))->toBeFalse();
});

it('can unset array keys only within the wrapper', function (): void {
    $input = ['a' => 1];
    $data = Data::wrap($input);

    $data->unset('a');

    expect($data->has('a'))->toBeFalse()
        ->and(array_key_exists('a', $input))->toBeTrue();
});
