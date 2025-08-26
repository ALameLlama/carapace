# MapTo

The `MapTo` attribute allows you to customize the output key for a property during serialization.

## Basic Usage

```php
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Data;

readonly class User extends Data
{
    public function __construct(
        #[MapTo('full_name')]
        public string $name,

        #[MapTo('email_address')]
        public string $email,
    ) {}
}
```

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);

print_r($user->toArray());
```

```php
// Output:
[
    'full_name' => 'John Doe',
    'email_address' => 'john.doe@example.com',
]
```
