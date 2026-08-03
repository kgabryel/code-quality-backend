<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Tool;

use Composer\InstalledVersions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

enum TestTool: string implements ToPackages
{
    case Infection = 'Infection';
    case PhpUnit = 'PhpUnit';

    /** @return list<string> */
    public function toPackages(Framework $framework): array
    {
        return match ($this) {
            self::PhpUnit => $this->getPhpUnitPackages(),
            self::Infection => ['infection/infection'],
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return [
            self::PhpUnit->value,
            self::Infection->value,
        ];
    }

    /** @return list<string> */
    private function getPhpUnitPackages(): array
    {
        $packages = [
            'phpunit/phpunit',
        ];

        if (InstalledVersions::isInstalled('phpstan/phpstan')) {
            $packages[] = 'phpstan/phpstan-phpunit';
        }

        return $packages;
    }
}
