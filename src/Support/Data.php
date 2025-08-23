<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Support;

use const JSON_THROW_ON_ERROR;

use Throwable;
use Traversable;

use function array_key_exists;
use function get_object_vars;
use function is_array;
use function is_object;
use function is_string;
use function json_decode;
use function method_exists;
use function property_exists;

/**
 * Unified accessor for array/object/model-like data sources.
 *
 * - JSON strings: wrap() accepts a JSON string and decodes it to use as an array (without mutating the caller).
 *
 * - Arrays: has() uses array_key_exists
 *           get() returns value.
 *
 * - Objects: has() prefers property_exists, then any magic-based existence check provided by the object, then a standard existence check, and finally __get() as a fallback.
 *            get() prefers direct property access or __get if available.
 *
 * - Mutations: set()/unset() will affect only the internal array state;
 *              for objects, set() assigns the property on the underlying object;
 *              unset() is a no-op for objects to avoid side-effects on models.
 */
final class Data
{
    /**
     * @param  array<mixed,mixed>|object  $data
     */
    private function __construct(private array|object $data) {}

    /**
     * @param  string|array<mixed,mixed>|object  $data
     */
    public static function wrap(string|array|object $data): self
    {
        if (is_string($data)) {
            $data = (array) json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }

        return new self($data);
    }

    public static function isArrayOrObject(mixed $value): bool
    {
        return is_array($value) || is_object($value);
    }

    // TODO: see if we can use a more specific return type here using phpstan
    /**
     * @return array<mixed,mixed>|object
     */
    public function raw(): array|object
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        if (is_array($this->data)) {
            return array_key_exists($key, $this->data);
        }

        if (property_exists($this->data, $key)) {
            return true;
        }

        if (method_exists($this->data, '__isset')) {
            try {
                if ($this->data->__isset($key)) {
                    return true;
                }
            } catch (Throwable) {
                // fall through
            }
        }

        if (method_exists($this->data, '__get')) {
            try {
                $this->data->__get($key);

                return true;
            } catch (Throwable) {
                return false;
            }
        }

        return false;
    }

    public function get(string $key): mixed
    {
        if (is_array($this->data)) {
            return $this->data[$key] ?? null;
        }

        if (property_exists($this->data, $key)) {
            return $this->data->{$key};
        }

        if (method_exists($this->data, '__get')) {
            return $this->data->__get($key);
        }

        return $this->data->{$key} ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        if (is_array($this->data)) {
            $this->data[$key] = $value;

            return;
        }

        $this->data->{$key} = $value;
    }

    public function unset(string $key): void
    {
        if (is_array($this->data)) {
            unset($this->data[$key]);
        }

        // For objects/models we avoid unsetting to prevent unintended side-effects.
    }

    public function isArray(): bool
    {
        return is_array($this->data);
    }

    public function isObject(): bool
    {
        return is_object($this->data);
    }

    /**
     * Normalizes wrapped array|object into an array.
     *
     * - Arrays: returned as-is
     * - Objects: public properties via get_object_vars
     *
     * @return array<mixed, mixed>
     */
    public function toArray(): array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        return get_object_vars($this->data);
    }

    /**
     * Returns a list of items contained in a wrapped array, iterator, or plain object.
     * - Arrays: returned as-is (numeric or string keys)
     * - Iterators: iterated into a plain array
     * - Objects: iterated over public properties via get_object_vars
     *
     * @return array<int|string, mixed>
     */
    public function items(): array
    {
        $raw = $this->data;

        if (is_array($raw)) {
            return $raw;
        }

        if ($raw instanceof Traversable) {
            $items = [];
            foreach ($raw as $item) {
                $items[] = $item;
            }

            return $items;
        }

        return array_values(get_object_vars($raw));
    }
}
