<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\CsFixerGenerator;
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
#[CoversClass(CsFixerGenerator::class)]
#[CoversClass(ConfigGenerator::class)]
final class CsFixerGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Podstawia wszystkie placeholdery w wygenerowanym configu')]
    public function iteGenerateCorrectlyConfig(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn(
            file_get_contents(
                Path::getTemplate('.php-cs-fixer.dist.php.template')
            )
        );
        $installConfig = new AnalysisOptions(
            PhpVersion::PHP_84,
            Framework::Symfony,
            AnalysisTool::cases(),
            ComposerTool::cases(),
            true,
            null,
        );
        $csFixerGenerator = new CsFixerGenerator($filesystem, $installConfig);

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

            use Kgabryel\CodeQuality\Preset\CsFixer\SymfonyConfig as Preset;

            $finder = new PhpCsFixer\Finder()
                ->in([
                    ...Preset::getPaths(__DIR__),
                ]);

            return new PhpCsFixer\Config()
                ->setRiskyAllowed(Preset::isRiskyAllowed())
                ->setRules([
                    ...Preset::getSets(),
                ])
                ->setFinder($finder)
                ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
            PHP;
    }
}
