<AttributeBadges scope="both" stage="serialization" />

# Hidden

`Hidden` allows you to exclude properties from serialization.

## Basic Usage

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

$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'secret123',
]);

$user->toArray();
// [
//     'name' => 'John Doe',
//     'email' => 'john.doe@example.com',
//     // password is excluded from serialization
// ]
```

You can hide all properties by default at the class level.

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
