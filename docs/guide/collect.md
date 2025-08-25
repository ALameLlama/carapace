# Collecting DTOs

The `collect` method allows you to hydrate an array of DTO instances from an array of arrays or a JSON string representing an array.

## Basic Usage

Use the static `collect` method to create multiple DTOs at once:

```php
class User extends ImmutableDTO
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

// $users is an array of User instances
```

## Return Type

- Returns: `array<static>`
- Each element in the returned array is the same DTO type on which you called `collect`.

## Nested DTOs

Nested DTOs are handled the same way as with `from()` — if your DTO contains a property that is another DTO, pass the nested structure and it will be properly hydrated.

```php
class Address extends ImmutableDTO
{
    public function __construct(
        public string $street,
        public string $city,
    ) {}
}

class User extends ImmutableDTO
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

## Error Handling

- JSON input is decoded with `JSON_THROW_ON_ERROR`; invalid JSON will throw a `JsonException`.
- Each item must be compatible with the DTO's constructor. Missing required parameters will result in an `InvalidArgumentException` from `from()`.

## When to use `collect()` vs `from()`

- Use `from()` to hydrate a single DTO from an array or JSON object.
- Use `collect()` to hydrate an array of DTOs from an array of arrays or a JSON array.

> Tip: If you need to represent a property in another DTO that holds an array of DTOs, consider using the `CastWith` attribute on that property. See Creating DTOs for more details.

See also: [Hydrating DTOs with from()](/guide/from)
