<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisConfigGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Override;

readonly class PhpCsGenerator extends AnalysisConfigGenerator
{
    public function getCacheName(): string
    {
        return '.phpcs-cache';
    }

    #[Override]
    public function getCachePath(): string
    {
        return $this->getCacheName();
    }

    public function getFileName(): string
    {
        return 'phpcs.xml.dist';
    }

    protected function getPlaceholders(): array
    {
        return [
            '{{CACHE_FILE}}' => $this->getCachePath(),
            '{{EXCLUDES}}' => $this->renderPhpcsExcludes($this->getExcludes()),
            '{{FRAMEWORK}}' => $this->installConfig->getFrameworkPaths()->getTemplateFileName(),
            '{{PACKAGE_NAME}}' => $this->installConfig->getFrameworkPaths()->getName(),
            '{{PATHS}}' => $this->renderPhpcsFiles($this->getFiles()),
        ];
    }

    /** @return list<string> */
    private function getExcludes(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Laravel => ['bootstrap/cache/*'],
            Framework::Symfony, Framework::None => [],
        };
    }

    /** @return list<string> */
    private function getFiles(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Symfony => [
                'bin/',
                'config/',
                'public/',
                'src/',
                'tests/',
            ],
            Framework::Laravel => [
                'app/',
                'bootstrap/',
                'config/',
                'database/',
                'routes/',
                'tests/',
            ],
            Framework::None => [
                'src/',
                'tests/',
            ]
        };
    }

    /** @param list<string> $excludes */
    private function renderPhpcsExcludes(array $excludes): string
    {
        return implode("\n", array_map(
            static fn (string $pattern): string => sprintf('    <exclude-pattern>%s</exclude-pattern>', $pattern),
            $excludes
        ));
    }

    /** @param list<string> $paths */
    private function renderPhpcsFiles(array $paths): string
    {
        return implode("\n", array_map(
            static fn (string $path): string => sprintf('    <file>%s</file>', $path),
            $paths
        ));
    }
}
