<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\DependencyInstaller;
use Kgabryel\CodeQuality\Installer\Dto\Installable;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Script\Script;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Kgabryel\CodeQuality\Installer\Script\ScriptCommand;
use Kgabryel\CodeQuality\Installer\Script\ScriptGenerator;

use function Laravel\Prompts\info;

abstract readonly class Command
{
    public function __construct(
        protected ProcessRunner $processRunner,
        protected FileWriter $fileWriter,
        protected ConfigWriter $configWriter,
    ) {}

    abstract public function run(): void;

    /**
     * @template T of Script
     *
     * @param null|ScriptsOptions<T> $scriptsConfig
     * @param ScriptCommand<T> $scriptCommand
     */
    protected function generateScripts(?ScriptsOptions $scriptsConfig, ScriptCommand $scriptCommand): void
    {
        if (!$scriptsConfig instanceof ScriptsOptions) {
            return;
        }

        new ScriptGenerator($this->fileWriter, $scriptCommand, $scriptsConfig)->generateScripts();
    }

    protected function installDependencies(Installable $config): void
    {
        new DependencyInstaller($this->processRunner, $config->framework, $config->toInstall())->run(
            $config->installDependencies
        );
    }

    protected function warnIfNothingToDo(Installable $config): bool
    {
        if ($config->toInstall() !== []) {
            return false;
        }

        info(Message::NothingSelected->value);

        return true;
    }
}
