<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Dto;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

readonly class TestOptions implements Installable
{
    /**
     * @param array<TestTool> $tools
     * @param null|ScriptsOptions<TestScript> $scriptsConfig
     */
    public function __construct(
        public Framework $framework,
        public array $tools,
        public bool $installDependencies,
        public ?ScriptsOptions $scriptsConfig,
    ) {}

    public function getFrameworkPaths(): FrameworkPaths
    {
        return new FrameworkPaths($this->framework);
    }

    public function toInstall(): array
    {
        return $this->tools;
    }
}
