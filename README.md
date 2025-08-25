<h1 align="center">Carapace</h1>
<p align="center">
    <img alt="Packagist License" src="https://img.shields.io/packagist/l/ALameLlama/carapace">
    <img alt="Packagist Version" src="https://img.shields.io/packagist/v/ALameLlama/carapace">
    <img alt="Packagist Stars" src="https://img.shields.io/packagist/stars/ALameLlama/carapace">
    <img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/ALameLlama/carapace">
</p>

Carapace is a lightweight PHP library for building immutable, strictly typed Data Transfer Objects.
It leverages PHP attributes for casting, property mapping,
and serialization, while providing a simple, expressive API.

## Features

- **Immutable DTOs**: Define immutable data objects with constructor promotion
- **Attribute-Driven Mapping**: Use attributes for casting, mapping, and serialization
- **Strictly Typed**: Leverage PHP's type system for predictable data structures
- **Framework-Agnostic**: Works in Laravel, Symfony, or plain PHP projects
- **Simple API**: Create, hydrate, and transform DTOs with minimal boilerplate

## Installation

```bash
composer require alamellama/carapace
```

## Usage

```php
use Alamellama\Carapace\ImmutableDTO;
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Attributes\Hidden;

class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[MapFrom('email_address')]
        
        public string $email,
        
        #[Hidden]
        public string $password,
        
        #[CastWith(Address::class)]
        public Address $address,
    ) {}
}

// Create from an array
$user = User::from([
    'name' => 'John Doe',
    'email_address' => 'john@example.com',
    'password' => 'secret',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Anytown',
    ],
]);

// Create a modified copy
$updatedUser = $user->with(name: 'Jane Doe');

// Serialize
$array = $user->toArray(); // Password will be excluded
$json = $user->toJson();
```

## Documentation

For detailed documentation, visit our [documentation site](https://alamellama.github.io/carapace/).

## Coding Style

Carapace follows PSR-12 coding standards with Laravel-style modifications. We use Laravel Pint for code style enforcement and Rector for automated refactoring.

```bash
composer fix
```

## Testing

Carapace uses Pest PHP for testing and aims for 100% test coverage.

```bash
# Run all tests
composer test
```

## Acknowledgements & Inspirations

In particular, drew inspiration and ideas from:

- Spatie's data libraries, including [spatie/data-transfer-object](https://github.com/spatie/data-transfer-object) and [spatie/laravel-data](https://github.com/spatie/laravel-data)
- [CuyZ/Valinor](https://github.com/CuyZ/Valinor)
- [Symfony Serializer](https://symfony.com/doc/current/components/serializer.html)

We also rely on fantastic tooling that keeps this project reliable and maintainable:

- [Pest PHP](https://pestphp.com/) for testing
- [PHPStan](https://phpstan.org/) for static analysis
- [Laravel Pint](https://laravel.com/docs/pint) for code style
- [Rector](https://github.com/rectorphp/rector) for automated refactoring

## License

Carapace is open-sourced software licensed under the MIT license.
