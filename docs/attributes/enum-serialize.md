# EnumSerialize

The `EnumSerialize` attribute controls how enums are serialized.
It is useful when you want to output either the backed value, the case name, or a custom representation.

## Modes

By default Carapace serializes backed enums to their value and unit enums to their case name.
The EnumSerialize attribute allows overriding this.

Possible modes may include:
- "value" (default for backed): serialize backed enums to ->value
- "name": serialize to case name

## Usage

```php
use Alamellama\Carapace\Attributes\EnumSerialize;
use Alamellama\Carapace\Data;

enum Status: string { case PENDING = 'pending'; case ACTIVE = 'active'; }

enum Color { case RED; case GREEN; }

class Item extends Data
{
    public function __construct(
        #[EnumSerialize('name')]
        public Status $status,

        #[EnumSerialize('name')]
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

