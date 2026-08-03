<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Project;

enum CoverageDriver
{
    case None;
    case Pcov;
    case Xdebug;

    public static function detect(): self
    {
        if (extension_loaded('pcov') && filter_var(ini_get('pcov.enabled'), FILTER_VALIDATE_BOOL)) {
            return self::Pcov;
        }

        return extension_loaded('xdebug') ? self::Xdebug : self::None;
    }
}
