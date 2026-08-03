<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Project;

use Kgabryel\CodeQuality\Installer\Enum\SortedValues;

enum PhpVersion: string
{
    use SortedValues;

    case PHP_84 = '8.4';
    case PHP_85 = '8.5';
    case PHP_86 = '8.6';

    public static function detect(): self
    {
        $detected = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

        return self::tryFrom($detected) ?? self::PHP_86;
    }

    public function toRectorPhpSet(): string
    {
        return match ($this) {
            self::PHP_84 => 'php84',
            self::PHP_85 => 'php85',
            self::PHP_86 => 'php86',
        };
    }
}
