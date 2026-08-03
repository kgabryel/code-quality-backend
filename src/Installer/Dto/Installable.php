<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Dto;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ToPackages;

interface Installable
{
    public Framework $framework { get; }

    public bool $installDependencies { get; }

    public function getFrameworkPaths(): FrameworkPaths;

    /** @return array<ToPackages> */
    public function toInstall(): array;
}
