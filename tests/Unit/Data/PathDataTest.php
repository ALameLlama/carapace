<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use const E_USER_WARNING;

use Alamellama\Carapace\Support\Data;
use ArrayIterator;
use stdClass;

it('reads nested arrays including present null values', function (): void {
    $data = Data::wrap(['user' => ['profile' => ['name' => 'Jane', 'nickname' => null]]]);

    expect($data->has('user.profile.name'))->toBeTrue()
        ->and($data->get('user.profile.name'))->toBe('Jane')
        ->and($data->has('user.profile.nickname'))->toBeTrue()
        ->and($data->get('user.profile.nickname'))->toBeNull()
        ->and($data->has('user.profile.missing'))->toBeFalse();
});

it('reads paths through mixed arrays and objects', function (): void {
    $profile = new stdClass;
    $profile->contact = ['email' => 'jane@example.com'];

    $data = Data::wrap(['user' => $profile]);

    expect($data->has('user.contact.email'))->toBeTrue()
        ->and($data->get('user.contact.email'))->toBe('jane@example.com');
});

it('stops when an intermediate path value is not traversable', function (): void {
    $data = Data::wrap(['user' => null]);

    expect($data->has('user.name'))->toBeFalse()
        ->and($data->get('user.name'))->toBeNull();
});

it('prefers exact dotted keys, root overrides, and root masks', function (): void {
    $data = Data::wrap(['user.name' => 'literal', 'user' => ['name' => 'nested']]);
    expect($data->has('user.name'))->toBeTrue()
        ->and($data->get('user.name'))->toBe('literal');

    $object = (object) ['user' => (object) ['name' => 'original']];
    $wrapped = Data::wrap($object);
    $wrapped->set('user', ['name' => 'override']);

    expect($wrapped->get('user.name'))->toBe('override');

    $wrapped->unset('user');
    expect($wrapped->has('user.name'))->toBeFalse()
        ->and($wrapped->get('user.name'))->toBeNull();
});

it('expands wildcard segments and preserves missing branch results', function (): void {
    $data = Data::wrap([
        'contacts' => ['first@example.com', null],
        'products' => [['name' => 'Desk'], 'invalid', ['sku' => 2], ['name' => null]],
    ]);

    expect($data->get('contacts.*'))->toBe(['first@example.com', null])
        ->and($data->get('products.*.name'))->toBe(['Desk', null, null, null])
        ->and($data->has('products.*.name'))->toBeTrue()
        ->and($data->has('products.*.missing'))->toBeFalse();
});

it('flattens multiple wildcards and returns an empty list for empty expansion', function (): void {
    $data = Data::wrap(['teams' => [
        ['members' => [['name' => 'A'], ['name' => 'B']]],
        ['members' => [['name' => 'C']]],
    ], 'empty' => []]);

    expect($data->get('teams.*.members.*.name'))->toBe(['A', 'B', 'C'])
        ->and($data->has('teams.*.members.*.name'))->toBeTrue()
        ->and($data->get('empty.*.name'))->toBe([])
        ->and($data->has('empty.*.name'))->toBeFalse();
});

it('preserves escaped path characters and exact wildcard key precedence', function (): void {
    $data = Data::wrap([
        'literal.dot' => ['*' => ['slash\\key' => 'escaped']],
        'products.*.name' => 'literal',
        'products' => [['name' => 'expanded']],
        'root' => ['slash\\' => 'trailing'],
    ]);

    expect($data->get('literal\\.dot.\\*.slash\\\\key'))->toBe('escaped')
        ->and($data->get('products.*.name'))->toBe('literal')
        ->and($data->get('root.slash\\'))->toBe('trailing');
});

it('does not expand traversable values', function (): void {
    $data = Data::wrap(['items' => new ArrayIterator([['name' => 'hidden']])]);

    expect($data->get('items.*.name'))->toBe([])
        ->and($data->has('items.*.name'))->toBeFalse();
});

it('gives masks precedence over wildcard traversal', function (): void {
    $data = Data::wrap((object) ['products' => [['name' => 'hidden']]]);
    $data->unset('products');

    expect($data->get('products.*.name'))->toBeNull()
        ->and($data->has('products.*.name'))->toBeFalse();

    $exact = Data::wrap((object) ['products.*.name' => 'literal', 'products' => [['name' => 'hidden']]]);
    $exact->unset('products.*.name');

    expect($exact->get('products.*.name'))->toBeNull()
        ->and($exact->has('products.*.name'))->toBeFalse();
});

it('expands normal object public properties', function (): void {
    $contacts = (object) ['primary' => (object) ['name' => 'Jane']];

    expect(Data::wrap(['contacts' => $contacts])->get('contacts.*.name'))->toBe(['Jane']);
});

it('preserves isset-only object reads at a wildcard terminal', function (): void {
    $contact = new class
    {
        public function __isset(string $name): bool
        {
            return $name === 'name';
        }
    };

    $data = Data::wrap(['contacts' => [$contact]]);

    expect($data->has('contacts.*.name'))->toBeTrue()
        ->and($data->get('contacts.*.name'))->toBe([null]);
});

it('reads each nested magic getter once', function (): void {
    $nested = new class
    {
        public int $reads = 0;

        public function __get(string $name): stdClass
        {
            $this->reads++;

            return (object) ['name' => 'first'];
        }
    };

    expect(Data::wrap(['account' => $nested])->get('account.contact.name'))->toBe('first')
        ->and($nested->reads)->toBe(1);
});

it('reads wildcard terminal magic getters once', function (): void {
    $contact = new class
    {
        public int $reads = 0;

        public function __get(string $name): string
        {
            return 'name-' . ++$this->reads;
        }
    };

    expect(Data::wrap(['contacts' => [$contact]])->get('contacts.*.name'))->toBe(['name-1'])
        ->and($contact->reads)->toBe(1);
});

it('uses terminal nested isset without invoking the getter', function (): void {
    $contact = new class
    {
        public int $reads = 0;

        public function __isset(string $name): bool
        {
            return $name === 'name';
        }

        public function __get(string $name): string
        {
            $this->reads++;

            return 'Jane';
        }
    };

    expect(Data::wrap(['contact' => $contact])->has('contact.name'))->toBeTrue()
        ->and($contact->reads)->toBe(0);
});

it('keeps nested has tolerant of getter warnings', function (): void {
    $contact = new class
    {
        public function __get(string $name): mixed
        {
            trigger_error('missing', E_USER_WARNING);

            return null;
        }
    };

    expect(Data::wrap(['contact' => $contact])->has('contact.name.value'))->toBeFalse();
});

it('reads a successful intermediate magic getter once during has', function (): void {
    $contact = new class
    {
        public int $reads = 0;

        public function __get(string $name): array
        {
            $this->reads++;

            return ['value' => null];
        }
    };

    expect(Data::wrap(['contact' => $contact])->has('contact.name.value'))->toBeTrue()
        ->and($contact->reads)->toBe(1);
});
