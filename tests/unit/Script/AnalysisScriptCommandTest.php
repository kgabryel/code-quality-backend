<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Script;

use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Script\AnalysisScriptCommand;
use Kgabryel\CodeQuality\Tests\DataProvider\AnalysisScriptDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AnalysisScriptCommand::class)]
final class AnalysisScriptCommandTest extends TestCase
{
    #[Test]
    #[TestDox('Buduje i łączy komendy CodeFix dla wszystkich trzech narzędzi')]
    public function itBuildsCodeFix(): void
    {
        // Arrange
        $analysisScriptCommand = new AnalysisScriptCommand(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::Laravel,
                AnalysisTool::cases(),
                ComposerTool::cases(),
                true,
                null,
            )
        );

        // Act
        $command = $analysisScriptCommand->get(AnalysisScript::CodeFix);

        // Assert
        self::assertSame(
            "echo \"===== PHP CS Fixer =====\"\n"
            . "php vendor/bin/php-cs-fixer fix && echo \"✓ PHP CS Fixer passed\" || echo \"✗ PHP CS Fixer failed\"\n\n"
            . "echo \"===== PHP Code Sniffer (fix) =====\"\n"
            . "php vendor/bin/phpcbf && echo \"✓ PHP Code Sniffer (fix) passed\" || echo \"✗ PHP Code Sniffer (fix) failed\"\n\n"
            . "echo \"===== Rector =====\"\n"
            . 'php vendor/bin/rector process && echo "✓ Rector passed" || echo "✗ Rector failed"',
            $command,
        );
    }

    #[Test]
    #[TestDox('Buduje stałą komendę Composer Check niezależną od wybranych narzędzi')]
    public function itBuildsComposerCheck(): void
    {
        // Arrange
        $analysisScriptCommand = new AnalysisScriptCommand(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::Symfony,
                AnalysisTool::cases(),
                ComposerTool::cases(),
                true,
                null,
            )
        );

        // Act
        $command = $analysisScriptCommand->get(AnalysisScript::ComposerCheck);

        // Assert
        self::assertSame(
            "echo \"===== Composer Validate =====\"\n"
            . 'composer validate --strict --no-check-publish'
            . " && echo \"✓ Composer Validate passed\" || echo \"✗ Composer Validate failed\"\n\n"
            . "echo \"===== Composer Audit =====\"\n"
            . "composer audit && echo \"✓ Composer Audit passed\" || echo \"✗ Composer Audit failed\"\n\n"
            . "echo \"===== Composer Outdated =====\"\n"
            . 'composer outdated && echo "✓ Composer Outdated passed" || echo "✗ Composer Outdated failed"',
            $command,
        );
    }

    #[Test]
    #[TestDox('Buduje komendę parallel-lint bez wykluczeń dla frameworka None')]
    public function itBuildsParallelLint(): void
    {
        // Arrange
        $analysisScriptCommand = new AnalysisScriptCommand(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::Laravel,
                AnalysisTool::cases(),
                ComposerTool::cases(),
                true,
                null,
            )
        );

        // Act
        $command = $analysisScriptCommand->get(AnalysisScript::ParallelLint);

        // Assert
        self::assertSame(
            'php vendor/bin/parallel-lint app bootstrap config database routes tests --exclude bootstrap/cache',
            $command
        );
    }

    #[Test]
    #[TestDox('Zwraca niepustą komendę dla każdego skryptu')]
    #[DataProviderExternal(AnalysisScriptDataProvider::class, 'scripts')]
    public function itReturnsCommand(AnalysisScript $script): void
    {
        $analysisScriptCommand = new AnalysisScriptCommand(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::None,
                AnalysisTool::cases(),
                ComposerTool::cases(),
                true,
                null,
            )
        );

        $command = $analysisScriptCommand->get($script);

        self::assertNotSame('', $command);
    }
}
