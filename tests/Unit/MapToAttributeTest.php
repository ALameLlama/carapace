<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Traits\SerializationTrait;

it('can transform property keys using MapTo', function (): void {
    $dto = new class
    {
        use SerializationTrait;

        #[MapTo('renamed_key')]
        public string $originalKey = 'value';
    };

    $result = $dto->toArray();

    expect($result)
        ->toHaveKey('renamed_key', 'value')
        ->not->toHaveKey('originalKey');
});

it('can transform multiple properties using MapTo', function (): void {
    $dto = new class
    {
        use SerializationTrait;

        #[MapTo('new_key1')]
        public string $key1 = 'value1';

        #[MapTo('new_key2')]
        public string $key2 = 'value2';
    };

    $result = $dto->toArray();

    expect($result)
        ->toHaveKey('new_key1', 'value1')
        ->toHaveKey('new_key2', 'value2')
        ->not->toHaveKey('key1')
        ->not->toHaveKey('key2');
});
