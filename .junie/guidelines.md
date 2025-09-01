# Carapace Development Guidelines

This document provides essential information for developers working on the Carapace project.

## Build and Configuration

### Requirements

- PHP 8.2 or higher
- Composer

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd carapace

# Install dependencies
composer install
```

## Testing

Carapace uses [Pest PHP](https://pestphp.com/) for testing, which is a testing framework built on top of PHPUnit with an expressive, fluent syntax.

### Running Tests

```bash
# Run all tests
composer test

# Run only unit tests
composer test:unit

# Run tests with coverage report
./vendor/bin/pest --coverage
```

### Test Structure

- Tests are located in the `tests/` directory
- Unit tests are in `tests/Unit/`
- Architecture tests are in `tests/Arch/`
- Test fixtures (sample DTOs) are in `tests/Fixtures/DTO/`

### Writing Tests

Tests use Pest's functional style with `it()` functions rather than class-based PHPUnit tests:

```php
<?php

declare(strict_types=1);

namespace Tests\Demo;

use Alamellama\Carapace\Data;

class SimpleDTO extends Data
{
    public function __construct(
        public string $name,
        public int $value,
    ) {}
}

it('can create a simple DTO', function (): void {
    $dto = SimpleDTO::from([
        'name' => 'Test DTO',
        'value' => 42,
    ]);

    expect($dto)
        ->toBeInstanceOf(SimpleDTO::class)
        ->name->toBe('Test DTO')
        ->value->toBe(42);
});
```

## Code Quality Tools

Carapace uses several tools to maintain code quality:

### PHPStan (Static Analysis)

PHPStan is configured at the maximum strictness level for the `src/` directory.

```bash
# Run PHPStan
composer test:types
```

### Laravel Pint (Code Style)

Pint enforces a consistent code style based on a customized Laravel preset.

```bash
# Check code style
composer test:lint

# Fix code style issues
composer lint
```

### Rector (Automated Refactoring)

Rector automatically refactors code to follow best practices and modern PHP patterns.

```bash
# Check for potential refactorings
composer test:refactor

# Apply refactorings
composer refactor
```

### All Quality Checks

```bash
# Run all code quality checks
composer test
```

### Before completing tasks
```bash
composer fix && composer test
```

## Development Workflow

1. Make your changes in a feature branch
2. Run code quality tools before committing:
   ```bash
   composer fix  # Apply all automated fixes (Rector + Pint)
   composer test # Run all tests and quality checks
   ```
3. Ensure all tests pass and code meets quality standards
   - All tests must pass with 100% code coverage
   - Fix any issues reported by PHPStan, Pint, Rector, or Peck
   - Running `composer fix` followed by `composer test` is mandatory before submitting code
4. Submit a pull request

## Code Style Guidelines

- Strict types must be declared in all files
- Follow PSR-12 coding standards with Laravel-style modifications
- Class elements should follow the order defined in `pint.json`
- Use type declarations for all properties, parameters, and return types
- All customer-facing functions must have PHP doc blocks inforation about usage

## Architecture Guidelines

- Use attributes for mapping and casting
- Keep the library framework-agnostic
- Aim for 100% test coverage

## Documentation Guidelines

- Any new feature or update must be documented
- Documentation should be added in the `docs/` directory
- Each attribute should have its own markdown file in `docs/attributes/`
- Advanced features should be documented in `docs/advanced/`
- Include practical examples in all documentation
- Update the main README.md when adding significant features