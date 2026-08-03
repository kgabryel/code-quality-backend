<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Cli\Analysis;

use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisCommand;
use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisInputCollector;
use Kgabryel\CodeQuality\Installer\Cli\Command;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AnalysisCommand::class)]
#[CoversClass(Command::class)]
final class AnalysisCommandTest extends TestCase
{
    #[Test]
    #[TestDox('Polecenie się uruchamia')]
    public function itRunsSuccessfully(): void
    {
        // Arrange
        Prompt::fake();
        $inputCollector = self::createStub(AnalysisInputCollector::class);
        $inputCollector->method('collect')->willReturn(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::None,
                [AnalysisTool::Rector],
                [ComposerTool::RequireChecker],
                false,
                new ScriptsOptions(
                    Environment::Docker,
                    [AnalysisScript::Rector],
                    'container-name'
                ),
            )
        );

        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn('');

        $analysisCommand = new AnalysisCommand(
            self::createStub(ProcessRunner::class),
            self::createStub(FileWriter::class),
            new ConfigWriter(),
            $inputCollector,
            $filesystem,
        );

        // Act
        $analysisCommand->run();

        // Assert
        Prompt::assertOutputContains(Message::Done->value);
    }

    #[Test]
    #[TestDox('Kończy działanie bez instalacji, gdy nie wybrano żadnych narzędzi')]
    public function itStopsEarly(): void
    {
        // Arrange
        Prompt::fake();
        $inputCollector = self::createStub(AnalysisInputCollector::class);
        $inputCollector->method('collect')->willReturn(
            new AnalysisOptions(
                PhpVersion::PHP_84,
                Framework::None,
                [],
                [],
                false,
                null,
            )
        );

        $analysisCommand = new AnalysisCommand(
            self::createStub(ProcessRunner::class),
            self::createStub(FileWriter::class),
            new ConfigWriter(),
            $inputCollector,
            self::createStub(Filesystem::class),
        );

        // Act
        $analysisCommand->run();

        // Assert
        Prompt::assertOutputContains(Message::NothingSelected->value);
    }
}
