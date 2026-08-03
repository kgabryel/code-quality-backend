<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

readonly class ProjectRoot
{
    public function __construct(private Filesystem $filesystem) {}

    public function resolve(): string
    {
        $projectRoot = $this->filesystem->getCurrentDirectory();

        if ($projectRoot === false) {
            throw CommandError::projectRootUnavailable();
        }

        if ($this->filesystem->fileExists($projectRoot . '/composer.json')) {
            return $projectRoot;
        }

        warning(Message::NoComposerJson->value);

        if (!confirm(label: Label::Continue->value, default: false)) {
            throw CommandError::aborted();
        }

        return $projectRoot;
    }
}
