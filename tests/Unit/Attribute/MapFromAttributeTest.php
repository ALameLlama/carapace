<?php

declare(strict_types=1);

namespace Tests\Unit\Attribute;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Data;

class EmailFromSingleKeyDTO extends Data
{
    public function __construct(
        #[MapFrom('email_address')]
        public string $email,
    ) {}
}

class EmailFromEitherKeyDTO extends Data
{
    public function __construct(
        #[MapFrom('contact_email')]
        #[MapFrom('email_address')]
        public string $email,
    ) {}
}

class EmailFromEitherKeyReverseDTO extends Data
{
    public function __construct(
        #[MapFrom('email_address')]
        #[MapFrom('contact_email')]
        public string $email,
    ) {}
}

class OptionalEmailDTO extends Data
{
    public function __construct(
        #[MapFrom]
        public ?string $email = null,
    ) {}
}

it('can map attributes from an array using MapFrom with a single source key', function (): void {
    $dto = EmailFromSingleKeyDTO::from([
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->email)->toBe('nick@example.com');
});

it('can map attributes using the first matching source key', function (): void {
    $dto = EmailFromEitherKeyDTO::from([
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->email)->toBe('nick@example.com');
});

it('can map attributes using the second source key when first is not present', function (): void {
    $dto = EmailFromEitherKeyReverseDTO::from([
        'name' => 'Nick',
        'contact_email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->email)->toBe('nick@example.com');
});

it('does nothing when none of the source keys are present', function (): void {
    $dto = OptionalEmailDTO::from([
        'name' => 'Nick',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->email)->toBeNull();
});

it('does nothing when sourceKeys array is empty', function (): void {
    $dto = OptionalEmailDTO::from([
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
    ]);

    expect($dto->email)->toBeNull();
});
