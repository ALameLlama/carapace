# Combining Attributes

Carapace attributes can be combined to create powerful, flexible DTOs. This advanced guide shows how to leverage multiple attributes together for complex scenarios.

## Attribute Processing Order

When multiple attributes are applied to a property, they are processed in a specific order:

1. **Pre-hydration** (`MapFrom`, etc.)
2. **Hydration** (value assignment)
3. **Post-hydration** (not currently implemented)
4. **Serialization** (`MapTo`, `Hidden`, etc.)

Understanding this order is important when combining attributes, as it determines how they interact.

## Basic Combinations

You can apply multiple attributes to a single property to achieve combined effects:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\ImmutableDTO;

final class User extends ImmutableDTO
{
    public function __construct(
        #[MapFrom('display_name')]
        #[MapFrom('user_name')]
        #[MapTo('full_name')]
        public string $name,

        #[MapFrom('user_email')]
        #[MapTo('email_address')]
        public string $email,

        #[Hidden]
        public string $password,
    ) {}
}
```

In this example:

- `name` is mapped from `user_name` or `display_name` in the input and to `full_name` in the output
- `email` is mapped from `user_email` in the input and to `email_address` in the output
- `password` is excluded from serialization

## Mapping and Casting

Combine `MapFrom` with `CastWith` to both map and transform data:

```php
final class Order extends ImmutableDTO
{
    public function __construct(
        public string $id,

        #[MapFrom('order_date')]
        #[CastWith(new DateTimeCaster('Y-m-d'))]
        public DateTimeInterface $date,

        #[MapFrom('items')]
        #[CastWith(OrderItem::class)]
        public array $orderItems,
    ) {}
}
```

This allows you to:

1. Accept data with different key names
2. Automatically cast that data to the appropriate types
