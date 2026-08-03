<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Dto;

use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Tests\DataProvider\AnalysisOptionsToolsDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AnalysisOptions::class)]
final class AnalysisOptionsTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca połączenie narzędzi analizy i narzędzi composera')]
    #[DataProviderExternal(AnalysisOptionsToolsDataProvider::class, 'tools')]
    public function itCombinesATools(
        array $analysisTools,
        array $composerTools,
        array $expected,
    ): void {
        // Arrange
        $analysisOptions = new AnalysisOptions(
            PhpVersion::PHP_84,
            Framework::None,
            $analysisTools,
            $composerTools,
            true,
            null,
        );

        // Act
        $tools = $analysisOptions->toInstall();

        // Assert
        self::assertSame($expected, $tools);
    }
}
