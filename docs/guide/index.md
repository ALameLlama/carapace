# Getting Started

## Introduction

Carapace is a lightweight PHP library for building immutable, strictly typed Data Transfer Objects (DTOs).

It leverages PHP attributes for casting, property mapping, and serialization, while providing a simple, expressive API.

## Features

### Immutable DTOs

- Define immutable data objects by extending the `ImmutableDTO` base class.
- Properties are initialized via constructor promotion.
- Enforces strict immutability for data integrity.

### Attribute-Driven Mapping

- **`CastWith`**  
  Automatically casts values during hydration:
  - Nested DTOs and collections
  - Primitive types (int, float, string, bool, array)
  - PHP enums (backed and unit enums)
  - Custom types via `CasterInterface`
- **`MapFrom`**  
  Maps properties from custom keys in the input array.
- **`MapTo`**  
  Controls output keys when serializing the DTO.
- **`Hidden`**  
  Excludes properties from serialization output.

### Serialization

- Convert DTOs to arrays or JSON using built-in methods.
- Supports deep, recursive serialization of nested DTOs.

## Installation

```bash
composer require alamellama/carapace
```

## Requirements

- PHP 8.2 or higher
