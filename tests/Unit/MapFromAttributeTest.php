<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\MapFrom;

it('can map attributes from an array using MapFrom with a single source key', function (): void {
    $data = [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $attribute = new MapFrom('email_address');
    $attribute->handle('email', $data);

    expect($data)
        ->toHaveKey('email')
        ->and($data['email'])
        ->toBe('nick@example.com');
});

it('can map attributes using the first matching source key', function (): void {
    $data = [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $attribute = new MapFrom('contact_email', 'email_address');
    $attribute->handle('email', $data);

    expect($data)
        ->toHaveKey('email')
        ->and($data['email'])
        ->toBe('nick@example.com');
});

it('can map attributes using the second source key when first is not present', function (): void {
    $data = [
        'name' => 'Nick',
        'contact_email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $attribute = new MapFrom('email_address', 'contact_email');
    $attribute->handle('email', $data);

    expect($data)
        ->toHaveKey('email')
        ->and($data['email'])
        ->toBe('nick@example.com');
});

it('does nothing when none of the source keys are present', function (): void {
    $data = [
        'name' => 'Nick',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $attribute = new MapFrom('email_address', 'contact_email');
    $attribute->handle('email', $data);

    expect($data)
        ->not->toHaveKey('email');
});

it('does nothing when sourceKeys array is empty', function (): void {
    $data = [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
    ];

    $attribute = new MapFrom;
    $attribute->handle('email', $data);

    expect($data)
        ->not->toHaveKey('email')
        ->and($data)
        ->toHaveKey('email_address');
});
