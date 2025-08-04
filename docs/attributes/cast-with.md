# CastWith

The `CastWith` attribute allows you to automatically cast values during DTO hydration. It supports casting to DTOs, primitive types, enums, and custom types.

## Casting to DTOs

Automatically cast a property into a specific DTO (or array of DTOs):

```php
use Alamellama\Carapace\Attributes\CastWith;

final class Account extends ImmutableDTO
{
    public function __construct(
        public string $name,
        
        #[CastWith(User::class)]
        /** @var User[] */
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
> **Important**: The `@var` is to help IDEs understand the type of the `members` property. Carapace will automatically cast each using the`CastWith` item in the array to the specified DTO type.

## Primitive Type Casting

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

## Enum Casting

The `EnumCaster` allows you to cast values to native PHP enums (both backed and unit enums):

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\EnumCaster;
use Alamellama\Carapace\ImmutableDTO;

// Define your enums
enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

enum Color
{
    case RED;
    case GREEN;
    case BLUE;
}

// Use EnumCaster in your DTO
final class Task extends ImmutableDTO
{
    public function __construct(
        public string $title,
        
        #[CastWith(new EnumCaster(Status::class))]
        public Status $status,
        
        #[CastWith(new EnumCaster(Color::class))]
        public Color $color
    ) {}
}
```

```php
// The EnumCaster will handle the conversion
$task = Task::from([
    'title' => 'Complete documentation',
    'status' => 'active',     // String value for backed enum
    'color' => 'RED',         // Case name for unit enum
]);

// Case-insensitive matching is supported
$task = Task::from([
    'title' => 'Complete documentation',
    'status' => 'ACTIVE',     // Will match Status::ACTIVE
    'color' => 'red',         // Will match Color::RED
]);

// You can also use enum instances directly
$task = Task::from([
    'title' => 'Complete documentation',
    'status' => Status::PENDING,
    'color' => Color::BLUE,
]);
```

## DateTime Casting

The `DateTimeCaster` allows you to automatically cast values to PHP's native `DateTime` objects during DTO hydration:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\ImmutableDTO;

final class Event extends ImmutableDTO
{
    public function __construct(
        public string $name,
        
        #[CastWith(new DateTimeCaster)]
        public DateTimeInterface $createdAt,
        
        #[CastWith(new DateTimeCaster('Y-m-d'))]
        public DateTimeInterface $eventDate,
    ) {}
}
```

```php
$event = Event::from([
    'name' => 'Conference',
    'createdAt' => '2025-08-04 13:25:00', // Default format: Y-m-d H:i:s
    'eventDate' => '2025-10-15',          // Custom format: Y-m-d
]);

echo $event->createdAt->format('F j, Y'); // August 4, 2025
echo $event->eventDate->format('F j, Y'); // October 15, 2025
```

### Supported Input Types

The `DateTimeCaster` can handle various input types:

1. **DateTime objects**: Passed through unchanged
2. **Strings**: Parsed according to the specified format
3. **Integers**: Treated as Unix timestamps

When working with string dates, you can specify a custom format in the constructor:

```php
// Default format (Y-m-d H:i:s)
#[CastWith(new DateTimeCaster)]
public DateTimeInterface $timestamp;

// Custom format
#[CastWith(new DateTimeCaster('Y-m-d'))]
public DateTimeInterface $date;
```

If a string doesn't match the specified format, the caster will attempt to parse it using PHP's flexible date parsing capabilities. If that fails, an `InvalidArgumentException` will be thrown.

Integer values are treated as Unix timestamps:

```php
$event = Event::from([
    'name' => 'Conference',
    'createdAt' => 1722945900, // Unix timestamp
    'eventDate' => '2025-10-15',
]);
```

## Custom Casters

For information on creating your own custom casters, see the [Custom Casters](/advanced/custom-casters) guide in the Advanced section.