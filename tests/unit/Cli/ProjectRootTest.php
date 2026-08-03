<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Cli;

use Kgabryel\CodeQuality\Installer\Cli\ProjectRoot;
use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ProjectRoot::class)]
final class ProjectRootTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca katalog roboczy')]
    public function itReturnsCwd(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('getCurrentDirectory')->willReturn('/project');
        $filesystem->method('fileExists')->willReturn(true);

        $projectRoot = new ProjectRoot($filesystem);

        // Act
        $result = $projectRoot->resolve();

        // Assert
        self::assertSame('/project', $result);
    }

    #[Test]
    #[TestDox('Rzuca wyjątek, gdy composer.json nie istnieje, a użytkownik odmawia kontynuacji')]
    public function itThrowsWhenUserDeclines(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('getCurrentDirectory')->willReturn('/project');
        $filesystem->method('fileExists')->willReturn(false);

        Prompt::fake([Key::ENTER]);

        $projectRoot = new ProjectRoot($filesystem);

        // Assert
        $this->expectException(CommandError::class);

        // Act
        $projectRoot->resolve();
    }

    #[Test]
    #[TestDox('Wyświetla ostrzeżenie i zwraca katalog roboczy, gdy composer.json nie istnieje, a użytkownik potwierdza')]
    public function itWarnsAndContinues(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('getCurrentDirectory')->willReturn('/project');
        $filesystem->method('fileExists')->willReturn(false);

        Prompt::fake(['y', Key::ENTER]);

        $projectRoot = new ProjectRoot($filesystem);

        // Act
        $result = $projectRoot->resolve();

        // Assert
        self::assertSame('/project', $result);
        Prompt::assertOutputContains(Message::NoComposerJson->value);
    }
}
