# Contributing to Carapace

Thank you for considering contributing to Carapace! This document provides guidelines and instructions to help you contribute effectively.

## Development Setup

### Requirements

- PHP 8.2 or higher
- Composer

### Installation

```bash
# Clone the repository
git clone git@github.com:ALameLlama/carapace.git
cd carapace

# Install dependencies
composer install
```

## Development Workflow

1. Create a feature branch from the main branch
2. Make your changes
3. Run code quality tools before committing:
   ```bash
   composer fix  # Apply all automated fixes (Rector + Pint)
   composer test # Run all tests and quality checks
   ```
4. Ensure all tests pass and code meets quality standards
5. Submit a pull request

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

### Peck (Typo Checking)

Peck checks for typos in your code.

```bash
# Check for typos
composer test:typos
```

### All Quality Checks

```bash
# Run all code quality checks
composer test
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

readonly class SimpleDTO extends Data
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

## Code Style Guidelines

- All classes should be final (enforced by Pint)
- Strict types must be declared in all files
- Follow PSR-12 coding standards with Laravel-style modifications
- Class elements should follow the order defined in `pint.json`
- Use type declarations for all properties, parameters, and return types
- All customer-facing functions must have PHP doc blocks information about usage

## Pull Request Process

1. Ensure your code follows the coding standards and passes all tests
2. Update documentation if necessary
3. The PR should clearly describe what changes were made and why
4. All CI checks must pass before a PR can be merged
5. PRs require approval from at least one maintainer

## Documentation

- Any new feature or update must be documented
- Documentation should be added in the `docs/` directory
- Each attribute should have its own markdown file in `docs/attributes/`
- Advanced features should be documented in `docs/advanced/`
- Include practical examples in all documentation
- Update the main README.md when adding significant features

## Release Process

Carapace uses Google's Release Please for managing releases and changelogs. When contributing:

1. Use [Conventional Commits](https://www.conventionalcommits.org/) format for your commit messages
2. Include the type of change (feat, fix, docs, etc.) in your commit message
3. Breaking changes should be noted with `BREAKING CHANGE:` in the commit message

## Questions?

If you have any questions or need help with your contribution, please open an issue on GitHub.

Thank you for contributing to Carapace!