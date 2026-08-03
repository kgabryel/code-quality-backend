<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisCommand;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestCommand;
use Kgabryel\CodeQuality\Installer\Enum\CommandName;

class CommandClassDataProvider
{
    public static function commands(): array
    {
        $commands = [];
        foreach (CommandName::cases() as $commandName) {
            $commands[] = [
                'commandClass' => self::getExpectedClass($commandName),
                'commandName' => $commandName,
            ];
        }

        return $commands;
    }

    private static function getExpectedClass(CommandName $commandName): string
    {
        return match ($commandName) {
            CommandName::Analysis => AnalysisCommand::class,
            CommandName::Test => TestCommand::class,
        };
    }
}
