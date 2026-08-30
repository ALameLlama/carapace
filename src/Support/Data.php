<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Support;

use const JSON_THROW_ON_ERROR;

use ErrorException;
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
 * Unified accessor for data sources.
 */
class Data
{
    /**
     * Current data state used for reads and mutations.
     * - If wrapping an array: this is a copy of the original array that can be modified freely.
     * - If wrapping an object: this stores overrides as an associative array.
     *
     * @var array<string|int, mixed>
     */
    private array $data;

    /**
     * Tracks keys explicitly unset on object wrappers to mask original properties.
     * Ignored for array wrappers.
     *
     * @var array<string, true>
     */
    private array $masked = [];

    /**
     * The original immutable reference/value provided to wrap().
     *
     * @param  array<mixed,mixed>|object  $originalData
     */
    private function __construct(private readonly array|object $originalData)
    {
        $this->data = is_array($originalData) ? $originalData : [];
    }

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

    public function has(string $key): bool
    {
        if ($this->hasDirect($key)) {
            return true;
        }

        if (isset($this->masked[$key])) {
            return false;
        }

        return $this->resolve(DataPath::parse($key)->segments)[0];
    }

    public function get(string $key): mixed
    {
        if ($this->hasDirect($key) || isset($this->masked[$key])) {
            return $this->getDirect($key);
        }

        return $this->resolve(DataPath::parse($key)->segments)[1];
    }

    public function set(string $key, mixed $value): void
    {
        if (is_object($this->originalData) && isset($this->masked[$key])) {
            unset($this->masked[$key]);
        }

        $this->data[$key] = $value;
    }

    /**
     * Unset the value causing subsequent reads to return null and has() to be false.
     *  - Arrays: removes the key from the wrapper's mutable copy only (original array unchanged)
     *  - Objects: masks the key within the wrapper (without mutating the original object)
     */
    public function unset(string $key): void
    {
        if (is_array($this->originalData)) {
            unset($this->data[$key]);

            return;
        }

        $this->masked[$key] = true;
        unset($this->data[$key]);
    }

    public function isArray(): bool
    {
        return is_array($this->originalData);
    }

    public function isObject(): bool
    {
        return is_object($this->originalData);
    }

    /**
     * Normalizes wrapped array|object into an array.
     *
     * - Arrays: return the current mutable copy ($this->data)
     * - Objects: public properties of the original merged with overrides ($this->data)
     *
     * @return array<mixed, mixed>
     */
    public function toArray(): array
    {
        if (is_array($this->originalData)) {
            return $this->data;
        }

        $base = get_object_vars($this->originalData);

        foreach (array_keys($this->masked) as $k) {
            unset($base[$k]);
        }

        foreach ($this->data as $k => $v) {
            $base[$k] = $v;
        }

        return $base;
    }

    /**
     * Returns a list of items contained in a wrapped array, iterator, or plain object.
     * - Arrays: return current array values
     * - Iterators: iterated into a plain array (from originalData)
     * - Objects: iterated over public properties via get_object_vars, then merge with overrides ($this->data), then values()
     *
     * @return array<int|string, mixed>
     */
    public function items(): array
    {
        $raw = $this->originalData;

        if (is_array($raw)) {
            return $this->data;
        }

        if ($raw instanceof Traversable) {
            $items = [];
            foreach ($raw as $item) {
                $items[] = $item;
            }

            return $items;
        }

        $vars = get_object_vars($raw);

        foreach (array_keys($this->masked) as $k) {
            unset($vars[$k]);
        }

        foreach ($this->data as $k => $v) {
            $vars[$k] = $v;
        }

        return array_values($vars);
    }

    /**
     * @param  non-empty-list<array{key: string, wildcard: bool}>  $segments
     * @return array{bool, mixed}
     */
    private function resolve(array $segments): array
    {
        $segment = array_shift($segments);

        if ($segment['wildcard']) {
            $items = $this->expandableItems();
            $values = [];
            $exists = false;

            foreach ($items as $item) {
                if ($segments === []) {
                    $values[] = $item;
                    $exists = true;

                    continue;
                }

                if (! is_array($item) && ! is_object($item)) {
                    $values[] = null;

                    continue;
                }

                [$branchExists, $value] = self::wrap($item)->resolve($segments);
                $exists = $exists || $branchExists;

                if ($this->containsWildcard($segments) && is_array($value)) {
                    array_push($values, ...$value);
                } else {
                    $values[] = $value;
                }
            }

            return [$exists, $values];
        }

        if (! $this->hasDirect($segment['key'])) {
            return [false, null];
        }

        $value = $this->getDirect($segment['key']);

        if ($segments === []) {
            return [true, $value];
        }

        if (! is_array($value) && ! is_object($value)) {
            return [false, null];
        }

        return self::wrap($value)->resolve($segments);
    }

    /** @param list<array{key: string, wildcard: bool}> $segments */
    private function containsWildcard(array $segments): bool
    {
        foreach ($segments as $segment) {
            if ($segment['wildcard']) {
                return true;
            }
        }

        return false;
    }

    /** @return array<mixed, mixed> */
    private function expandableItems(): array
    {
        if ($this->originalData instanceof Traversable) {
            return [];
        }

        return $this->toArray();
    }

    private function hasDirect(string $key): bool
    {
        // Arrays: consult only the mutable copy
        if (is_array($this->originalData)) {
            return array_key_exists($key, $this->data);
        }

        // Objects: if a key is masked (explicitly unset), it's considered absent
        if (isset($this->masked[$key])) {
            return false;
        }

        // Objects: check current overrides
        if (array_key_exists($key, $this->data)) {
            return true;
        }

        // Then check the original object
        if (property_exists($this->originalData, $key)) {
            return true;
        }

        if (method_exists($this->originalData, '__isset')) {
            try {
                if ($this->originalData->__isset($key)) {
                    return true;
                }
            } catch (Throwable) {
                // fall through
            }
        }

        if (method_exists($this->originalData, '__get')) {
            // ->__get() can return a warning, we want to make this an exception.
            set_error_handler(static function ($errno, $errstr, $errfile, $errline): void {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });

            try {
                @$this->originalData->__get($key);
                restore_error_handler();

                return true;
            } catch (Throwable) {
                restore_error_handler();

                return false;
            }
        }

        return false;
    }

    private function getDirect(string $key): mixed
    {
        // Arrays: read from the mutable copy only
        if (is_array($this->originalData)) {
            return $this->data[$key] ?? null;
        }

        // Objects: if masked, treat as absent/null
        if (isset($this->masked[$key])) {
            return null;
        }

        // Objects: prefer current overrides
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (property_exists($this->originalData, $key)) {
            return $this->originalData->{$key};
        }

        if (method_exists($this->originalData, '__get')) {
            return $this->originalData->__get($key);
        }

        return $this->originalData->{$key} ?? null;
    }
}
