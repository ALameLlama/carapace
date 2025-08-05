<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionProperty;
use RegexIterator;

/**
 * Command to generate PHPDoc annotations for the with() method.
 *
 * This command scans all DTO classes and generates PHPDoc annotations
 * for the with() method based on the properties of each class.
 */
final class GenerateWithDocCommand
{
    /**
     * Run the command to generate PHPDoc annotations.
     *
     * @param  string  $directory  The directory to scan for DTO classes
     * @param  string  $namespace  The namespace prefix for DTO classes
     */
    public function run(string $directory, string $namespace): void
    {
        $files = $this->findPhpFiles($directory);

        foreach ($files as $file) {
            $this->processFile($file, $namespace);
        }
    }

    /**
     * Find all PHP files in the given directory.
     *
     * @param  string  $directory  The directory to scan
     * @return array<string> Array of file paths
     */
    private function findPhpFiles(string $directory): array
    {
        $directory = rtrim($directory, '/');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

        $files = [];
        foreach ($regex as $file) {
            $files[] = $file[0];
        }

        return $files;
    }

    /**
     * Process a PHP file to generate PHPDoc annotations.
     *
     * @param  string  $file  The file to process
     * @param  string  $namespace  The namespace prefix for DTO classes
     */
    private function processFile(string $file, string $namespace): void
    {
        // Extract the class name from the file path
        $className = $this->extractClassName($file, $namespace);
        if ($className === null || $className === '' || $className === '0') {
            return;
        }

        // Check if the class extends ImmutableDTO
        if (! $this->isImmutableDTO($className)) {
            return;
        }

        // Generate the PHPDoc annotation
        $phpDoc = $this->generatePhpDoc($className);

        // Update the file with the new PHPDoc annotation
        $this->updateFile($file, $phpDoc);
    }

    /**
     * Extract the class name from the file path.
     *
     * @param  string  $file  The file path
     * @param  string  $namespace  The namespace prefix for DTO classes
     * @return string|null The fully qualified class name, or null if not found
     */
    private function extractClassName(string $file, string $namespace): ?string
    {
        $content = file_get_contents($file);
        if ($content === '' || $content === '0' || $content === false) {
            return null;
        }

        // Extract the namespace
        if (in_array(preg_match('/namespace\s+([^;]+);/', $content, $matches), [0, false], true)) {
            return null;
        }

        $fileNamespace = $matches[1];

        // Check if the namespace starts with the given prefix
        if (! str_starts_with($fileNamespace, $namespace)) {
            return null;
        }

        // Extract the class name
        if (in_array(preg_match('/class\s+(\w+)/', $content, $matches), [0, false], true)) {
            return null;
        }

        $className = $matches[1];

        return $fileNamespace . '\\' . $className;
    }

    /**
     * Check if the class extends ImmutableDTO.
     *
     * @param  string  $className  The class name to check
     * @return bool True if the class extends ImmutableDTO, false otherwise
     */
    private function isImmutableDTO(string $className): bool
    {
        if (! class_exists($className)) {
            return false;
        }

        $reflection = new ReflectionClass($className);

        return $reflection->isSubclassOf(\Alamellama\Carapace\ImmutableDTO::class);
    }

    /**
     * Generate the PHPDoc annotation for the with() method.
     *
     * @param  string  $className  The class name to generate PHPDoc for
     * @return string The generated PHPDoc annotation
     */
    private function generatePhpDoc(string $className): string
    {
        $reflection = new ReflectionClass($className);
        $properties = $reflection->getProperties();

        $phpDoc = "/**\n";
        $phpDoc .= ' * @method self with(array $overrides = [], ';

        foreach ($properties as $property) {
            $name = $property->getName();
            $type = $this->getPropertyType($property);

            $phpDoc .= "{$type} \${$name} = null, ";
        }

        // Remove the trailing comma and space
        $phpDoc = rtrim($phpDoc, ', ');

        $phpDoc .= ") Creates a modified copy of the DTO with overridden values.\n";

        return $phpDoc . ' */';
    }

    /**
     * Get the type of a property.
     *
     * @param  ReflectionProperty  $property  The property to get the type of
     * @return string The property type
     */
    private function getPropertyType(ReflectionProperty $property): string
    {
        $type = $property->getType();

        if (! $type) {
            return 'mixed';
        }

        $typeName = $type->getName();

        if ($type->allowsNull()) {
            return "?{$typeName}";
        }

        return $typeName;
    }

    /**
     * Update the file with the new PHPDoc annotation.
     *
     * @param  string  $file  The file to update
     * @param  string  $phpDoc  The PHPDoc annotation to add
     */
    private function updateFile(string $file, string $phpDoc): void
    {
        $content = file_get_contents($file);
        if ($content === '' || $content === '0' || $content === false) {
            return;
        }

        // Check if the file already has a PHPDoc annotation for the with() method
        if (str_contains($content, '@method') && (str_contains($content, 'self with') || str_contains($content, 'static self with'))) {
            // Replace the existing PHPDoc annotation
            $content = preg_replace('/(\/\*\*\s*\n\s*\*\s*@method\s+(static\s+)?self\s+with.*?\*\/)/s', $phpDoc, $content);
        } else {
            // Add the PHPDoc annotation before the class definition, handling the final keyword
            $content = preg_replace('/(final\s+)?class\s+(\w+)\s+extends\s+ImmutableDTO/s', "{$phpDoc}\n$1class $2 extends ImmutableDTO", $content);
        }

        file_put_contents($file, $content);
    }
}
