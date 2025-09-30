# Migrate from 1.x to 2.x

This guide helps you upgrade existing code from Carapace 1.x to 2.x. Version 2 introduces clearer naming, stronger typing, and more flexible attributes while remaining framework‑agnostic.

## TL;DR checklist

- [ ] Replace ImmutableDTO base class with Data (or ImmutableData)
- [ ] Update custom attributes to the new interfaces and method names
- [ ] Hidden can be placed on the class now
- [ ] CastWith now accepts: DTO class-string, caster class-string, or caster instance
- [ ] New attributes: ConvertEmptyToNull, EnumSerialize, GroupFrom, SnakeCase
- [ ] Use Support\Data helper in attribute handlers

---

## 1. DTO base classes

### Renamed

- 1.x: `Alamellama\Carapace\ImmutableDTO`
- 2.x: `Alamellama\Carapace\Data`

Most code can simply extend `Data`.

```php
use Alamellama\Carapace\ImmutableDTO; // [!code --]
use Alamellama\Carapace\Data; // [!code ++]


class User extends ImmutableDTO // [!code --]
class User extends Data // [!code ++]
{
    public function __construct(
        public string $name,
    ) {}
}
```

### New readonly base class

- 2.x adds `Alamellama\Carapace\ImmutableData`
- Use it if you want readonly properties by default

```php
use Alamellama\Carapace\ImmutableData;

readonly class User extends ImmutableData
{
    public function __construct(
        public string $name,
    ) {}
}
```

## 2. Attribute handler interfaces

Interfaces are now explicitly scoped to either class or property and to the lifecycle phase. The method names and signatures changed to be self‑descriptive and to receive extra context.

### New interfaces

- `Alamellama\Carapace\Contracts\ClassPreHydrationInterface`
- `Alamellama\Carapace\Contracts\ClassHydrationInterface`
- `Alamellama\Carapace\Contracts\ClassTransformationInterface`

### Renamed (property-scoped)

- 1.x `PreHydrationInterface` → 2.x `PropertyPreHydrationInterface`

```php
class MapFrom implements PreHydrationInterface // [!code --]
class MapFrom implements PropertyPreHydrationInterface // [!code ++]
{
    public function handle(string $propertyName, array &$data): void // [!code --]
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void // [!code ++]
    {
```

- 1.x `HydrationInterface` → 2.x `PropertyHydrationInterface`

```php
class ValidateIPv4 implements HydrationInterface // [!code --]
class ValidateIPv4 implements PropertyHydrationInterface // [!code ++]
{
    public function handle(string $propertyName, array &$data): void // [!code --]
    public function propertyHydrate(ReflectionProperty $property, Data $data): void // [!code ++]
    {
```

- 1.x `TransformationInterface` → 2.x `PropertyTransformationInterface`

```php
class MapTo implements TransformationInterface // [!code --]
class MapTo implements PropertyTransformationInterface// [!code ++]
{
    public function handle(string $propertyName, mixed $value): array // [!code --]
    public function propertyTransform(ReflectionProperty $property, mixed $value): array // [!code ++]
    {
```

### Method signatures

- Pre-hydration (property): `propertyPreHydrate(ReflectionProperty $property, Support\Data $data): void`
- Hydration (property): `propertyHydrate(ReflectionProperty $property, Support\Data $data): void`
- Transformation (property): `propertyTransform(ReflectionProperty $property, mixed $value): array{string, mixed}`
- Pre-hydration (class): `classPreHydrate(ReflectionProperty $property, Support\Data $data): void`
- Hydration (class): `classHydrate(ReflectionProperty $property, Support\Data $data): void`
- Transformation (class): `classTransform(ReflectionProperty $property, mixed $value): array{string, mixed}`

Note the addition of `ReflectionProperty` and `Support\Data` to make complex scenarios easier.

### Example update:

```php
use Alamellama\Carapace\Contracts\HydrationInterface; // [!code --]
use Alamellama\Carapace\Contracts\PropertyHydrationInterface; // [!code ++]
use Alamellama\Carapace\Support\Data; // [!code ++]
use ReflectionProperty; // [!code ++]

class ValidateIPv4 implements HydrationInterface // [!code --]
class ValidateIPv4 implements PropertyHydrationInterface // [!code ++]
{
    public function handle(string $propertyName, array &$data): void // [!code --]
    public function propertyHydrate(ReflectionProperty $property, Data $data): void // [!code ++]
    {
        $propertyName = $property->getName(); // [!code ++]

        if (! isset($data[$propertyName])) { // [!code --]
        if (! $data->has($propertyName)) { // [!code ++]
            return;
        }

        $value = $data[$propertyName];  // [!code --]
        $value = $data->get($propertyName); // [!code ++]

        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException("Invalid IPv4 address for property '{$propertyName}': {$value}");
        }
    }
}

```

## 3. CastWith enhancements

In 2.x, `#[CastWith]` can be constructed with:

- a DTO class-string: `#[CastWith(Address::class)]`
- a caster class-string: `#[CastWith(DateTimeCaster::class)]`
- a caster instance: `#[CastWith(new DateTimeCaster())]`

Example:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Data;
use DateTimeImmutable;

class Order extends Data
{
    public function __construct(
        #[CastWith(new DateTimeImmutableCaster())] // [!code --]
        #[CastWith(DateTimeImmutableCaster::class)] // [!code ++]
        public DateTimeImmutable $placedAt,
    ) {}
}
```

## 4. Hidden on the class

`Hidden` can now be applied at class-level (in addition to property-level) to exclude properties from serialization.

```php
use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\Data;

#[Hidden] // [!code ++]
class InternalOnly extends Data
{
    public function __construct(
        public string $token,
        public string $secret,
    ) {}
}

// toArray() / toJson() will exclude properties when Hidden applies
```

## 5. New attributes

All attributes live under `Alamellama\Carapace\Attributes`.

- `ConvertEmptyToNull` — converts empty string `""` or empty array `[]` to `null` during pre-hydration when the property type allows null. Can be applied at class or property level.
- `EnumSerialize` — controls enum serialization. Strategies: `EnumSerialize::VALUE` (default for backed enums) or `EnumSerialize::NAME`, or provide `method: '...'` to call a custom instance method.
- `GroupFrom` — collects multiple flat input keys into a grouped structure for a property.
- `SnakeCase` — maps snake_case input keys to camelCase properties on hydration and outputs snake_case keys on serialization. Can be applied at class or property level.

## 6. Support\\Data helper

Attributes now operate on a lightweight helper: `Alamellama\Carapace\Support\Data`.
It abstracts access to the incoming payload (array/object), with methods like:

- `wrap(string|array|object $data): Support\Data`
- `has(string $key): bool`
- `get(string $key): mixed`
- `set(string $key, mixed $value): void`
- `unset(string $key): void`
- `items(): array` for collections

Use it inside attribute handlers instead of accessing raw arrays/objects.

## 7. Examples end-to-end

```php
use Alamellama\Carapace\Attributes\{CastWith, SnakeCase, ConvertEmptyToNull, EnumSerialize, Hidden};
use Alamellama\Carapace\Data;

#[SnakeCase]
class User extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        #[EnumSerialize(EnumSerialize::NAME)]
        public Status $status,
        #[ConvertEmptyToNull]
        public ?string $nickname,
    ) {}
}

$user = User::from([
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'status' => Status::ACTIVE,
    'nickname' => '', // becomes null
]);

$user->toArray(); // ['first_name' => 'Jane', 'last_name' => 'Doe', 'status' => 'ACTIVE', 'nickname' => null]
```

> [!WARNING]
> If you spot gaps in this guide, please open an issue or a PR.
