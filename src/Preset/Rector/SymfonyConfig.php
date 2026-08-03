<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\Rector;

use Override;
use Rector\Doctrine\Set\DoctrineSetList;

readonly class SymfonyConfig extends BaseConfig
{
    /** @return array<string, bool> */
    #[Override]
    public static function getAttributesSets(): array
    {
        return [
            ...parent::getAttributesSets(),
            'doctrine' => true,
            'symfony' => true,
        ];
    }

    /** @return array<string, bool> */
    #[Override]
    public static function getComposerBased(): array
    {
        return [
            ...parent::getComposerBased(),
            'doctrine' => true,
            'symfony' => true,
            'twig' => true,
        ];
    }

    /** @return list<string> */
    #[Override]
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/config',
            $baseDir . '/public',
            $baseDir . '/src',
            $baseDir . '/tests',
        ];
    }

    /** @return list<string> */
    #[Override]
    public static function getSets(): array
    {
        return [
            ...parent::getSets(),
            DoctrineSetList::DOCTRINE_CODE_QUALITY,
        ];
    }
}
