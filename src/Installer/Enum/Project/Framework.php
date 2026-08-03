<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Project;

use Composer\InstalledVersions;
use Kgabryel\CodeQuality\Installer\Enum\SortedValues;

enum Framework: string
{
    use SortedValues;

    case Laravel = 'Laravel';
    case None = 'Plain PHP / other framework';
    case Symfony = 'Symfony';

    public static function detect(): self
    {
        if (InstalledVersions::isInstalled('laravel/framework')) {
            return self::Laravel;
        }

        if (InstalledVersions::isInstalled('symfony/framework-bundle')) {
            return self::Symfony;
        }

        return self::None;
    }
}
