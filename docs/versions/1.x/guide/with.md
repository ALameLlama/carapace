# Immutably Updating DTOs

The `with` method allows you to create modified copies of DTOs while preserving the original instance.

## Basic Usage

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Using an array of overrides
$updatedUser = $user->with(['name' => 'Jane Doe']);

// Or using named arguments
$updatedUser = $user->with(name: 'Jane Doe');

echo $user->name;        // Outputs: John Doe
echo $updatedUser->name; // Outputs: Jane Doe
```

## Multiple Property Updates

You can update multiple properties at once:

```php
// Using an array
$updatedUser = $user->with([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
]);

// Or using named arguments
$updatedUser = $user->with(
    name: 'Jane Doe',
    email: 'jane@example.com',
);
```

## Updating Nested DTOs

When working with nested DTOs, you need to provide the complete nested structure:

```php
$user = User::from([
    'name' => 'John Doe',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Anytown',
    ],
]);

// Update the nested address
$updatedUser = $user->with([
    'address' => [
        'street' => '456 Oak Ave',
        'city' => 'Newtown',
    ],
]);

// Or update just the parent property
$updatedUser = $user->with(
    name: 'Jane Doe',
);

// The original DTO remains unchanged
echo $user->name;                // Outputs: John Doe
echo $user->address->street;     // Outputs: 123 Main St
echo $updatedUser->address->city; // Outputs: Newtown (if address was updated)
```
