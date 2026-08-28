<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

interface DTOInterface
{
    /**
     * @param  string|array<mixed, mixed>|object  $data
     */
    public static function from(string|array|object $data): static;

    /**
     * @param  string|array<array<mixed, mixed>|object>|object  $data
     * @return static[]
     */
    public static function collect(string|array|object $data): array;

    /**
     * @param  array<mixed, mixed>  $overrides
     * @param  mixed  ...$namedOverrides
     */
    public function with(array|object $overrides = [], ...$namedOverrides): static;

    /** @return array<string, mixed> */
    public function toArray(): array;

    public function toJson(): string;
}
