# With Method Autocomplete

The `with()` method in Carapace DTOs allows you to create a new instance with modified properties. To improve developer experience, Carapace provides autocomplete support for the `with()` method.

## How It Works

The autocomplete support is provided through PHPDoc annotations that are generated for each DTO class. These annotations include all the properties of the DTO with their types, which allows IDEs to provide autocomplete suggestions when using the `with()` method.

For example, if you have a DTO with properties `name`, `email`, and `address`, you can use the `with()` method like this:

```php
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

## Generating Autocomplete Annotations

Carapace provides a command to generate autocomplete annotations for all DTO classes in your project. To use it, run:

```bash
composer generate-with-doc
```

By default, this command will scan all DTO classes in the `src/` directory with the namespace `Alamellama\Carapace`. If you want to scan a different directory or namespace, you can use the following options:

```bash
composer generate-with-doc -- -d /path/to/your/dtos -n Your\\Namespace
```

Or you can run the script directly:

```bash
php bin/generate-with-doc.php -d /path/to/your/dtos -n Your\\Namespace
```

## How to Use

After generating the autocomplete annotations, your IDE should provide autocomplete suggestions when using the `with()` method. This works with both named parameters and array parameters:

```php
// Using named parameters
$dto2 = $dto->with(name: 'Nicholas', email: 'nicholas@example.com');

// Using an array
$dto2 = $dto->with(['name' => 'Nicholas', 'email' => 'nicholas@example.com']);

// Using both
$dto2 = $dto->with(['name' => 'Nicholas'], email: 'nicholas@example.com');
```

## Regenerating Annotations

If you add or modify properties in your DTO classes, you'll need to regenerate the autocomplete annotations. Simply run the `generate-with-doc` command again:

```bash
composer generate-with-doc
```

This will update the annotations for all DTO classes in your project.