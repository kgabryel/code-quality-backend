<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Input;

use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

abstract readonly class BaseScriptsCollector
{
    public function __construct(protected Filesystem $filesystem) {}

    protected function detectEnvironment(): Environment
    {
        $environment = select(
            label: Label::ScriptEnvironment->value,
            options: Environment::values(),
            default: Environment::detect($this->filesystem)->value,
            hint: Message::SelectHint->value,
        );

        return Environment::from($environment);
    }

    protected function selectContainerName(): string
    {
        return text(
            label: Label::ContainerName->value,
            required: true,
            validate: $this->validateContainerName(...),
        );
    }

    private function validateContainerName(string $value): ?string
    {
        return preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_.-]*$/', $value) === 1 ? null : Message::InvalidContainerName->value;
    }
}
