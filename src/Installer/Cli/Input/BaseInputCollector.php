<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Input;

use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

abstract readonly class BaseInputCollector
{
    protected function confirmInstall(): bool
    {
        return confirm(
            label: Label::InstallDependencies->value,
            hint: Message::InstallHint->value,
        );
    }

    protected function detectFramework(): Framework
    {
        $framework = select(
            label: Label::Framework->value,
            options: Framework::values(),
            default: Framework::detect()->value,
            hint: Message::SelectHint->value,
        );

        return Framework::from($framework);
    }
}
