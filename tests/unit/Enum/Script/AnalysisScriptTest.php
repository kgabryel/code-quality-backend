<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Enum\Script;

use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Tests\DataProvider\AnalysisScriptDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AnalysisScript::class)]
final class AnalysisScriptTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca oczekiwaną dostępność skryptu dla podanej konfiguracji narzędzi')]
    #[DataProviderExternal(AnalysisScriptDataProvider::class, 'applicabilityCases')]
    public function itDeterminesApplicability(
        AnalysisScript $script,
        array $analysisTools,
        array $composerTools,
        bool $expected,
    ): void {
        // Act
        $result = $script->isApplicable($analysisTools, $composerTools);

        // Assert
        self::assertSame($expected, $result);
    }
}
