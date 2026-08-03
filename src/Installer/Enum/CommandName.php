<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum;

enum CommandName: string
{
    case Analysis = 'analysis';
    case Test = 'test';
}
