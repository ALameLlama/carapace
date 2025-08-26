<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Support\Data;

it('can wrap object', function (): void {
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

it('can set values on the underlying object property', function (): void {
    $obj = new class
    {
        public mixed $b = null;
    };

    $data = Data::wrap($obj);
    $data->set('b', 2);

    expect($obj->b)->toBe(2);
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

it('can return the same object handle from raw so mutations are visible', function (): void {
    $obj = new class
    {
        public mixed $c = null;
    };

    $data = Data::wrap($obj);
    $raw = $data->raw();
    $raw->c = 3;

    expect($obj->c)->toBe(3);
});
