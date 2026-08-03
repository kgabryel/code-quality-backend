<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Script;

use Kgabryel\CodeQuality\Installer\Enum\Script\Script;

/** @template T of Script */
interface ScriptCommand
{
    /** @param T $script */
    public function get(Script $script): string;
}
