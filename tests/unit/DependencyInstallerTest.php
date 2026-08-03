<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\DependencyInstaller;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
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
#[CoversClass(DependencyInstaller::class)]
final class DependencyInstallerTest extends TestCase
{
    #[Test]
    #[TestDox('Rzuca wyjątek, gdy użytkownik odmawia kontynuacji po niepowodzeniu')]
    public function itAbortsWhenUserDeclines(): void
    {
        // Arrange
        $processRunner = self::createStub(ProcessRunner::class);
        $processRunner->method('run')->willReturn(127);
        Prompt::fake([Key::ENTER]);
        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, [AnalysisTool::Rector]);

        // Assert
        $this->expectException(CommandError::class);

        // Act
        $dependencyInstaller->run(true);
    }

    #[Test]
    #[TestDox('Instaluje wymagane pakiety')]
    public function itInstallsSuccessfully(): void
    {
        // Arrange
        $processRunner = $this->createMock(ProcessRunner::class);
        $processRunner->expects($this->once())
            ->method('run')
            ->with('composer require --dev friendsofphp/php-cs-fixer')
            ->willReturn(0);

        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, [AnalysisTool::CsFixer]);

        // Act
        $dependencyInstaller->run(true);

        // Assert
        Prompt::assertOutputContains(Message::DependenciesInstalled->value);
    }

    #[Test]
    #[TestDox('Wypisuje polecenie composera bez instalowania zależności')]
    public function itPrintsCommand(): void
    {
        // Arrange
        $processRunner = $this->createMock(ProcessRunner::class);
        $processRunner->expects($this->never())->method('run');
        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, [AnalysisTool::Rector]);

        // Act
        $dependencyInstaller->run(false);

        // Assert
        Prompt::assertOutputContains('composer require --dev');
        Prompt::assertOutputContains(Message::DependenciesSkipped->value);
    }

    #[Test]
    #[TestDox('Nie wypisuje niczego, gdy nie ma pakietów, a instalacja jest wyłączona')]
    public function itPrintsNothing(): void
    {
        // Arrange
        Prompt::fake();
        $processRunner = $this->createMock(ProcessRunner::class);
        $processRunner->expects($this->never())->method('run');
        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, []);

        // Act
        $dependencyInstaller->run(false);

        // Assert
        Prompt::assertOutputDoesntContain(Message::DependenciesSkipped->value);
    }

    #[Test]
    #[TestDox('Ostrzega o braku composera i przerywa, gdy użytkownik odmawia kontynuacji')]
    public function itThrowsWhenComposerMissing(): void
    {
        // Arrange
        $processRunner = self::createStub(ProcessRunner::class);
        $processRunner->method('run')->willReturn(127);
        Prompt::fake([Key::ENTER]);
        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, [AnalysisTool::CsFixer]);

        // Assert
        $this->expectException(CommandError::class);

        // Act
        $dependencyInstaller->run(true);
    }

    #[Test]
    #[TestDox('Rzuca wyjątek, gdy nie ma pakietów do zainstalowania')]
    public function itThrowsWithoutPackages(): void
    {
        // Arrange
        $processRunner = $this->createMock(ProcessRunner::class);
        $processRunner->expects($this->never())->method('run');
        $dependencyInstaller = new DependencyInstaller($processRunner, Framework::None, []);

        // Assert
        $this->expectException(CommandError::class);

        // Act
        $dependencyInstaller->run(true);
    }
}
