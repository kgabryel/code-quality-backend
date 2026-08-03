<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Cli\Test;

use Kgabryel\CodeQuality\Installer\Cli\Command;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestCommand;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestInputCollector;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TestCommand::class)]
#[CoversClass(Command::class)]
final class TestCommandTest extends TestCase
{
    #[Test]
    #[TestDox('Polecenie się uruchamia')]
    public function itRunsSuccessfully(): void
    {
        // Arrange
        Prompt::fake(['y', Key::ENTER]);
        $inputCollector = self::createStub(TestInputCollector::class);
        $inputCollector->method('collect')->willReturn(
            new TestOptions(
                Framework::None,
                [TestTool::PhpUnit, TestTool::Infection],
                true,
                new ScriptsOptions(
                    Environment::Local,
                    [TestScript::PhpUnit],
                    ''
                ),
            )
        );

        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn('');

        $testCommand = new TestCommand(
            self::createStub(ProcessRunner::class),
            self::createStub(FileWriter::class),
            new ConfigWriter(),
            $inputCollector,
            $filesystem,
        );

        // Act
        $testCommand->run();

        // Assert
        Prompt::assertOutputContains(Message::Done->value);
    }

    #[Test]
    #[TestDox('Kończy działanie bez instalacji, gdy nie wybrano żadnych narzędzi')]
    public function itStopsEarly(): void
    {
        // Arrange
        $inputCollector = self::createStub(TestInputCollector::class);
        $inputCollector->method('collect')->willReturn(
            new TestOptions(
                Framework::None,
                [],
                true,
                null,
            )
        );

        $testCommand = new TestCommand(
            self::createStub(ProcessRunner::class),
            self::createStub(FileWriter::class),
            new ConfigWriter(),
            $inputCollector,
            self::createStub(Filesystem::class),
        );

        // Act
        $testCommand->run();

        // Assert
        Prompt::assertOutputContains(Message::NothingSelected->value);
    }
}
