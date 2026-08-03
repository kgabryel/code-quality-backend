<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Cli;

use Kgabryel\CodeQuality\Installer\Cli\CommandFactory;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\Enum\CommandName;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Kgabryel\CodeQuality\Tests\DataProvider\CommandClassDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CommandFactory::class)]
final class CommandFactoryTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca instancję komendy zgodną z podaną nazwą komendy')]
    #[DataProviderExternal(CommandClassDataProvider::class, 'commands')]
    public function itCreatesCommand(CommandName $commandName, string $commandClass): void
    {
        // Act
        $command = new CommandFactory(new Filesystem(), new ProcessRunner())->create(
            $commandName,
            new FileWriter(new Filesystem(), '/tmp'),
            new ConfigWriter()
        );

        // Assert
        self::assertSame($commandClass, $command::class);
    }
}
