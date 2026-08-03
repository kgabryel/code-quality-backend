<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Tool;

use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

interface ToPackages
{
    /** @return array<string> */
    public function toPackages(Framework $framework): array;
}
