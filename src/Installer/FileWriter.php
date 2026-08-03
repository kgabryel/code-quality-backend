<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer;

use Kgabryel\CodeQuality\Installer\Enum\Text\Message;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

readonly class FileWriter
{
    public function __construct(private Filesystem $filesystem, private string $projectRoot) {}

    public function write(string $fileName, string $content, bool $executable = false): void
    {
        $target = sprintf('%s/%s', $this->projectRoot, $fileName);
        $existed = $this->filesystem->fileExists($target);

        if ($existed && !$this->shouldOverwrite($fileName)) {
            warning(sprintf(Message::FileSkipped->value, $fileName));

            return;
        }

        if ($this->filesystem->writeFile($target, $content) === false) {
            throw CommandError::writeFailed($target);
        }

        if ($executable) {
            $this->filesystem->makeExecutable($target);
        }

        info(sprintf($existed ? Message::FileUpdated->value : Message::FileCreated->value, $fileName));
    }

    private function shouldOverwrite(string $fileName): bool
    {
        return confirm(
            label: sprintf(Message::FileExists->value, $fileName),
            default: false,
        );
    }
}
