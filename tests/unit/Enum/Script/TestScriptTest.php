<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Enum\Script;

use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;
use Kgabryel\CodeQuality\Tests\DataProvider\TestScriptDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TestScript::class)]
final class TestScriptTest extends TestCase
{
    /** @param array<TestTool> $tools */
    #[Test]
    #[TestDox('Zwraca oczekiwaną dostępność skryptu dla podanej konfiguracji narzędzi')]
    #[DataProviderExternal(TestScriptDataProvider::class, 'applicabilityCases')]
    public function itDeterminesApplicability(TestScript $script, array $tools, bool $expected): void
    {
        // Act
        $result = $script->isApplicable($tools);

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    #[TestDox('Zwraca skrypty w kolejności PhpUnit, CodeCoverage, Infection')]
    public function itReturnsValuesInDefinedOrder(): void
    {
        // Act
        $result = TestScript::values(TestTool::cases());

        // Assert
        self::assertSame(
            [TestScript::PhpUnit->value, TestScript::CodeCoverage->value, TestScript::Infection->value],
            $result,
        );
    }
}
