# Enum Casting

The `EnumCaster` allows you to cast values to native PHP enums (both backed and unit enums):

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\EnumCaster;
use Alamellama\Carapace\Data;

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

class Task extends Data
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
