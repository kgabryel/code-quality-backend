<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Filesystem;

abstract readonly class AnalysisConfigGenerator extends ConfigGenerator
{
    protected AnalysisOptions $installConfig;

    public function __construct(Filesystem $filesystem, AnalysisOptions $installConfig)
    {
        parent::__construct($filesystem);
        $this->installConfig = $installConfig;
    }

    protected function getFrameworkPaths(): FrameworkPaths
    {
        return $this->installConfig->getFrameworkPaths();
    }
}
