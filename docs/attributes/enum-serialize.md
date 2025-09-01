<AttributeBadges scope="property" stage="serialization" />

# EnumSerialize

`EnumSerialize` controls how enums are serialized.
It is useful when you want to output either the enums backed value, the case name, or a custom method.

## Modes

By default, Carapace serializes backed enums to their value and unit enums to their case name.
The EnumSerialize attribute allows overriding this.

- `EnumSerialize::VALUE` (default for backed): serialize backed enums to ->value
- `EnumSerialize::NAME`: serialize to case name
- Custom method: call a custom instance method on the enum via the `method` named argument

## Usage

```php
use Alamellama\Carapace\Attributes\EnumSerialize;
use Alamellama\Carapace\Data;

enum Status: string { case PENDING = 'pending'; case ACTIVE = 'active'; }

enum Color { case RED; case GREEN; }

class Item extends Data
{
    public function __construct(
        #[EnumSerialize(EnumSerialize::NAME)]
        public Status $status,

        #[EnumSerialize(EnumSerialize::NAME)]
        public Color $color,
    ) {}
}

$item = Item::from([
    'status' => 'active',
    'color' => 'RED',
]);

$item->toArray();
// ['status' => 'ACTIVE', 'color' => 'RED']
```

## Custom method

You can provide a custom method to be called on the enum instance during serialization.
The method must exist on the enum and takes no arguments.
Its return value will be used in the serialized output.

```php
use Alamellama\Carapace\Attributes\EnumSerialize;
use Alamellama\Carapace\Data;

enum Status: string {
    case PENDING = 'pending';
    case ACTIVE = 'active';

    // Any instance method with no parameters is fine
    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Pending approval',
            self::ACTIVE => 'Active and running',
        };
    }
}

class Job extends Data
{
    public function __construct(
        #[EnumSerialize(method: 'description')]
        public Status $status,
    ) {}
}

$job = Job::from(['status' => 'active']);
$job->toArray();
// ['status' => 'Active and running']
```
