<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Alamellama\Carapace\Console\GenerateWithDocCommand;

it('can generate PHPDoc annotations for DTOs', function (): void {
    // Create a temporary directory for testing
    $tempDir = sys_get_temp_dir() . '/carapace_test_' . uniqid('', true);
    mkdir($tempDir, 0777, true);

    // Create a test DTO file
    $testFile = $tempDir . '/TestDTO.php';
    $content = <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Temp;

use Alamellama\Carapace\ImmutableDTO;

final class TestDTO extends ImmutableDTO
{
    public function __construct(
        public string $name,
        public int $age,
        public bool $active = true,
    ) {}
}
PHP;

    file_put_contents($testFile, $content);

    // Run the command
    $command = new GenerateWithDocCommand;
    $command->run($tempDir, 'Tests\\Temp');

    // Check if the PHPDoc annotation was added
    $updatedContent = file_get_contents($testFile);

    // Debug output
    echo "Generated content:\n" . $updatedContent . "\n";

    expect($updatedContent)->toContain('@method self with(array $overrides = [], string $name = null, int $age = null, bool $active = null)');

    // Clean up
    unlink($testFile);
    rmdir($tempDir);
});
