<AttributeBadges scope="both" stage="serialization" />

# SnakeCase

`SnakeCase` converts property names to snake_case during serialization.

## Usage

```php
use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;

#[SnakeCase]
class UserProfile extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phoneNumber,
    ) {}
}

$user = UserProfile::from([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'phoneNumber' => '123',
]);

$user->toArray();
// [
//   'first_name' => 'John',
//   'last_name' => 'Doe',
//   'phone_number' => '123',
// ]
```

You can also apply to a single property:

```php
class Item extends Data
{
    public function __construct(
        #[SnakeCase]
        public string $displayName,
    ) {}
}
```