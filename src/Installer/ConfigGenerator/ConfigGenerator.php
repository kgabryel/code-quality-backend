<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Filesystem;

abstract readonly class ConfigGenerator
{
    public function __construct(protected Filesystem $filesystem) {}

    public function generate(): string
    {
        $path = $this->getFrameworkPaths()->getTemplate($this->getFileName());
        $template = $this->filesystem->readFile($path);

        if ($template === false) {
            throw CommandError::templateUnreadable($path);
        }

        $replacements = $this->getPlaceholders();

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template,
        );
    }

    abstract public function getCacheName(): string;

    public function getCachePath(): string
    {
        $frameworkDir = rtrim($this->getFrameworkPaths()->getCacheDir(), '/');
        $cacheName = ltrim($this->getCacheName(), '/');

        return $frameworkDir === '' ? $cacheName : sprintf('%s/%s', $frameworkDir, $cacheName);
    }

    abstract public function getFileName(): string;

    abstract protected function getFrameworkPaths(): FrameworkPaths;

    /** @return array<string, string> */
    abstract protected function getPlaceholders(): array;
}
