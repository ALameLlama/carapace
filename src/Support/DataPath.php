<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Support;

use function count;

final readonly class DataPath
{
    /**
     * @param  non-empty-list<array{key: string, wildcard: bool}>  $segments
     */
    private function __construct(public array $segments) {}

    public static function parse(string $path): self
    {
        $segments = [];
        $segment = '';
        $escaped = false;
        $wildcard = true;

        foreach (str_split($path) as $character) {
            if ($escaped) {
                $segment .= $character;
                $wildcard = false;
                $escaped = false;

                continue;
            }

            if ($character === '\\') {
                $escaped = true;

                continue;
            }

            if ($character === '.') {
                $segments[] = ['key' => $segment, 'wildcard' => $wildcard && $segment === '*'];
                $segment = '';
                $wildcard = true;

                continue;
            }

            $segment .= $character;
        }

        if ($escaped) {
            $segment .= '\\';
        }

        $segments[] = ['key' => $segment, 'wildcard' => $wildcard && $segment === '*'];

        return new self($segments);
    }

    public function fieldName(): string
    {
        for ($index = count($this->segments) - 1; $index >= 0; $index--) {
            if (! $this->segments[$index]['wildcard']) {
                return $this->segments[$index]['key'];
            }
        }

        return '*';
    }
}
