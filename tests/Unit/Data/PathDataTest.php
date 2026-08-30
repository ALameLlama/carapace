<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use Alamellama\Carapace\Support\Data;
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
