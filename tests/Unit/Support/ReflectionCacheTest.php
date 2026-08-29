<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Contracts\AttributeInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\ReflectionCache;
use Attribute;
use ReflectionAttribute;
use ReflectionParameter;
use ReflectionProperty;

final class CachedDescriptorFixture
{
    private readonly string $privateValue;

    public function __construct(
        #[MapFrom('value')]
        public string $name,
        string $unpromoted,
    ) {
        $this->privateValue = strtoupper($unpromoted);
    }

    public function privateValue(): string
    {
        return $this->privateValue;
    }
}

final class NoConstructorFixture {}

interface InheritedAttributeFixture extends AttributeInterface {}

#[Attribute(Attribute::TARGET_CLASS)]
final class ParentAttributeFixture implements InheritedAttributeFixture {}

#[Attribute(Attribute::TARGET_CLASS)]
final class ChildClassAttributeFixture implements InheritedAttributeFixture {}

#[Attribute(Attribute::TARGET_CLASS)]
final class UnrelatedClassAttributeFixture {}

#[ParentAttributeFixture]
class AttributedParentFixture {}

#[ChildClassAttributeFixture]
#[UnrelatedClassAttributeFixture]
final class AttributedChildFixture extends AttributedParentFixture {}

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExactAttributeFixture {}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ChildAttributeFixture extends ExactAttributeFixture {}

final class AttributeLookupFixture
{
    #[MapFrom('mapped')]
    #[ChildAttributeFixture]
    public string $value;
}

it('reflects class strings and objects using the same cached descriptor', function (): void {
    $object = new CachedDescriptorFixture('name', 'unpromoted');

    expect(ReflectionCache::reflection(CachedDescriptorFixture::class))
        ->toBe(ReflectionCache::reflection($object))
        ->and(ReflectionCache::reflection($object)->getName())->toBe(CachedDescriptorFixture::class)
        ->and($object->privateValue())->toBe('UNPROMOTED');
});

it('returns all properties while public properties are filtered', function (): void {
    $class = CachedDescriptorFixture::class;
    $properties = ReflectionCache::properties($class);
    $publicProperties = ReflectionCache::publicProperties($class);

    expect(array_map(static fn (ReflectionProperty $property): string => $property->getName(), $properties))
        ->toEqualCanonicalizing(['name', 'privateValue'])
        ->and(array_map(static fn (ReflectionProperty $property): string => $property->getName(), $publicProperties))->toBe(['name'])
        ->and($properties[0])->toBe(ReflectionCache::properties($class)[0])
        ->and($publicProperties[0])->toBe(ReflectionCache::publicProperties($class)[0]);
});

it('returns cached constructor parameters and handles classes without constructors', function (): void {
    $parameters = ReflectionCache::constructorParameters(CachedDescriptorFixture::class);

    expect(array_map(static fn (ReflectionParameter $parameter): string => $parameter->getName(), $parameters))->toBe(['name', 'unpromoted'])
        ->and($parameters[0])->toBe(ReflectionCache::constructorParameters(CachedDescriptorFixture::class)[0])
        ->and(ReflectionCache::constructorParameters(NoConstructorFixture::class))->toBe([]);
});

it('maps constructor parameters to properties when one exists', function (): void {
    $property = ReflectionCache::parameterProperty(CachedDescriptorFixture::class, 'name');

    expect($property?->getName())->toBe('name')
        ->and(ReflectionCache::parameterProperty(CachedDescriptorFixture::class, 'name'))->toBe($property)
        ->and(ReflectionCache::parameterProperty(CachedDescriptorFixture::class, 'unpromoted'))->toBeNull()
        ->and(ReflectionCache::parameterProperty(CachedDescriptorFixture::class, 'missing'))->toBeNull();
});

it('returns cached inherited class attributes filtered by interface', function (): void {
    $attributes = ReflectionCache::parentAttributes(AttributedChildFixture::class, InheritedAttributeFixture::class);

    expect(array_map(static fn (ReflectionAttribute $attribute): string => $attribute->getName(), $attributes))
        ->toBe([ChildClassAttributeFixture::class, ParentAttributeFixture::class])
        ->and($attributes[0])->toBe(ReflectionCache::parentAttributes(AttributedChildFixture::class, InheritedAttributeFixture::class)[0]);
});

it('returns cached property attributes filtered by interface', function (): void {
    $property = ReflectionCache::properties(AttributeLookupFixture::class)[0];
    $attributes = ReflectionCache::propertyAttributes($property, PropertyPreHydrationInterface::class);

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->getName())->toBe(MapFrom::class)
        ->and($attributes[0])->toBe(ReflectionCache::propertyAttributes($property, PropertyPreHydrationInterface::class)[0]);
});

it('matches property attribute names exactly', function (): void {
    $property = ReflectionCache::properties(AttributeLookupFixture::class)[0];
    expect(ReflectionCache::propertyHasAttribute($property, ChildAttributeFixture::class))->toBeTrue()
        ->and(ReflectionCache::propertyHasAttribute($property, ExactAttributeFixture::class))->toBeFalse();
});
