# Custom Casters

Carapace ships with several built‑in casters and also lets you provide your own by implementing `CasterInterface`.
Custom casters are used via the CastWith attribute during the hydration stage.

## Creating a Custom Caster

Implement the `CasterInterface` from `Alamellama\Carapace\Contracts`.

```php
use Alamellama\Carapace\Contracts\CasterInterface;

class MyCaster implements CasterInterface
{
    public function cast(mixed $value): mixed
    {
        // Validate input and transform
        if ($value === null) {
            // Let CastWith handle nulls based on property type; usually return null as-is
            return null;
        }

        // ... your casting logic
        $transformed = $value; // replace with real logic

        return $transformed;
    }
}
```
> [!tip]
> Throw `InvalidArgumentException` for unsupported inputs. CastWith will surface helpful error messages.

## Example: Carbon date caster

Here's an example of a custom caster for Carbon dates:

```php
use Alamellama\Carapace\Contracts\CasterInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use InvalidArgumentException;

final class CarbonCaster implements CasterInterface
{
    public function __construct(
        private string $format = 'Y-m-d H:i:s'
    ) {}

    public function cast(mixed $value): CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value)) {
            // Try explicit format first
            $carbon = Carbon::createFromFormat($this->format, $value);
            if ($carbon !== false) {
                return $carbon;
            }

            // Fallback to flexible parser
            return Carbon::parse($value);
        }

        if (is_int($value)) {
            return Carbon::createFromTimestamp($value);
        }

        throw new InvalidArgumentException('Cannot cast to Carbon: unsupported type ' . gettype($value));
    }
}
```

## Using your caster with CastWith

CastWith accepts one of the following:
- A DTO class‑string (e.g. User::class) — arrays/objects/JSON will be cast to that DTO, lists to arrays of DTOs
- A caster class‑string (e.g. PrimitiveCaster::class) — CastWith will instantiate it with no arguments
- A caster instance (e.g. new PrimitiveCaster('int'))

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Data;
use Carbon\CarbonInterface;

final class Event extends Data
{
    public function __construct(
        public string $name,

        // Pass an instance when your caster has constructor args
        #[CastWith(new CarbonCaster('Y-m-d'))]
        public CarbonInterface $date,
    ) {}
}

$event = Event::from([
    'name' => 'Conference',
    'date' => '2025-08-15', // Will be cast to Carbon
]);
```