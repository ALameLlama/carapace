# MapFrom

The `MapFrom` attribute allows you to map a DTO property from a different input key.

## Basic Usage

```php
use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\ImmutableDTO;

final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        
        #[MapFrom('email_address')]
        public string $email,
    ) {}
}
```

```php
$user = User::from([
    'name' => 'John Doe',
    'email_address' => 'john.doe@example.com', // Will be mapped to $email
]);
```

In this example, the `email` property will be populated from the `email_address` key in the input array.

## Multiple Sources

You can specify multiple source keys in a single `MapFrom` attribute:

```php
final class UserProfile extends ImmutableDTO
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
final class UserProfile extends ImmutableDTO
{
    public function __construct(
        #[MapFrom('display_name')]
        #[MapFrom('user_name')]
        public string $username,
    ) {}
}
```
