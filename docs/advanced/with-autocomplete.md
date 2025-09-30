# With Method Autocomplete

The `with()` method in Carapace DTOs allows you to create a new instance with modified properties.

## How It Works

The autocomplete support is provided through PHPDoc annotations. Which allows IDEs to provide autocomplete suggestions when using the `with()` method.

For example, if you have a DTO with properties `name`, `email`, and `address`, you can use the `with()` method like this:

```php
/**
 * @method self with(array $overrides = [], string $name = null, string $email = null, Address $address = null)
 */
class User extends Data
{
    public function __construct(
        public string $name,
        public string $email,

        #[CastWith(Address::class)]
        public Address $address,
    ) {}
}

$dto = User::from([
    'name' => 'Nick',
    'email' => 'nick@example.com',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Melbourne',
        'postcode' => '3000',
    ],
]);

// Your IDE will provide autocomplete for these properties
$dto2 = $dto->with(name: 'Nicholas', email: 'nicholas@example.com');
```

> [!WARNING]
> In the future, I am hoping to add a script to generate this PHPDoc annotation automatically. similar to laravel/ide-helper.