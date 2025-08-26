# Custom Casters

While Carapace provides several built-in casters, you can also create your own custom casters by implementing the `CasterInterface`. This allows you to handle specialized data types or complex conversion logic.

## Creating a Custom Caster

To create a custom caster, implement the `CasterInterface` which requires a single `cast` method:

```php
use Alamellama\Carapace\Contracts\CasterInterface;

class MyCaster implements CasterInterface
{
    public function cast(mixed $value): mixed
    {
        // Your casting logic here
        return $transformedValue;
    }
}
```

## Example: Carbon Date Caster

Here's an example of a custom caster for Carbon dates (a popular date/time library for PHP):

```php
use Alamellama\Carapace\Contracts\CasterInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;

class CarbonCaster implements CasterInterface
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
            try {
                $carbon = Carbon::createFromFormat($this->format, $value);
                if ($carbon !== false) {
                    return $carbon;
                }
            } catch (\Exception $e) {
                // If format parsing fails, try the flexible parser
                return Carbon::parse($value);
            }
        }

        if (is_int($value)) {
            return Carbon::createFromTimestamp($value);
        }

        throw new \InvalidArgumentException('Cannot cast to Carbon: unsupported type ' . gettype($value));
    }
}
```

## Using Custom Casters

Use your custom caster with the `CastWith` attribute:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Data;

class Event extends Data
{
    public function __construct(
        public string $name,

        #[CastWith(new CarbonCaster('Y-m-d'))]
        public CarbonInterface $date,
    ) {}
}

$event = Event::from([
    'name' => 'Conference',
    'date' => '2025-08-15', // Will be cast to Carbon
]);
```
