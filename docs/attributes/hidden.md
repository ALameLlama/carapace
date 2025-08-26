# Hidden

The `Hidden` attribute allows you to exclude specific properties from serialization.

## Basic Usage

```php
use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\Data;

readonly class User extends Data
{
    public function __construct(
        public string $name,
        public string $email,

        #[Hidden]
        public string $password,
    ) {}
}
```

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'secret123',
]);

print_r($user->toArray());
```

```php
// Output:
[
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    // password is excluded from serialization
]
```

The `Hidden` attribute works with both `toArray()` and `toJson()` methods, ensuring sensitive data is never included in serialized output.
