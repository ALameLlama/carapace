# DateTime Casting

The `DateTimeCaster` allows you to automatically cast values to PHP's native `DateTime` objects during DTO hydration:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\Data;

class Event extends Data
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

## Supported Input Types

The `DateTimeCaster` can handle various input types:

1. DateTime objects: Passed through unchanged
2. Strings: Parsed according to the specified format
3. Integers: Treated as Unix timestamps

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
