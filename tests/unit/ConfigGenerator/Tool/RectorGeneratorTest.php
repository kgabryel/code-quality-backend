<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\RectorGenerator;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Tests\Helper\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RectorGenerator::class)]
#[CoversClass(ConfigGenerator::class)]
final class RectorGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Podstawia wszystkie placeholdery w wygenerowanym configu')]
    public function iteGenerateCorrectlyConfig(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn(
            file_get_contents(
                Path::getTemplate('rector.php.template')
            )
        );
        $installConfig = new AnalysisOptions(
            PhpVersion::PHP_84,
            Framework::Laravel,
            AnalysisTool::cases(),
            ComposerTool::cases(),
            true,
            null,
        );
        $csFixerGenerator = new RectorGenerator($filesystem, $installConfig);

        // Act
        $result = $csFixerGenerator->generate();

        // Assert
        self::assertSame($this->getExpectedConfig(), $result);
    }

    private function getExpectedConfig(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);
            
            use Kgabryel\CodeQuality\Preset\Rector\LaravelConfig as Preset;
            use Rector\Config\RectorConfig;
            use Rector\Set\ValueObject\SetList;
            
            return RectorConfig::configure()
                ->withPaths([
                    ...Preset::getPaths(__DIR__),
                ])
                ->withImportNames()
                ->withPhpSets(php84: true)
                ->withSets([
                    ...Preset::getSets(),
                ])
                ->withComposerBased(
                    ...Preset::getComposerBased(),
                )
                ->withAttributesSets(
                    ...Preset::getAttributesSets(),
                )
                ->withCache(__DIR__ . '/.rector-cache/')
                ->withSkip([
                    ...Preset::getsSkip(),
                ]);
            PHP;
    }
}
