# Getting Started

## Introduction

Carapace is a lightweight PHP library for building strictly typed Data Transfer Objects (DTOs).

It leverages PHP attributes for casting, property mapping, and serialization, while providing a simple, expressive API.

## Features

### DTO Base Classes

- Extend `Alamellama\Carapace\Data` for standard DTOs.
- Extend `Alamellama\Carapace\ImmutableData` for readonly DTOs.

### Attribute-Driven Mapping

- **`CastWith`**
  - Accepts a DTO class-string, a caster class-string, or a caster instance
  - Supports nested DTOs and collections, primitives, enums, and custom types
- **`MapFrom`**
  - Map properties from custom keys in the input array
- **`MapTo`**
  - Control output keys when serializing the DTO
- **`Hidden`**
  - Exclude properties from serialization (can be used on the class or property)
- **`ConvertEmptyToNull`**
  -  Convert empty strings/arrays to null during pre-hydration
- **`EnumSerialize`**
  - Control how enums are serialized (value vs name) or custom methods 
- **`GroupFrom`**
  - Group multiple input keys into one array property
- **`SnakeCase`**
  - Serialize property names as snake_case (`MapFrom` and `MapTo`)

### Serialization

- Convert DTOs to arrays or JSON using built-in methods
- Supports deep, recursive serialization of nested DTOs

## Installation

```bash
composer require alamellama/carapace
```

## Requirements

- PHP 8.2 or higher
