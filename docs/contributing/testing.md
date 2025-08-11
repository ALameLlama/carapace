# Testing

Carapace is fully tested and aims for 100% test coverage. This page provides guidance on testing your DTOs and contributing to Carapace.

### Example Test

Here's an example of how you might test a DTO using [Pest PHP](https://pestphp.com/):

```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\User;

it('can create a user DTO', function (): void {
    $user = User::from([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('John Doe')
        ->email->toBe('john.doe@example.com');
});

it('can create a user DTO from JSON', function (): void {
    $user = User::from(json_encode([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]));

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('John Doe')
        ->email->toBe('john.doe@example.com');
});

it('can serialize a user DTO to array', function (): void {
    $user = User::from([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]);

    expect($user->toArray())
        ->toBe([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
});

it('can create a modified copy of a user DTO', function (): void {
    $user = User::from([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]);

    $updatedUser = $user->with(name: 'Jane Doe');

    expect($user->name)->toBe('John Doe');
    expect($updatedUser->name)->toBe('Jane Doe');
    expect($updatedUser)->not->toBe($user);
});
```
