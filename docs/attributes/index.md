# Attributes

Carapace uses PHP attributes to provide powerful functionality with minimal boilerplate. This section covers the available attributes and how to use them.

> [!note]
> Each attribute page starts with badges indicating where it applies (Class, Property, or both) and in which pipeline stage it runs (Pre-hydration, Hydration, Serialization).

- [CastWith](./cast-with.md) - Cast values during hydration
- [MapFrom](./map-from.md) - Map properties from custom input keys
- [MapTo](./map-to.md) - Control output keys during serialization
- [Hidden](./hidden.md) - Exclude properties from serialization (class or property)
- [ConvertEmptyToNull](./convert-empty-to-null.md) - Convert empty strings/arrays to null during pre-hydration
- [EnumSerialize](./enum-serialize.md) - Control how enums are serialized (value, name, or a custom method)
- [GroupFrom](./group-from.md) - Group multiple input keys into one array property
- [SnakeCase](./snake-case.md) - Serialize property names as snake_case
