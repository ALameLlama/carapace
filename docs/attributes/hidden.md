# Hidden

The `Hidden` attribute allows you to exclude properties from serialization.

## Basic Usage (property-level)

```php
use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\Data;

class User extends Data
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

## Class-level

You can hide all properties by default at the class level and then selectively expose with other attributes if supported:

```php
#[Hidden]
class SecureUser extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}

$secure = SecureUser::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'secret123',
]);

$secure->toArray();
// []
```

The `Hidden` attribute works with both `toArray()` and `toJson()` methods, ensuring sensitive data is never included in serialized output.
