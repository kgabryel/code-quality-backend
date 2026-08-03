<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\Rector;

use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\SetList;

readonly class BaseConfig
{
    /** @return array<string, bool> */
    public static function getAttributesSets(): array
    {
        return [
            'phpunit' => true,
        ];
    }

    /** @return array<string, bool> */
    public static function getComposerBased(): array
    {
        return [
            'phpunit' => true,
        ];
    }

    /** @return list<string> */
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/src',
            $baseDir . '/tests',
        ];
    }

    /** @return list<string> */
    public static function getSets(): array
    {
        return [
            SetList::CODE_QUALITY,
            SetList::DEAD_CODE,
            SetList::TYPE_DECLARATION,
            SetList::EARLY_RETURN,
            SetList::PRIVATIZATION,
            SetList::CODING_STYLE,
        ];
    }

    /** @return list<string> */
    public static function getsSkip(): array
    {
        return [
            ClassPropertyAssignToConstructorPromotionRector::class,
        ];
    }
}
