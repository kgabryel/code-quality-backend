<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Tool;

use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\SortedValues;

enum ComposerTool: string implements ToPackages
{
    use SortedValues;

    case Normalize = 'Composer Normalize';
    case RequireChecker = 'Composer Require Checker (missing deps)';
    case Unused = 'Composer Unused (unused deps)';

    /** @return list<string> */
    public function toPackages(Framework $framework): array
    {
        return match ($this) {
            self::RequireChecker => ['maglnet/composer-require-checker'],
            self::Unused => ['icanhazstring/composer-unused'],
            self::Normalize => ['ergebnis/composer-normalize'],
        };
    }
}
