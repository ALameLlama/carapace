# Carapace

Carapace is a lightweight PHP library for building immutable, strictly typed Data Transfer Objects (DTOs). It leverages PHP 8+ attributes for casting, property mapping, and serialization, while providing a simple, expressive API.

## ✨ Features

### ✅ Immutable DTOs

- Define immutable data objects by extending the `ImmutableDTO` base class.
- Properties are initialized via constructor promotion.
- Enforces strict immutability for data integrity.

### 🎯 Attribute-Driven Mapping

- **`CastWith`**  
  Automatically casts values during hydration:
  - Nested DTOs and collections
  - Primitive types (int, float, string, bool, array)
  - Custom types via `CasterInterface`
- **`MapFrom`**  
  Maps properties from custom keys in the input array.
- **`MapTo`**  
  Controls output keys when serializing the DTO.

### 📦 Serialization

- Convert DTOs to arrays or JSON using built-in methods.
- Supports deep, recursive serialization of nested DTOs.

---

## 🔧 Installation

```bash
composer require alamellama/carapace
```

---

## 🚀 Usage

### Defining a DTO

Extend `ImmutableDTO` and define your properties in the constructor:

```php
use Alamellama\Carapace\ImmutableDTO;

final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
```

### Hydrating a DTO

Use the static `from` method to hydrate the DTO from an array or json:

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);

// Or from JSON

$user = User::fromJson('{"name": "John Doe", "email": "john.doe@example.com"}');
```

### Immutably Updating a DTO

Use the `with` method to create a modified copy:

```php
$updatedUser = $user->with(['name' => 'Jane Doe']);

// Or using named arguments:

$updatedUser = $user->with(name: 'Jane Doe');

echo $user->name // John
echo $updatedUser>name // Jane
```

> The original instance remains unchanged.

---

## Attribute Usage

### `CastWith`

The `CastWith` attribute can be used in two ways:

#### 1. Casting to DTOs

Automatically cast a property into a specific DTO (or array of DTOs):

```php
use Alamellama\Carapace\Attributes\CastWith;

final class Account extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[CastWith(User::class)]
        public array $users,
    ) {}
}
```

```php
$account = Account::from([
    'name' => 'Me, Myself and I',
    'users' => [
        ['name' => 'John', 'email' => 'john@example.com'],
        ['name' => 'Jane', 'email' => 'jane@example.com'],
    ],
]);
```

#### 2. Primitive Type Casting

Cast values to primitive types using the `PrimitiveCaster`:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\PrimitiveCaster;

final class Product extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[CastWith(new PrimitiveCaster('int'))]
        public int $price,
        #[CastWith(new PrimitiveCaster('bool'))]
        public bool $inStock,
        #[CastWith(new PrimitiveCaster('array'))]
        public array $tags,
    ) {}
}
```

```php
$product = Product::from([
    'name' => 'Awesome Product',
    'price' => '1299', // String will be cast to int
    'inStock' => 1,    // Integer will be cast to bool
    'tags' => '["sale", "new"]', // JSON string will be cast to array
]);

// You can also pass scalar values to be wrapped in an array
$product = Product::from([
    'name' => 'Awesome Product',
    'price' => 1299,
    'inStock' => true,
    'tags' => 'featured', // Will become ['featured']
]);
```

The `PrimitiveCaster` supports the following types:
- `int`: Casts to integer
- `float`: Casts to float
- `string`: Casts to string
- `bool`: Casts to boolean
- `array`: Handles multiple scenarios:
  - Keeps arrays as-is
  - Converts JSON strings to arrays
  - Converts objects to arrays
  - Wraps scalar values in an array

#### 3. Custom Casters

You can create your own casters by implementing the `CasterInterface`:

```php
use Alamellama\Carapace\Contracts\CasterInterface;

final readonly class DateTimeCaster implements CasterInterface
{
    public function __construct(
        private string $format = 'Y-m-d H:i:s'
    ) {}

    public function cast(mixed $value): \DateTime
    {
        if ($value instanceof \DateTime) {
            return $value;
        }
        
        if (is_string($value)) {
            return \DateTime::createFromFormat($this->format, $value) 
                ?? new \DateTime($value);
        }
        
        if (is_int($value)) {
            return (new \DateTime())->setTimestamp($value);
        }
        
        throw new \InvalidArgumentException('Cannot cast to DateTime');
    }
}
```

Then use it with the `CastWith` attribute:

```php
final class Event extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[CastWith(new DateTimeCaster('Y-m-d'))]
        public \DateTime $date,
    ) {}
}

$event = Event::from([
    'name' => 'Conference',
    'date' => '2025-08-15', // Will be cast to DateTime
]);
```

### `MapFrom`

Map a DTO property from a different input key:

```php
use Alamellama\Carapace\Attributes\MapFrom;

final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[MapFrom('email_address')]
        public string $email,
    ) {}
}
```

```php
$user = User::from([
    'name' => 'John Doe',
    'email_address' => 'john.doe@example.com',
]);
```

### `MapTo`

Customize the output key for serialization:

```php
use Alamellama\Carapace\Attributes\MapTo;

final class User extends ImmutableDTO
{
    public function __construct(
        #[MapTo('full_name')]
        public string $name,

        #[MapTo('email_address')]
        public string $email,
    ) {}
}
```

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);

print_r($user->toArray());
```

```php
// Output:
[
    'full_name' => 'John Doe',
    'email_address' => 'john.doe@example.com',
]
```

---

## Serialization

```php
$dto->toArray();
$dto->toJson();
```

- Nested DTOs are automatically and recursively serialized.
- Property keys can be customized using `MapTo`.
- `toArray()` only serializes public properties.

---

## ❓ Why Carapace?

- ⚖️ **Strict typing**: Encourages well-typed, predictable data structures.
- 🧊 **Immutable by default**: No accidental mutation.
- 🪄 **Attribute-driven**: Minimal boilerplate for mapping/casting.
- 💡 **Framework-agnostic**: Works in Laravel, Symfony, or plain PHP.
- 🛠️ **Simple, expressive API**: DTOs that are a pleasure to work with.

---

## 🧪 Testing

Carapace is fully tested and aims for 100% test coverage. PRs are welcome.

---

## 🐘 Requirements

- PHP 8.2 or higher

---
