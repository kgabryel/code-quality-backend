<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\InfectionGenerator;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Tests\Helper\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(InfectionGenerator::class)]
#[CoversClass(ConfigGenerator::class)]
final class InfectionGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Podstawia wszystkie placeholdery w wygenerowanym configu')]
    public function iteGenerateCorrectlyConfig(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn(
            file_get_contents(
                Path::getTemplate('infection.json5.template')
            )
        );
        $installConfig = new TestOptions(
            Framework::Symfony,
            TestTool::cases(),
            false,
            null,
        );
        $csFixerGenerator = new InfectionGenerator($filesystem, $installConfig);

        // Act
        $result = $csFixerGenerator->generate();

        // Assert
        self::assertSame($this->getExpectedConfig(), $result);
    }

    private function getExpectedConfig(): string
    {
        return <<<'JSON'
        {
          "$schema": "vendor/infection/infection/resources/schema.json",
          "tmpDir": "var/.infection/",
          "source": {
            "directories": [
              "src"
            ],
            "excludes": [
              "Kernel.php"
            ]
          },
          "logs": {
            "text": "var/.infection/infection.txt",
            "html": "var/.infection/infection.html"
          },
          "mutators": {
            "@default": true,
            "CastInt": false,
            "CastString": false,
            "MethodCallRemoval": {
              "ignore": [
                "App\\Entity\\*::*",
                "*::__construct"
              ]
            }
          }
        }
        JSON;
    }
}
