<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli;

use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisCommand;
use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisInputCollector;
use Kgabryel\CodeQuality\Installer\Cli\Analysis\AnalysisScriptsCollector;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestCommand;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestInputCollector;
use Kgabryel\CodeQuality\Installer\Cli\Test\TestScriptsCollector;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\Enum\CommandName;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;

readonly class CommandFactory
{
    public function __construct(private Filesystem $filesystem, private ProcessRunner $processRunner) {}

    public function create(CommandName $commandName, FileWriter $fileWriter, ConfigWriter $configWriter): Command
    {
        return match ($commandName) {
            CommandName::Analysis => new AnalysisCommand(
                $this->processRunner,
                $fileWriter,
                $configWriter,
                new AnalysisInputCollector(
                    new AnalysisScriptsCollector($this->filesystem),
                ),
                $this->filesystem,
            ),
            CommandName::Test => new TestCommand(
                $this->processRunner,
                $fileWriter,
                $configWriter,
                new TestInputCollector(
                    new TestScriptsCollector($this->filesystem)
                ),
                $this->filesystem,
            )
        };
    }
}
