<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisToolGeneratorFactory;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Tests\DataProvider\AnalysisToolGeneratorDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AnalysisToolGeneratorFactory::class)]
final class AnalysisToolGeneratorFactoryTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca generator zgodny z narzędziem analizy')]
    #[DataProviderExternal(AnalysisToolGeneratorDataProvider::class, 'tools')]
    public function itBuildsExpectedGenerator(AnalysisTool $tool, ?string $expectedClass): void
    {
        $factory = new AnalysisToolGeneratorFactory(new Filesystem(), $this->createInstallConfig());

        $generator = $factory->build($tool);

        if ($expectedClass === null) {
            self::assertNull($generator);

            return;
        }

        self::assertInstanceOf($expectedClass, $generator);
    }

    private function createInstallConfig(): AnalysisOptions
    {
        return new AnalysisOptions(
            PhpVersion::PHP_84,
            Framework::None,
            AnalysisTool::cases(),
            ComposerTool::cases(),
            true,
            null,
        );
    }
}
