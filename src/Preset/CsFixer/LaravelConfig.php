<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\CsFixer;

use Override;

readonly class LaravelConfig extends BaseConfig
{
    /** @return list<string> */
    #[Override]
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/app',
            $baseDir . '/config',
            $baseDir . '/database',
            $baseDir . '/routes',
            $baseDir . '/tests',
        ];
    }
}
