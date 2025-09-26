<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Support\Data;

readonly class SimpleReadonly
{
    public function __construct(
        public string $name,
        public int $value,
    ) {}
}

it('sets override on readonly property without mutating the original', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);

    expect($wrapped->get('value'))
        ->toBe(2)
        ->and($wrapped->has('value'))
        ->toBeTrue()
        ->and($original->value)
        ->toBe(1);
});

it('toArray reflects overridden readonly property', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);

    expect($wrapped->toArray())
        ->toMatchArray([
            'name' => 'Item',
            'value' => 2,
        ]);
});

it('can add extra dynamic property via overrides on a readonly object', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('extra', 7);

    expect($wrapped->get('extra'))
        ->toBe(7)
        ->and($wrapped->has('extra'))
        ->toBeTrue()
        ->and($wrapped->toArray()['extra'])
        ->toBe(7);
});

it('items reflects original and overridden values on a readonly object', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);
    $wrapped->set('extra', 7);

    $items = $wrapped->items();
    expect($items)
        ->toContain('Item')
        ->toContain(2)
        ->toContain(7);
});

it('unset after set masks the readonly property in the wrapper', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);
    $wrapped->unset('value');

    expect($wrapped->has('value'))
        ->toBeFalse()
        ->and($wrapped->get('value'))
        ->toBeNull();
});

it('toArray omits masked property after unset on a readonly object', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);
    $wrapped->unset('value');

    expect($wrapped->toArray())
        ->not->toHaveKey('value');
});

it('items omits the original value after masking on a readonly object', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);
    $wrapped->unset('value');

    expect($wrapped->items())
        ->not->toContain(1);
});

it('setting again after mask unmasks and takes precedence on a readonly object', function (): void {
    $original = new SimpleReadonly('Item', 1);
    $wrapped = Data::wrap($original);

    $wrapped->set('value', 2);
    $wrapped->unset('value');
    $wrapped->set('value', 5);

    expect($wrapped->has('value'))
        ->toBeTrue()
        ->and($wrapped->get('value'))
        ->toBe(5)
        ->and($wrapped->toArray()['value'])
        ->toBe(5);
});

class MixedProps
{
    public readonly int $ro;

    public int $rw = 5;

    public function __construct()
    {
        $this->ro = 1;
    }
}

it('applies non-destructive overrides to both readonly and writable properties without mutating the original object', function (): void {
    $obj = new MixedProps;
    $wrapped = Data::wrap($obj);

    $wrapped->set('ro', 2);

    expect($wrapped->get('ro'))
        ->toBe(2)
        ->and($obj->ro)
        ->toBe(1)
        ->and($wrapped->toArray()['ro'])
        ->toBe(2);

    $wrapped->set('rw', 6);

    expect($wrapped->get('rw'))
        ->toBe(6)
        ->and($obj->rw)
        ->toBe(5);
});
