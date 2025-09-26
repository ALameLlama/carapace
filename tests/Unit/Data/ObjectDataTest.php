<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use Alamellama\Carapace\Support\Data;
use stdClass;

it('wraps objects and identifies type correctly', function (): void {
    $obj = new class {};

    $data = Data::wrap($obj);

    expect($data->isObject())->toBeTrue()
        ->and($data->isArray())->toBeFalse();
});

it('can read public properties from objects using has and get', function (): void {
    $obj = new class
    {
        public int $a = 1;
    };

    $data = Data::wrap($obj);

    expect($data->has('a'))->toBeTrue()
        ->and($data->get('a'))->toBe(1)
        ->and($data->has('missing'))->toBeFalse()
        ->and($data->get('missing'))->toBeNull();
});

it('stores object updates as non-destructive overrides without mutating the original object', function (): void {
    $obj = new class
    {
        public mixed $b = null;
    };

    $data = Data::wrap($obj);
    $data->set('b', 2);

    expect($data->get('b'))->toBe(2)
        ->and($obj->b)->toBeNull();
});

it('does not unset properties on objects (no-op)', function (): void {
    $obj = new class
    {
        public int $a = 1;
    };

    $data = Data::wrap($obj);
    $data->unset('a');

    expect(isset($obj->a))->toBeTrue();
});

it('applies non-destructive overrides to pre-existing dynamic property without mutating the original object', function (): void {
    $obj = new stdClass;
    $obj->dyn = 1;

    $wrapped = Data::wrap($obj);

    $wrapped->set('dyn', 2);

    expect($obj->dyn)
        ->toBe(1)
        ->and($wrapped->get('dyn'))
        ->toBe(2);
});
