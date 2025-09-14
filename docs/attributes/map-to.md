<AttributeBadges scope="property" stage="serialization" />

# MapTo

`MapTo` allows you to customize the output key for a property during serialization.

## Basic Usage

```php
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Data;

class User extends Data
{
    public function __construct(
        #[MapTo('full_name')]
        public string $name,

        #[MapTo('email_address')]
        public string $email,
    ) {}
}

$user = User::from([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);

$user->toArray();
// [
//    'full_name' => 'John Doe',
//    'email_address' => 'john.doe@example.com',
// ]
```
