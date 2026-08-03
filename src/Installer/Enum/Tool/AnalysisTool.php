<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Tool;

use Composer\InstalledVersions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\SortedValues;

enum AnalysisTool: string implements ToPackages
{
    use SortedValues;

    case CsFixer = 'PHP CS Fixer';
    case ParallelLint = 'PHP Parallel Lint';
    case Phpcs = 'PHP Code Sniffer + Slevomat';
    case PhpStan = 'PHPStan';
    case Rector = 'Rector';

    /** @return list<string> */
    public function toPackages(Framework $framework): array
    {
        return match ($this) {
            self::CsFixer => ['friendsofphp/php-cs-fixer'],
            self::Phpcs => ['squizlabs/php_codesniffer', 'slevomat/coding-standard'],
            self::PhpStan => $this->getPhpStanPackages($framework),
            self::Rector => $this->getRectorPackages($framework),
            self::ParallelLint => ['php-parallel-lint/php-parallel-lint'],
        };
    }

    /** @return list<string> */
    private function getPhpStanPackages(Framework $framework): array
    {
        $packages = [
            'phpstan/phpstan',
            'phpstan/extension-installer',
            'phpstan/phpstan-deprecation-rules',
            'phpstan/phpstan-strict-rules',
            'tomasvotruba/cognitive-complexity',
            'shipmonk/phpstan-rules',
        ];

        if ($framework === Framework::Symfony) {
            $packages[] = 'phpstan/phpstan-symfony';
            $packages[] = 'phpstan/phpstan-doctrine';
        }

        if ($framework === Framework::Laravel) {
            $packages[] = 'larastan/larastan';
        }

        if (InstalledVersions::isInstalled('phpunit/phpunit')) {
            $packages[] = 'phpstan/phpstan-phpunit';
        }

        return $packages;
    }

    /** @return list<string> */
    private function getRectorPackages(Framework $framework): array
    {
        $packages = [
            'rector/rector',
        ];

        if ($framework === Framework::Laravel) {
            $packages[] = 'driftingly/rector-laravel';
        }

        return $packages;
    }
}
