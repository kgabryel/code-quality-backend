<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\PhpCsGenerator;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Tests\Helper\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PhpCsGenerator::class)]
#[CoversClass(ConfigGenerator::class)]
final class PhpCsGeneratorTest extends TestCase
{
    #[Test]
    #[TestDox('Podstawia wszystkie placeholdery w wygenerowanym configu')]
    public function iteGenerateCorrectlyConfig(): void
    {
        // Arrange
        $filesystem = self::createStub(Filesystem::class);
        $filesystem->method('readFile')->willReturn(
            file_get_contents(
                Path::getTemplate('phpcs.xml.dist.template')
            )
        );
        $installConfig = new AnalysisOptions(
            PhpVersion::PHP_84,
            Framework::Laravel,
            AnalysisTool::cases(),
            ComposerTool::cases(),
            true,
            null,
        );
        $csFixerGenerator = new PhpCsGenerator($filesystem, $installConfig);

        // Act
        $result = $csFixerGenerator->generate();

        // Assert
        self::assertSame($this->getExpectedConfig(), $result);
    }

    private function getExpectedConfig(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>

        <ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">

            <arg name="basepath" value="."/>
            <arg name="cache" value=".phpcs-cache"/>
            <arg name="colors"/>
            <arg name="extensions" value="php"/>

            <rule ref="vendor/kgabryel/code-quality/resources/presets/phpcs/laravel.xml"/>

            <file>app/</file>
            <file>bootstrap/</file>
            <file>config/</file>
            <file>database/</file>
            <file>routes/</file>
            <file>tests/</file>
            <exclude-pattern>bootstrap/cache/*</exclude-pattern>
        </ruleset>

        XML;
    }
}
