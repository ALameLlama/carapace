---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "Carapace"
  text: "Framework-agnostic DTOs for PHP"
  tagline: Lightweight, immutable, strictly typed Data Transfer Objects with attribute-driven mapping
  actions:
    - theme: brand
      text: Get Started
      link: /guide/
    - theme: alt
      text: View on GitHub
      link: https://github.com/alamellama/carapace

features:
  - title: Immutable DTOs
    details: Define immutable data objects by extending the Data base class. Properties are initialized via constructor promotion.
  - title: Attribute-Driven Mapping
    details: Use PHP attributes like CastWith, MapFrom, MapTo, and Hidden to control how data is hydrated, transformed, and serialized with minimal boilerplate.
  - title: Framework-Agnostic
    details: Works in Laravel, Symfony, or plain PHP projects. Carapace is a lightweight library with no external dependencies, making it easy to integrate into any PHP 8.2+ project.
  - title: Strictly Typed
    details: Leverage PHP's type system to create well-defined, predictable data structures. Carapace encourages type safety.
  - title: Simple, Expressive API
    details: Create, hydrate, and transform DTOs with a clean, intuitive API. Carapace makes working with DTOs a pleasure, reducing boilerplate and increasing productivity.
  - title: Comprehensive Documentation
    details: Detailed documentation with examples for all features. Learn how to use Carapace effectively and get the most out of your DTOs.
---
