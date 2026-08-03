<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Project;

use Kgabryel\CodeQuality\Installer\Enum\SortedValues;
use Kgabryel\CodeQuality\Installer\Filesystem;

enum Environment: string
{
    use SortedValues;

    case Docker = 'Docker';
    case Local = 'Local';

    public static function detect(Filesystem $filesystem): self
    {
        return $filesystem->fileExists('/.dockerenv') ? self::Docker : self::Local;
    }
}
