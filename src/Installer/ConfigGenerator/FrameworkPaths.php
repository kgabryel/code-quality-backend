<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Composer\InstalledVersions;
use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

readonly class FrameworkPaths
{
    public function __construct(private Framework $framework) {}

    public function getCacheDir(): string
    {
        return match ($this->framework) {
            Framework::Symfony => 'var/',
            Framework::Laravel, Framework::None => '',
        };
    }

    public function getName(): string
    {
        return 'kgabryel/code-quality';
    }

    public function getRoot(): string
    {
        $path = InstalledVersions::getInstallPath($this->getName());

        if ($path === null) {
            throw CommandError::packageRootUnavailable();
        }

        $realPath = realpath($path);

        return $realPath === false ? $path : $realPath;
    }

    public function getTemplate(string $file): string
    {
        return sprintf('%s/resources/templates/%s.template', $this->getRoot(), $file);
    }

    public function getTemplateFileName(): string
    {
        return match ($this->framework) {
            Framework::Symfony => 'symfony',
            Framework::Laravel => 'laravel',
            Framework::None => 'base',
        };
    }

    public function isAlreadyIgnored(string $path): bool
    {
        return array_any(
            $this->getIgnoredPrefixes(),
            static fn (string $prefix): bool => str_starts_with($path, $prefix)
        );
    }

    /** @return list<string> */
    private function getIgnoredPrefixes(): array
    {
        return match ($this->framework) {
            Framework::Symfony => ['var/'],
            Framework::Laravel => ['bootstrap/cache/', 'storage/framework/cache/'],
            Framework::None => [],
        };
    }
}
