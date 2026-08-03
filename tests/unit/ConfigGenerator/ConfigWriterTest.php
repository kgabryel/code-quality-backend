<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigWriter::class)]
final class ConfigWriterTest extends TestCase
{
    #[Test]
    #[TestDox('Zapisuje konfiguracje każdego generatora')]
    public function itWritesConfigs(): void
    {
        // Arrange
        $first = self::createStub(ConfigGenerator::class);
        $first->method('getFileName')->willReturn('phpstan.dist.neon');
        $first->method('generate')->willReturn('phpstan content');

        $second = self::createStub(ConfigGenerator::class);
        $second->method('getFileName')->willReturn('.php-cs-fixer.dist.php');
        $second->method('generate')->willReturn('cs-fixer content');

        $fileWriter = $this->createMock(FileWriter::class);
        $fileWriter->expects($this->exactly(2))->method('write');

        $frameworkPaths = self::createStub(FrameworkPaths::class);
        $configWriter = new ConfigWriter();

        // Act
        $configWriter->write([$first, $second], $fileWriter, $frameworkPaths);
    }

    #[Test]
    #[TestDox('Informuje o wpisach do .gitignore')]
    public function itWritesGitignore(): void
    {
        // Arrange
        $generator = self::createStub(ConfigGenerator::class);
        $generator->method('getCachePath')->willReturn('.phpstan-cache/');

        $fileWriter = self::createStub(FileWriter::class);

        $frameworkPaths = self::createStub(FrameworkPaths::class);
        $frameworkPaths->method('isAlreadyIgnored')->willReturn(false);

        $configWriter = new ConfigWriter();

        // Act
        $configWriter->write([$generator], $fileWriter, $frameworkPaths);

        // Assert
        Prompt::assertOutputContains(Message::GitignoreHint->value);
    }
}
