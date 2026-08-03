<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

readonly class TestableConfigGenerator extends ConfigGenerator
{
    public function getCacheName(): string
    {
        return '';
    }

    public function getFileName(): string
    {
        return '';
    }

    protected function getFrameworkPaths(): FrameworkPaths
    {
        return new FrameworkPaths(Framework::Symfony);
    }

    protected function getPlaceholders(): array
    {
        return [];
    }
}
