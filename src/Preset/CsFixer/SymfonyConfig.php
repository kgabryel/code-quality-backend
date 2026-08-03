<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\CsFixer;

use Override;

readonly class SymfonyConfig extends BaseConfig
{
    /** @return list<string> */
    #[Override]
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/bin',
            $baseDir . '/src',
            $baseDir . '/tests',
            $baseDir . '/config',
            $baseDir . '/public',
        ];
    }
}
