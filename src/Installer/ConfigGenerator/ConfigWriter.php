<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\FileWriter;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

readonly class ConfigWriter
{
    /** @param list<ConfigGenerator> $generators */
    public function write(array $generators, FileWriter $fileWriter, FrameworkPaths $frameworkPaths): void
    {
        info(Message::ConfigsGenerating->value);

        foreach ($generators as $generator) {
            $fileWriter->write($generator->getFileName(), $generator->generate());
        }

        $toIgnore = array_filter(
            array_map(
                static fn (ConfigGenerator $generator): string => $generator->getCachePath(),
                $generators,
            ),
            static fn (string $path): bool => !$frameworkPaths->isAlreadyIgnored($path),
        );

        if ($toIgnore === []) {
            return;
        }

        info(Message::GitignoreHint->value);
        note(implode(PHP_EOL, $toIgnore));
    }
}
