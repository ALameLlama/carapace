# Custom Attributes

Carapace lets you create your own attributes that plug into the data pipeline at different stages. You can target:

- Pre-hydration: shape/prepare incoming data before constructor args are read
- Hydration: validate/adjust during hydration
- Transformation: customize how data is serialized to arrays

## Pre-hydration

Use `PropertyPreHydrationInterface` or `ClassPreHydrationInterface` to modify input data prior to hydration.
This is where `CastWith`, `MapFrom`, and `ConvertEmptyToNull` operate.

```php
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Trim implements PropertyPreHydrationInterface
{
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $name = $property->getName();
        if (! $data->has($name)) {
            return;
        }

        $value = $data->get($name);
        if (is_string($value)) {
            $data->set($name, trim($value));
        }
    }
}
```

usage:

```php
use App\Attributes\Trim;
use Alamellama\Carapace\Data;

class User extends Data
{
    public function __construct(
        #[Trim]
        public string $name,
    ) {}
}

$user = User::from(['name' => '  Ada  ']);
// $user->name === 'Ada'
```

> [!tip]
> For class-wide behavior across all properties, implement ClassPreHydrationInterface instead and iterate over properties.

## Hydration

Hydration hooks run between argument gathering and object creation. Implement `PropertyHydrationInterface` or `ClassHydrationInterface` to validate or normalize. This is rarely needed but provided for completeness.

```php
use Alamellama\Carapace\Contracts\PropertyHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;
use InvalidArgumentException;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidateIPv4 implements PropertyHydrationInterface
{
    public function propertyHydrate(ReflectionProperty $property, Data $data): void
    {
        $name = $property->getName();
        if (! $data->has($name)) {
            return;
        }

        $value = $data->get($name);
        if (! is_string($value) || filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            throw new InvalidArgumentException("{$name} must be a valid IPv4 address");
        }
    }
}
```

Usage:

```php
use App\Attributes\ValidateIPv4;
use Alamellama\Carapace\Data;

class Server extends Data
{
    public function __construct(
        #[ValidateIPv4]
        public string $ip,
    ) {}
}
```

## Transformation

To customize output during serialization, implement `PropertyTransformationInterface` or `ClassTransformationInterface`.
Examples in-core include `MapTo`, `Hidden`, `SnakeCase`, and `EnumSerialize`.

```php
use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Uppercase implements PropertyTransformationInterface
{
    /** @return array{string, mixed} */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        $key = $property->getName();
        if (is_string($value)) {
            return [$key, mb_strtoupper($value)];
        }

        return [$key, $value];
    }
}
```

Usage:

```php
use App\Attributes\Uppercase;
use Alamellama\Carapace\Data;

final class Book extends Data
{
    public function __construct(
        #[Uppercase]
        public string $title,
    ) {}
}

$book = Book::from(['title' => 'refactoring']);
$book->toArray();
// [ 'title' => 'REFACTORING' ]
```

## Stages at a glance

- `Pre-hydration`: PropertyPreHydrationInterface, ClassPreHydrationInterface
- `Hydration`: PropertyHydrationInterface, ClassHydrationInterface
- `Transformation`: PropertyTransformationInterface, ClassTransformationInterface
