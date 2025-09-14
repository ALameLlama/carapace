# Collecting DTOs

The `collect` method allows you to hydrate an array of DTO instances.

## Basic Usage

Use the static `collect` method to create multiple DTOs at once:

```php
class User extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}

// From an array of arrays
$users = User::collect([
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
]);

// From a JSON string
$users = User::collect('[
  {"name": "John", "email": "john@example.com"},
  {"name": "Jane", "email": "jane@example.com"}
]');

$userModels = UserModel::all();

$users = User::collect($userModels);

// $users is an array of User instances
```

## Nested DTOs

Nested DTOs are handled the same way as with `from()` — if your DTO contains a property that is another DTO, pass the nested structure and it will be properly hydrated.

```php
class Address extends Data
{
    public function __construct(
        public string $street,
        public string $city,
    ) {}
}

class User extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        #[CastWith(Address::class)]
        public Address $address,
    ) {}
}

$users = User::collect([
    [
        'name' => 'John',
        'email' => 'john@example.com',
        'address' => ['street' => '123 Main', 'city' => 'Anytown'],
    ],
    [
        'name' => 'Jane',
        'email' => 'jane@example.com',
        'address' => ['street' => '456 Oak', 'city' => 'Otherville'],
    ],
]);
```

## When to use `collect()` vs `from()`

- Use `from()` to hydrate a single DTO from an array or JSON object.
- Use `collect()` to hydrate an array of DTOs.

> [!tip]
> If you need to represent a property in another DTO that holds an array of DTOs, consider using the `CastWith` attribute on that property. See Creating DTOs for more details.

See also: [Hydrating DTOs with from()](./from)
