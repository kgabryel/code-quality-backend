<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Script;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Script\TestScriptCommand;
use Kgabryel\CodeQuality\Tests\DataProvider\TestScriptDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TestScriptCommand::class)]
final class TestScriptCommandTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca niepustą komendę dla każdego skryptu')]
    #[DataProviderExternal(TestScriptDataProvider::class, 'scripts')]
    public function itReturnsCommand(TestScript $script): void
    {
        $testScriptCommand = new TestScriptCommand(new FrameworkPaths(Framework::Symfony));

        $command = $testScriptCommand->get($script);

        self::assertNotSame('', $command);
    }
}
