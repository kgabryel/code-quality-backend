<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Dto;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ToPackages;

readonly class AnalysisOptions implements Installable
{
    /**
     * @param array<AnalysisTool> $analysisTools
     * @param array<ComposerTool> $composerTools
     * @param null|ScriptsOptions<AnalysisScript> $scriptsConfig
     */
    public function __construct(
        public PhpVersion $phpVersion,
        public Framework $framework,
        public array $analysisTools,
        public array $composerTools,
        public bool $installDependencies,
        public ?ScriptsOptions $scriptsConfig,
    ) {}

    public function getFrameworkPaths(): FrameworkPaths
    {
        return new FrameworkPaths($this->framework);
    }

    /** @return array<ToPackages> */
    public function toInstall(): array
    {
        return [...$this->analysisTools, ...$this->composerTools];
    }
}
