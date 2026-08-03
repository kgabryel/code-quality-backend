<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Script;

use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\Script;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\FileWriter;

use function Laravel\Prompts\info;

/** @template T of Script */
readonly class ScriptGenerator
{
    private const string SHEBANG = '#!/usr/bin/env sh';

    /**
     * @param ScriptCommand<T> $scriptCommand
     * @param ScriptsOptions<T> $scriptsConfig
     */
    public function __construct(
        private FileWriter $fileWriter,
        private ScriptCommand $scriptCommand,
        private ScriptsOptions $scriptsConfig,
    ) {}

    public function generateScripts(): void
    {
        if ($this->scriptsConfig->scripts === []) {
            return;
        }

        info(Message::ScriptsGenerating->value);

        foreach ($this->scriptsConfig->scripts as $script) {
            $this->write($script);
        }
    }

    /** @param T $script */
    private function getFileContent(Script $script): string
    {
        $command = $this->scriptCommand->get($script);

        if ($this->scriptsConfig->environment === Environment::Docker) {
            $command = $this->wrapInDocker($command);
        }

        return sprintf("%s\n\n%s\n", self::SHEBANG, $command);
    }

    private function wrapInDocker(string $command): string
    {
        if (str_contains($command, "\n")) {
            $command = sprintf("\n%s\n", $command);
        }

        return sprintf("docker exec -i %s sh -c '%s'", $this->scriptsConfig->containerName, $command);
    }

    /** @param T $script */
    private function write(Script $script): void
    {
        $this->fileWriter->write(
            sprintf('%s.sh', $script->getFileName()),
            $this->getFileContent($script),
            true
        );
    }
}
