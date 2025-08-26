<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Support\Data;
use JsonException;

use function is_array;

it('can decode JSON strings without mutating the original variable', function (): void {
    $json = '{"x":1,"y":null}';

    $data = Data::wrap($json);

    expect($data->isArray())->toBeTrue()
        ->and(is_array($json))->toBeFalse();
});

it('can access decoded JSON values using has and get', function (): void {
    $json = '{"x":1,"y":null}';
    $data = Data::wrap($json);

    expect($data->has('x'))->toBeTrue()
        ->and($data->get('x'))->toBe(1);
});

it('can set values on decoded JSON without mutating the original variable', function (): void {
    $json = '{"x":1,"y":null}';
    $data = Data::wrap($json);

    $data->set('z', 5);

    expect($data->get('z'))->toBe(5)
        ->and($json)->toBe('{"x":1,"y":null}');
});

it('throws exception for invalid JSON input', function (): void {
    $bad = '{"x":';

    $fn = function () use ($bad): void {
        Data::wrap($bad);
    };

    $fn();
})->throws(JsonException::class);
