<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FileWriter::class)]
final class FileWriterTest extends TestCase
{
    #[Test]
    #[TestDox('Ustawia uprawnienia do wykonywania dla skryptu')]
    public function itMakesExecutable(): void
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('fileExists')->willReturn(false);
        $filesystem->method('writeFile')->willReturn(10);
        $filesystem->expects($this->once())
            ->method('makeExecutable')
            ->with('/project/script.sh');
        $fileWriter = new FileWriter($filesystem, '/project');

        // Act
        $fileWriter->write('script.sh', 'content', true);
    }

    #[Test]
    #[TestDox('Nadpisuje plik, gdy użytkownik potwierdzi')]
    public function itOverwritesWhenUserConfirms(): void
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('fileExists')->willReturn(true);
        $filesystem->expects($this->once())
            ->method('writeFile')
            ->with('/project/script.sh', 'content')
            ->willReturn(10);
        Prompt::fake(['y', Key::ENTER]);
        $fileWriter = new FileWriter($filesystem, '/project');

        // Act
        $fileWriter->write('script.sh', 'content');
    }

    #[Test]
    #[TestDox('Pomija zapis, gdy użytkownik odmówi')]
    public function itSkipsWhenUserDeclines(): void
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('fileExists')->willReturn(true);
        $filesystem->expects($this->never())->method('writeFile');
        Prompt::fake([Key::ENTER]);
        $fileWriter = new FileWriter($filesystem, '/project');

        // Act
        $fileWriter->write('script.sh', 'content');

        // Assert
        Prompt::assertOutputContains(sprintf(Message::FileSkipped->value, 'script.sh'));
    }

    #[Test]
    #[TestDox('Rzuca wyjątek, gdy zapis pliku się nie powiedzie')]
    public function itThrowsWhenWriteFails(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('fileExists')->willReturn(false);
        $filesystem->method('writeFile')->willReturn(false);
        $fileWriter = new FileWriter($filesystem, '/project');

        // Act & Assert
        $this->expectException(CommandError::class);
        $fileWriter->write('script.sh', 'content');
    }

    #[Test]
    #[TestDox('Tworzy plik, gdy nie istnieje')]
    public function itWritesWhenFileMissing(): void
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('fileExists')->willReturn(false);
        $filesystem->expects($this->once())
            ->method('writeFile')
            ->with('/project/script.sh', 'content')
            ->willReturn(10);
        $filesystem->expects($this->never())->method('makeExecutable');
        $fileWriter = new FileWriter($filesystem, '/project');

        // Act
        $fileWriter->write('script.sh', 'content');
    }
}
