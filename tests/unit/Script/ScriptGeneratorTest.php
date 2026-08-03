<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Script;

use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\Script\ScriptCommand;
use Kgabryel\CodeQuality\Installer\Script\ScriptGenerator;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ScriptGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Informuje o generowaniu, gdy lista skryptów nie jest pusta')]
    public function itInfosWhenGenerate(): void
    {
        // Arrange
        $fileWriter = self::createStub(FileWriter::class);
        $scriptCommand = self::createStub(ScriptCommand::class);
        $scriptCommand->method('get')->willReturn('php vendor/bin/phpunit');
        $scriptsConfig = new ScriptsOptions(Environment::Local, [TestScript::PhpUnit]);
        $scriptGenerator = new ScriptGenerator($fileWriter, $scriptCommand, $scriptsConfig);

        // Act
        $scriptGenerator->generateScripts();

        // Assert
        Prompt::assertOutputContains(Message::ScriptsGenerating->value);
    }

    #[Test]
    #[TestDox('Nic nie robi, gdy lista skryptów jest pusta')]
    public function itSkipsEmpty(): void
    {
        // Arrange
        $fileWriter = $this->createMock(FileWriter::class);
        $fileWriter->expects($this->never())->method('write');
        $scriptCommand = $this->createMock(ScriptCommand::class);
        $scriptCommand->expects($this->never())->method('get');
        $scriptsConfig = new ScriptsOptions(Environment::Local, []);
        $scriptGenerator = new ScriptGenerator($fileWriter, $scriptCommand, $scriptsConfig);

        // Act
        $scriptGenerator->generateScripts();
    }

    #[Test]
    #[TestDox('Owija komendę w docker exec, gdy środowisko jest Docker')]
    public function itWrapsInDocker(): void
    {
        // Arrange
        $fileWriter = $this->createMock(FileWriter::class);
        $fileWriter->expects($this->once())
            ->method('write')
            ->with(
                'phpunit.sh',
                "#!/usr/bin/env sh\n\ndocker exec -i my-container sh -c 'php vendor/bin/phpunit'\n",
                true,
            );
        $scriptCommand = self::createStub(ScriptCommand::class);
        $scriptCommand->method('get')->willReturn('php vendor/bin/phpunit');
        $scriptsConfig = new ScriptsOptions(Environment::Docker, [TestScript::PhpUnit], 'my-container');
        $scriptGenerator = new ScriptGenerator($fileWriter, $scriptCommand, $scriptsConfig);

        // Act
        $scriptGenerator->generateScripts();
    }

    #[Test]
    #[TestDox('Zapisuje komendę do pliku')]
    public function itWritesExpectedContent(): void
    {
        // Arrange
        $fileWriter = $this->createMock(FileWriter::class);
        $fileWriter->expects($this->once())
            ->method('write')
            ->with('phpunit.sh', "#!/usr/bin/env sh\n\nphp vendor/bin/phpunit\n", true);
        $scriptCommand = $this->createMock(ScriptCommand::class);
        $scriptCommand->expects($this->once())
            ->method('get')
            ->willReturn('php vendor/bin/phpunit');
        $scriptsConfig = new ScriptsOptions(Environment::Local, [TestScript::PhpUnit]);
        $scriptGenerator = new ScriptGenerator($fileWriter, $scriptCommand, $scriptsConfig);

        // Act
        $scriptGenerator->generateScripts();
    }
}
