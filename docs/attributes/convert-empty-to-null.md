<AttributeBadges scope="both" stage="hydration" />

# ConvertEmptyToNull

`ConvertEmptyToNull` converts empty values to null. This is useful when APIs return empty strings or empty arrays for optional fields.

## What is considered "empty"?

- Empty string: ""
- Empty array: []

> [!IMPORTANT]
> Internally this runs PHP's `empty()` function to see if it should be set to null but excludes `bool` values

## Usage

```php
use Alamellama\Carapace\Attributes\ConvertEmptyToNull;
use Alamellama\Carapace\Data;

class Profile extends Data
{
    public function __construct(
        #[ConvertEmptyToNull]
        public ?string $bio,

        #[ConvertEmptyToNull]
        public ?array $tags,
    ) {}
}

// Or on the class
#[ConvertEmptyToNull]
class Profile extends Data
{
    public function __construct(
        public ?string $bio,
        public ?array $tags,
    ) {}
}

```


```php
$profile = Profile::from([
    'bio' => '',
    'tags' => [],
]);

echo $profile->bio // Outputs: null
echo $profile->tags // Outputs: null
```

