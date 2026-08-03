<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\Rector;

use Override;
use RectorLaravel\Set\LaravelSetList;

readonly class LaravelConfig extends BaseConfig
{
    /** @return array<string, bool> */
    #[Override]
    public static function getComposerBased(): array
    {
        return [
            ...parent::getComposerBased(),
            'laravel' => true,
        ];
    }

    /** @return list<string> */
    #[Override]
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/app',
            $baseDir . '/bootstrap/app.php',
            $baseDir . '/config',
            $baseDir . '/database',
            $baseDir . '/routes',
            $baseDir . '/tests',
        ];
    }

    /** @return list<string> */
    #[Override]
    public static function getSets(): array
    {
        return [
            ...parent::getSets(),
            LaravelSetList::LARAVEL_CODE_QUALITY,
            LaravelSetList::LARAVEL_COLLECTION,
        ];
    }
}
