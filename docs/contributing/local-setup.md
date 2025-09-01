# Local Development Setup

This guide will help you set up Carapace for local development.

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)
- [NPM](https://www.npmjs.com/) (for documentation)

## Clone the Repository

```bash
# Clone the repository
git clone https://github.com/alamellama/carapace.git
cd carapace
```

## Install Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```

Install documentation dependencies using Yarn:

```bash
npm install
```

## Code Quality Tools

Carapace uses several tools to maintain code quality:

### Running All Tests and Checks

To run all tests and code quality checks:

```bash
composer test
```

This will run:

- PHPUnit/Pest tests
- PHPStan static analysis
- Laravel Pint code style checks
- Rector code quality checks

### Individual Tools

You can also run each tool individually:

```bash
# Run only unit tests
composer test:unit

# Run static analysis
composer test:types

# Check code style
composer test:lint

# Check for potential refactorings
composer test:refactor
```

## Fixing Code Style Issues

To automatically fix code style issues:

```bash
# Fix code style issues with Pint
composer lint

# Apply automated refactorings with Rector
composer refactor

# Run both tools
composer fix
```

## Building Documentation

The documentation is built using [VitePress](https://vitepress.dev/). To work with the documentation:

```bash
# Start a local development server
npm run docs:dev

# Build the documentation
npm run docs:build

# Preview the built documentation
npm run docs:preview
```

When running `npm run docs:dev`,
you can access the documentation at `http://localhost:5173/` and see your changes in real-time.

## Workflow

A typical workflow for contributing to Carapace:

1. Create a new branch for your feature or bugfix
2. Make your changes
3. Run tests and code quality checks: `composer test`
4. Fix any issues: `composer fix`
5. Run tests again to ensure fixes didn't introduce new issues
6. Submit a pull request
