<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Filesystem;

abstract readonly class TestConfigGenerator extends ConfigGenerator
{
    protected TestOptions $installConfig;

    public function __construct(Filesystem $filesystem, TestOptions $installConfig)
    {
        parent::__construct($filesystem);
        $this->installConfig = $installConfig;
    }

    protected function getFrameworkPaths(): FrameworkPaths
    {
        return $this->installConfig->getFrameworkPaths();
    }
}
