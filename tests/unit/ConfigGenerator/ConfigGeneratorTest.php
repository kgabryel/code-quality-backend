<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigGenerator::class)]
final class ConfigGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Rzuca wyjątek, gdy template jest nieczytelny')]
    public function itThrowsWhenTemplateUnreadable(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn(false);
        $configGenerator = new TestableConfigGenerator($filesystem);

        // Assert
        $this->expectException(CommandError::class);

        // Act
        $configGenerator->generate();
    }
}
