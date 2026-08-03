<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Helper;

class Path
{
    public static function getTemplate(string $filename): string
    {
        return sprintf('%s/resources/templates/%s', self::getRoot(), $filename);
    }

    private static function getRoot(): string
    {
        return dirname(__DIR__, 2);
    }
}
