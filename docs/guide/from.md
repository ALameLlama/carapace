# Hydrating DTOs

The `from` method allows you to create DTO instances from arrays or JSON.

## Basic Usage

Use the static `from` method to hydrate the DTO from an array or JSON:

```php
// From an array
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);

// From a JSON string
$user = User::from('{"name": "John Doe", "email": "john.doe@example.com"}');
```

## Nested DTOs

Carapace automatically handles nested DTOs:

```php
final class Address extends ImmutableDTO
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zipCode,
    ) {}
}

final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        public string $email,
        
        #[CastWith(Address::class)]
        public Address $address,
    ) {}
}

$user = User::from([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Anytown',
        'zipCode' => '12345',
    ],
]);

echo $user->address->city; // Outputs: Anytown
```

## Collections of DTOs

You can also work with collections of DTOs:

```php
final class Team extends ImmutableDTO
{
    public function __construct(
        public string $name,
        
        #[CastWith(User::class)]
        /** @var User[] */
        public array $members,
    ) {}
}

$team = Team::from([
    'name' => 'Engineering',
    'members' => [
        ['name' => 'John', 'email' => 'john@example.com'],
        ['name' => 'Jane', 'email' => 'jane@example.com'],
    ],
]);

echo $team->members[0]->name; // Outputs: John
```
> **Important**: The `@var` is to help IDEs understand the type of the `members` property. Carapace will automatically cast each using the`CastWith` item in the array to the specified DTO type.