<?php

declare(strict_types=1);

namespace Tests\Arch;

test('the codebase does not contain any debugging code')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'ray', 'rd'])
    ->not->toBeUsed();

arch('package to use strict types')
    ->expect('src')
    ->toUseStrictTypes();

arch()
    ->preset()
    ->php();

arch()
    ->preset()
    ->security();
