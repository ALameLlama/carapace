<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use Alamellama\Carapace\Support\Data;
use RuntimeException;

use function array_key_exists;

it('can use __isset and __get when available', function (): void {
    $obj = new class
    {
        private array $store = ['present' => 'value'];

        public function __isset(string $name): bool
        {
            return array_key_exists($name, $this->store);
        }

        public function __get(string $name): mixed
        {
            return $this->store[$name] ?? null;
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('present'))->toBeTrue()
        ->and($data->get('present'))->toBe('value');
});

it('treats value as present when __get does not throw and __isset reports false', function (): void {
    $obj = new class
    {
        private array $store = ['present' => 'value'];

        public function __isset(string $name): bool
        {
            return false;
        }

        public function __get(string $name): mixed
        {
            return $this->store[$name] ?? null;
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('missing'))->toBeTrue()
        ->and($data->get('missing'))->toBeNull();
});

it('can fall back to __get when __isset is absent and __get does not throw', function (): void {
    $obj = new class
    {
        public function __get(string $name): mixed
        {
            return $name === 'lazy' ? 'loaded' : null;
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('lazy'))->toBeTrue()
        ->and($data->get('lazy'))->toBe('loaded');
});

it('returns false in has when __get throws', function (): void {
    $obj = new class
    {
        public function __get(string $name): mixed
        {
            throw new RuntimeException('not found');
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('anything'))->toBeFalse();
});

it('ignores exceptions from __isset and still tries __get', function (): void {
    $obj = new class
    {
        public function __isset(string $name): bool
        {
            throw new RuntimeException('boom');
        }

        public function __get(string $name): mixed
        {
            return $name === 'ok' ? 42 : throw new RuntimeException('missing');
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('ok'))->toBeTrue()
        ->and($data->get('ok'))->toBe(42)
        ->and($data->has('bad'))->toBeFalse();
});

it('has will return false if __get is accessing undefined key', function (): void {
    $obj = new class
    {
        public function __get(string $name): mixed
        {
            return $this->{$name};
        }
    };

    $data = Data::wrap($obj);

    expect($data->has('random'))->toBeFalse();
});
