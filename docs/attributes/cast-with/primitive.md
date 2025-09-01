# Primitive Type Casting

Cast values to primitive types using the `PrimitiveCaster`:

```php
use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\PrimitiveCaster;

class Product extends Data
{
    public function __construct(
        public string $name,

        #[CastWith(new PrimitiveCaster('int'))]
        public int $price,

        #[CastWith(new PrimitiveCaster('bool'))]
        public bool $inStock,

        #[CastWith(new PrimitiveCaster('array'))]
        public array $tags,
    ) {}
}
```

```php
$product = Product::from([
    'name' => 'Awesome Product',
    'price' => '1299', // String will be cast to int
    'inStock' => 1,    // Integer will be cast to bool
    'tags' => "[\"sale\", \"new\"]", // JSON string will be cast to array
]);

// You can also pass scalar values to be wrapped in an array
$product = Product::from([
    'name' => 'Awesome Product',
    'price' => 1299,
    'inStock' => true,
    'tags' => 'featured', // Will become ['featured']
]);
```

The `PrimitiveCaster` supports the following types:

- `int`: Casts to integer
- `float`: Casts to float
- `string`: Casts to string
- `bool`: Casts to boolean
- `array`: Handles multiple scenarios:
  - Keeps arrays as-is
  - Converts JSON strings to arrays
  - Converts objects to arrays
  - Wraps scalar values in an array
