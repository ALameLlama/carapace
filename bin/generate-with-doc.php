<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Alamellama\Carapace\Console\GenerateWithDocCommand;

// Default values
$directory = __DIR__ . '/../src';
$namespace = 'Alamellama\\Carapace';

// Parse command line arguments
$options = getopt('d:n:', ['directory:', 'namespace:']);

if (isset($options['d']) || isset($options['directory'])) {
    $directory = $options['d'] ?? $options['directory'];
}

if (isset($options['n']) || isset($options['namespace'])) {
    $namespace = $options['n'] ?? $options['namespace'];
}

// Run the command
$command = new GenerateWithDocCommand;
$command->run($directory, $namespace);

echo "PHPDoc annotations for with() method have been generated.\n";
