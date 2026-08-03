<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer;

use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ToPackages;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\warning;

readonly class DependencyInstaller
{
    private const string COMMAND = 'composer require --dev';

    private const int EXIT_COMMAND_NOT_FOUND = 127;

    private const int EXIT_PROCESS_START_FAILED = 1;

    private const int EXIT_SUCCESS = 0;

    /** @var array<string> */
    private array $packages;

    private ProcessRunner $processRunner;

    /** @param array<ToPackages> $toInstall */
    public function __construct(ProcessRunner $processRunner, Framework $framework, array $toInstall)
    {
        $this->processRunner = $processRunner;
        $packages = array_merge(...array_map(
            static fn (ToPackages $tool): array => $tool->toPackages($framework),
            $toInstall
        ));
        sort($packages);
        $this->packages = array_unique($packages);
    }

    public function run(bool $installDependencies): void
    {
        $installDependencies ? $this->installDependencies() : $this->printInstallDependenciesCommand();
    }

    private function install(): int
    {
        if ($this->packages === []) {
            throw CommandError::noPackages();
        }

        $exitCode = $this->processRunner->run(
            sprintf('%s %s', self::COMMAND, implode(' ', $this->packages))
        );

        return $exitCode ?? self::EXIT_PROCESS_START_FAILED;
    }

    private function installDependencies(): void
    {
        info(Message::DependenciesInstalling->value);
        $exitCode = $this->install();

        if ($exitCode === self::EXIT_SUCCESS) {
            info(Message::DependenciesInstalled->value);

            return;
        }

        warning(
            $exitCode === self::EXIT_COMMAND_NOT_FOUND ? Message::ComposerNotFound->value : Message::ComposerFailed->value
        );
        note($this->printableCommand());

        if (!confirm(label: Label::ContinueWithoutDependencies->value, default: false)) {
            throw CommandError::aborted();
        }
    }

    private function printableCommand(): string
    {
        if ($this->packages === []) {
            return '';
        }

        return sprintf("%s \\\n    %s", self::COMMAND, implode(" \\\n    ", $this->packages));
    }

    private function printInstallDependenciesCommand(): void
    {
        $command = $this->printableCommand();
        if ($command === '') {
            return;
        }

        warning(Message::DependenciesSkipped->value);
        note($command);
    }
}
