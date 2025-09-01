# SnakeCase

The SnakeCase attribute converts property names to snake_case during serialization. Useful when your API outputs snake_case but your code uses camelCase.

## Usage

```php
<?php

declare(strict_types=1);

use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;

#[SnakeCase]
class UserProfile extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phoneNumber,
    ) {}
}

$user = UserProfile::from([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'phoneNumber' => '123',
]);

$user->toArray();
// [
//   'first_name' => 'John',
//   'last_name' => 'Doe',
//   'phone_number' => '123',
// ]
```

## Property-level

You can also apply to a single property:

```php
class Item extends Data
{
    public function __construct(
        #[SnakeCase]
        public string $displayName,
    ) {}
}
```

## Notes

- A class-level attribute applies to all properties unless overridden.
- Works only during serialization; does not affect hydration.
