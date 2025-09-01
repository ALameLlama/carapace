<AttributeBadges scope="property" stage="hydration" />

# CastWith

`CastWith` allows you to automatically cast values during DTO hydration. You can cast to DTOs, or use built-in casters for primitives, enums, and dates.

CastWith accepts any of the following:
- A DTO class-string (e.g. `User::class`) to auto-cast arrays/collections to that DTO
- A caster class-string (e.g. `PrimitiveCaster::class`)
- A caster instance (e.g. `new PrimitiveCaster('int')`)

## Casting to DTOs

Automatically cast a property into a specific DTO (or array of DTOs):

```php
use Alamellama\Carapace\Attributes\CastWith;

class Account extends Data
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

> [!tip]
> The `@var` is to help IDEs understand the type of the `members` property. Carapace will automatically cast each using the `CastWith` item in the array to the specified DTO type.

## Built-in casters

- Date and time: [DateTime Caster](/attributes/cast-with/datetime)
- Enums: [Enum Caster](/attributes/cast-with/enum)
- Primitive types: [Primitive Caster](/attributes/cast-with/primitive)

## Custom Casters

For information on creating your own custom casters, see the [Custom Casters](/advanced/custom-casters) guide in the Advanced section.
