<AttributeBadges scope="property" stage="hydration" />

# MapFrom

`MapFrom` allows you to map a DTO property from a different input key.

## Basic Usage

```php
use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Data;

class User extends Data
{
    public function __construct(
        public string $name,

        #[MapFrom('email_address')]
        public string $email,
    ) {}
}

$user = User::from([
    'name' => 'John Doe',
    'email_address' => 'john.doe@example.com',
]);

echo $user->email; // Outputs: john.doe@example.com
```

## Multiple Sources

You can specify multiple source keys in a single `MapFrom` attribute:

```php
class UserProfile extends Data
{
    public function __construct(
        public string $name,

        #[MapFrom('email_address', 'contact_email')]
        public string $email,
    ) {}
}
```

You can also use multiple `MapFrom` attributes on the same property:

```php
class UserProfile extends Data
{
    public function __construct(
        #[MapFrom('display_name')]
        #[MapFrom('user_name')]
        public string $username,
    ) {}
}
```
