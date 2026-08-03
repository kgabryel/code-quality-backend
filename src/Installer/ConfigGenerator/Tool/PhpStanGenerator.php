<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisConfigGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

readonly class PhpStanGenerator extends AnalysisConfigGenerator
{
    public function getCacheName(): string
    {
        return '.phpstan-cache/';
    }

    public function getFileName(): string
    {
        return 'phpstan.dist.neon';
    }

    protected function getPlaceholders(): array
    {
        return [
            '{{CACHE_FILE}}' => $this->getCachePath(),
            '{{FRAMEWORK}}' => $this->installConfig->getFrameworkPaths()->getTemplateFileName(),
            '{{PACKAGE_NAME}}' => $this->installConfig->getFrameworkPaths()->getName(),
            '{{PATHS}}' => $this->renderNeonPaths($this->getPaths()),
        ];
    }

    /** @return list<string> */
    private function getPaths(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Symfony, Framework::None => ['src', 'tests'],
            Framework::Laravel => ['app', 'tests'],
        };
    }

    /** @param array<string> $paths */
    private function renderNeonPaths(array $paths): string
    {
        return implode(
            "\n",
            array_map(
                static fn (string $path): string => '        - ' . $path,
                $paths
            )
        );
    }
}
