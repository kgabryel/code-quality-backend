<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisConfigGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Preset\Rector\BaseConfig;
use Kgabryel\CodeQuality\Preset\Rector\LaravelConfig;
use Kgabryel\CodeQuality\Preset\Rector\SymfonyConfig;

readonly class RectorGenerator extends AnalysisConfigGenerator
{
    public function getCacheName(): string
    {
        return '.rector-cache/';
    }

    public function getFileName(): string
    {
        return 'rector.php';
    }

    protected function getPlaceholders(): array
    {
        return [
            '{{CACHE_PATH}}' => $this->getCachePath(),
            '{{PHP_VERSION}}' => $this->installConfig->phpVersion->toRectorPhpSet(),
            '{{PRESET_CLASS}}' => $this->getConfigClassName(),
        ];
    }

    private function getConfigClassName(): string
    {
        return match ($this->installConfig->framework) {
            Framework::None => BaseConfig::class,
            Framework::Symfony => SymfonyConfig::class,
            Framework::Laravel => LaravelConfig::class,
        };
    }
}
