<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum;

trait SortedValues
{
    /** @return list<string> */
    public static function values(): array
    {
        $values = array_column(self::cases(), 'value');
        sort($values);

        return $values;
    }
}
